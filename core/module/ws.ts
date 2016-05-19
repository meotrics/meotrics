

export class WS {
	public constructor(private url: string, private port: number, private http) {

	}
	private clients = {}
	public run() {
		let me = this;
		var WebSocketServer = require('websocket').server;

		var wsServer = new WebSocketServer({
			httpServer: this.http,
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
				console.log((new Date()) + ' Connection from origin ' + request.origin + ' rejected.');
				return;
			}

			var connection = request.accept('echo-protocol', request.origin);
			console.log((new Date()) + ' Connection accepted.');
			connection.on('message', function (message) {

				if (me.clients[message.code] === undefined)
					me.clients[message.code] = [];

				for (var client of me.clients[message.code]) {
					if (client.readyState === client.OPEN) {
						client.sendUTF(JSON.stringify(message));
					}
				});
			connection.on('close', function (reasonCode, description) {
				//TODO: remove all
			});
		});
	}
}