"use strict";
const http = require('http');
class WS {
    constructor(port) {
        this.port = port;
        this.clients = {};
        this.httpserver = http.createServer(function (req, res) {
            console.log((new Date()) + ' receive request for ' + req.url);
            res.writeHead(404);
            res.end();
        });
    }
    run() {
        let me = this;
        var WebSocketServer = require('websocket').server;
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
                console.log((new Date()) + ' Connection from origin ' + request.origin + ' rejected.');
                return;
            }
            var connection = request.accept('mtdashboard', request.origin);
            console.log(' Connection accepted.');
            connection.on('message', function (message) {
                if (message.utf8Data == undefined)
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
exports.WS = WS;
//# sourceMappingURL=ws.js.map