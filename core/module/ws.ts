import https = require('https');
import fs = require('fs');
import * as websocket from 'websocket';

class Message {
	public  constructor(public appid:string, public code:string) {
	}
}

export class WS {
	private httpserver;
	private topic_clients = {};
	private boardcast_clients = {};

	public constructor(private port:number, keypath:string, certpath:string) {
		var option = {
			key: fs.readFileSync(keypath),
			cert: fs.readFileSync(certpath)
		};

		this.httpserver = https.createServer(option, function (req, res) {
			res.writeHead(404);
			res.end();
		});
	}

	public change(appid:string, code:string) {
		var me = this;
		//console.log('\n',appid, code, me.boardcast_clients[appid].length,'\n');
		if (me.boardcast_clients[appid] !== undefined)
			for (let client of me.boardcast_clients[appid])
				if (client.closeDescription == null)
					client.sendUTF(JSON.stringify({appid: appid, code: code}));
		if (me.topic_clients[appid] !== undefined && me.topic_clients[appid][code] !== undefined)
			for (let client of me.topic_clients[appid][code])
				if (client.closeDescription == null)
					client.sendUTF(JSON.stringify({appid: appid, code: code}));
	}

	public run() {
		var me = this;

		this.httpserver.listen(me.port, function () {
			console.log('Websocket server / OK  / ' + me.port);
		});

		var wsServer = new websocket.server({
			httpServer: me.httpserver,
			autoAcceptConnections: false
		});

		function originIsAllowed(origin:string) {
			// put logic here to detect whether the specified origin is allowed.
			return true;
		}

		wsServer.on('request', function (request) {
			if (!originIsAllowed(request.origin)) {
				// Make sure we only accept requests from an allowed origin
				request.reject();
				console.log((new Date()) + 'Connection from origin ' + request.origin + ' rejected.');
				return;
			}

			var connection = request.accept('mtdashboard', request.origin);
			connection.on('message', function (message) {
				if (message.utf8Data == undefined) // reject if not utf8 encode
					return;

				let mes:Message = JSON.parse(message.utf8Data.toString());
				connection['appid'] = mes.appid;
				// client that listen on all event in app
				if (mes.code == undefined) {
					connection['meotricstype'] = 'boardcast';
					if (me.boardcast_clients[mes.appid] == undefined)
						me.boardcast_clients[mes.appid] = [];
					if (me.boardcast_clients[mes.appid].indexOf(connection) == -1)
						me.boardcast_clients[mes.appid].push(connection);
				} else {
					// client that only listen on specific event in app
					connection['meotricstype'] = 'topic';
					if (me.topic_clients[mes.appid] == undefined)
						me.topic_clients[mes.appid] = [];

					if (me.topic_clients[mes.appid][mes.code] === undefined)
						me.topic_clients[mes.appid][mes.code] = [];

					if (me.topic_clients[mes.appid][mes.code].indexOf(connection) == -1)
						me.topic_clients[mes.appid][mes.code].push(connection);
				}
			});
			connection.on('close', function (reasonCode, description) {
				//remove connection from list
				if (connection['meotricstype'] == 'boardcast' && undefined !== me.boardcast_clients[connection['appid']]) {
					var index = me.boardcast_clients[connection['appid']].indexOf(connection);
					me.boardcast_clients[connection['appid']].splice(index, 1);

					if (me.boardcast_clients[connection['appid']].length == 0)
						delete me.boardcast_clients[connection['appid']];
				}
				else if (connection['meotricstype'] == 'topic' && me.topic_clients[connection['appid']] !== undefined) {
					var index = me.topic_clients[connection['appid']].indexOf(connection);
					me.topic_clients[connection['appid']].splice(index, 1);
					if (me.topic_clients[connection['appid']].length == 0)
						delete me.topic_clients[connection['appid']];
				}
			});
		});
	}
}