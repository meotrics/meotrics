"use strict";
const mongodb = require('mongodb');
const config = require('config');
const express = require('express');
const WS = require('./module/ws');
const CrudApi = require('./module/crudapi');
const referer = require('./module/referer');
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
//Using connection pool. Initialize mongodb once
var option = {};
option.server = {};
option.server.poolSize = 40;
function ensureIndex(db, prefix, callback) {
    console.log('indexing ...');
    db.collection(prefix + "actiontype").createIndex({ _appid: 1 }, function () {
        db.collection(prefix + "actiontype").createIndex({ _appid: 1, _id: 1 }, function () {
            db.collection(prefix + "dashboard").createIndex({ appid: 1, starttime: 1, endtime: 1 }, function () {
                db.collection(prefix + "ip").createIndex({ ip: 1 }, function () {
                    db.collection(prefix + "mapping").createIndex({ anomtid: 1 }, function () {
                        db.collection(prefix + "mapping").createIndex({ idemtid: 1 }, function () {
                            db.collection(prefix + "mapping").createIndex({ idemtid: 1 }, function () {
                                db.collection(prefix + "segment").createIndex({ _appid: 1, _id: 1 }, function () {
                                    db.collection(prefix + "segment").createIndex({ _appid: 1 }, function () {
                                        db.collection(prefix + "trend").createIndex({ _appid: 1, _id: 1 }, function () {
                                            db.collection(prefix + "trend").createIndex({ _appid: 1 }, function () {
                                                db.collection(prefix + "valuedomain").createIndex({ appid: 1, typeid: 1, field: 1 }, function () {
                                                    callback();
                                                });
                                            });
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });
    });
}
mongodb.MongoClient.connect(buildconnstr(), option, function (err, db) {
    if (err)
        throw err;
    var prefix = config.get("mongod.prefix") || "meotrics_";
    ensureIndex(db, prefix, function () {
        var ref = new referer.RefererType();
        //set up new express application
        var app = express();
        appException(app);
        app.use(bodyParser.json()); // parse application/json
        converter = new converter.IdManager();
        var crudapi = new CrudApi.CrudApi(db, converter, prefix, ref, config.get("dashboard.delay"));
        crudapi.route(app); //bind route
        //run the backend bashboard
        var crudport = config.get("port") || 2108;
        app.listen(crudport, function () {
            console.log('Meotrics CORE API   | OK |    ' + crudport);
        });
        var httpport = config.get('apiserver.port') || 1711;
        var httpapi = new HttpApi(db, converter, prefix, config.get('apiserver.codepath'), ref, crudapi.valuemgr);
        var server = http.createServer(function (req, res) {
            httpapi.route(req, res);
        });
        server.listen(httpport, function () {
            console.log("HTTP API SERVER     | OK |    " + httpport);
        });
        let wsport = config.get('websocket.port') || 2910;
        let keypath = config.get('websocket.key');
        let certpath = config.get('websocket.cert');
        var ws = new WS.WS(wsport);
        // bind change event
        httpapi.onchange = function (appid, code) {
            ws.change(appid, code);
        };
        ws.run();
    });
});
//# sourceMappingURL=app.js.map