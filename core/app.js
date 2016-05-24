"use strict";
const mongodb = require('mongodb');
const config = require('config');
const express = require('express');
const WS = require('./module/ws');
const CrudApi = require('./module/crudapi');
const bodyParser = require('body-parser');
var converter = require('./utils/fakeidmanager.js');
var appException = require('./module/appException.js');
const http = require('http');
var HttpApi = require('./module/httpapi.js').HttpApi;
function buildconnstr() {
    var host = config.get("mongod.host") || "127.0.0.1";
    var port = config.get("mongod.port") || 27017;
    var database = config.get("mongod.database") || "test";
    return "mongodb://" + host + ":" + port + "/" + database;
}
//<<<<<<<<<<<<THE ENTRY POINT
//Using connection pool. Initialize mongodb once
var option = {};
option.server = {};
option.server.poolSize = 40;
mongodb.MongoClient.connect(buildconnstr(), option, function (err, db) {
    if (err)
        throw err;
    //set up new express application
    var app = express();
    appException(app);
    app.use(bodyParser.json()); // parse application/json
    converter = new converter.IdManager();
    var prefix = config.get("mongod.prefix") || "meotrics_";
    var crudapi = new CrudApi.CrudApi(db, converter, prefix, config.get("dashboard.delay"));
    crudapi.route(app); //bind route
    //run the backend bashboard
    var crudport = config.get("port") || 2108;
    app.listen(crudport, function () {
        console.log('Meotrics CORE API is listening at port ' + crudport);
    });
    var httpport = config.get('apiserver.port') || 1711;
    var httpapi = new HttpApi(config.get('apiserver.codepath'), crudapi.actionMgr, crudapi.valuemgr);
    var server = http.createServer(function (req, res) {
        httpapi.route(req, res);
    });
    server.listen(httpport, function () {
        console.log("HTTP API SERVER is running at port " + httpport);
    });
    var ws = new WS.WS('/ws', 80, server);
    ws.run();
});
//# sourceMappingURL=app.js.map