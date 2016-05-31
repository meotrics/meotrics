﻿import https = require('https');
import fs = require('fs');

export class WS {
	private httpserver;
	public constructor(private port: number, keypath:string, certpath:string) {
		
		var option = {
			key: fs.readFileSync(keypath),
			cert: fs.readFileSync(certpath)
		};

		this.httpserver = https.createServer( option,  function(req, res){
			console.log('receive request for ' + req.url);
			res.writeHead(404);
			res.end();
		});

	}
	private clients = {};
	public run() {
		let me = this;
		var WebSocketServer = require('websocket').server;

		this.httpserver.listen(me.port, function(){
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

		wsServer.on('connect', function(request) {
			console.log('Hello!');
		});
		wsServer.on('request', function (request) {
			if (!originIsAllowed(request.origin)) {
				// Make sure we only accept requests from an allowed origin
				request.reject();
				console.log((new Date()) + ' Connection from origin ' + request.origin + ' rejected.');
				return;
			}


			var connection = request.accept('mtdashboard', request.origin);
			console.log( ' Connection accepted.');
			connection.on('message', function (message) {

				if(message.utf8Data == undefined) // reject if not utf8 encode
					return;
				message = message.utf8Data;
				
				if (me.clients[message.code] === undefined)
					me.clients[message.code] = [];

				for (var client of me.clients[message.code]) {
					if (client.readyState === client.OPEN) {
						client.sendUTF(JSON.stringify(message));
					}
				}
			});
			connection.on('close', function (reasonCode, description) {
				//TODO: remove all
			});
		});
	}
}