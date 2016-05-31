import https = require('https');
import fs = require('fs');
import * as websocket from 'websocket';

var WebSocketServer = websocket.server;

private class Message {
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


	public  change(appid:string, code:string) {
		for(let client of me.boardcast_clients)
		if(client.closeDescription)
	}

	public run() {
		let me = this;

		this.httpserver.listen(me.port, function () {
			console.log(' Websocket server listing in port ' + me.port);
		});

		var wsServer = new WebSocketServer({
			httpServer: me.httpserver,
			autoAcceptConnections: false
		});

		function originIsAllowed(origin) {
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
				message = message.utf8Data;

				let mes:Message = JSON.parse(message);

				connection.appid = mes.appid;
				// client that listen on all event in app
				if (mes.code == undefined) {
					connection.meotricstype = 'boardcast';
					if (me.boardcast_clients[mes.appid] == undefined)
						me.boardcast_clients[mes.appid] = [];
					me.boardcast_clients[mes.appid].push(connection);
				} else {
					// client that only listen on specific event in app
					connection.meotricstype = 'topic';
					if (me.topic_clients[mes.appid] == undefined)
						me.topic_clients[mes.appid] = [];

					if (me.topic_clients[mes.appid][mes.code] === undefined)
						me.topic_clients[mes.appid][mes.code] = [];

					me.topic_clients[mes.appid][mes.code].push(connection);
				}
			});
			connection.on('close', function (reasonCode, description) {
				//remove connection from list
				if (connection.meotricstype == 'boardcast') {
					var index = me.boardcast_clients[connection.appid].indexOf(connection);
					me.boardcast_clients[connection.appid].splice(index, 1);

					if(me.boardcast_clients[connection.appid].length == 0)
						delete me.boardcast_clients[connection.appid];
				}
				else {
					var index = me.topic_clients[connection.appid].indexOf(connection);
					me.topic_clients[connection.appid].splice(index, 1);
					if(me.topic_clients[connection.appid].length == 0)
						delete me.topic_clients[connection.appid];
				}
			});
		});
	}
}