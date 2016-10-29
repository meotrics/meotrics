import * as mongodb from 'mongodb';
import * as config from 'config';
import * as express from 'express';
import * as WS from './module/ws';
import * as CrudApi from './module/crudapi';
import * as referer from './module/referer';
import bodyParser = require('body-parser');
var converter = require('./utils/fakeidmanager.js');
var appException = require('./module/appException.js');
import * as http  from 'http';

var HttpApi = require('./module/httpapi.js').HttpApi;

function buildconnstr(): string {
	var host = config.get("mongod.host") || "127.0.0.1";
	var port = config.get("mongod.port") || 27017;
	var database = config.get("mongod.database") || "test";
	return "mongodb://" + host + ":" + port + "/" + database;
}

//Using connection pool. Initialize mongodb once
var option: mongodb.MongoClientOptions = {};
option.server = {};
option.server.poolSize = 10;

function ensureIndex(db: mongodb.Db, prefix: string, callback: () => void) {
	console.log('indexing ...');
		db.collection(prefix + "actiontype").createIndex({ _appid: 1 },{background:true}, function () {
		db.collection(prefix + "actiontype").createIndex({ _appid: 1, _id: 1 },{background:true}, function () {
			db.collection(prefix + "dashboard").createIndex({ appid: 1, starttime: 1, endtime: 1 },{background:true}, function () {
				db.collection(prefix + "ip").createIndex({ ip: 1 },{background:true}, function () {
					db.collection(prefix + "mapping").createIndex({ anomtid: 1 },{background:true}, function () {
						db.collection(prefix + "mapping").createIndex({ idemtid: 1 },{background:true}, function () {
							db.collection(prefix + "mapping").createIndex({ idemtid: 1 },{background:true}, function () {
								db.collection(prefix + "segment").createIndex({ _appid: 1, _id: 1 },{background:true}, function () {
									db.collection(prefix + "segment").createIndex({ _appid: 1 },{background:true}, function () {
										db.collection(prefix + "trend").createIndex({ _appid: 1, _id: 1 },{background:true}, function () {
											db.collection(prefix + "trend").createIndex({ _appid: 1 },{background:true}, function () {
													db.collection(prefix + "valuedomain").createIndex({ appid: 1, typeid: 1, field: 1 },{background:true}, function () {
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

mongodb.MongoClient.connect(buildconnstr(), option, function (err: mongodb.MongoError, db: mongodb.Db) {
	if (err) throw err;
	var prefix: string = config.get<string>("mongod.prefix") || "meotrics_";
	ensureIndex(db, prefix, function () {
		var ref = new referer.RefererType();
		//set up new express application
		var app = express();
		appException(app);
		app.use(bodyParser.json()); // parse application/json
		converter = new converter.IdManager();
		var crudapi = new CrudApi.CrudApi(db, converter, prefix, ref, config.get("dashboard.delay"));
		// crudapi.route(app); //bind route
        //
		// //run the backend bashboard
		// var crudport = config.get("port") || 2108;
		// app.listen(crudport, function () {
		// 	console.log('Meotrics CORE API   | OK |    ' + crudport);
		// });

		var httpport = config.get<number>('apiserver.port') || 1711;
		var httpapi = new HttpApi(db, converter, prefix, config.get('apiserver.codepath'), ref, crudapi.valuemgr);
		var server = http.createServer(function (req: http.ServerRequest, res: http.ServerResponse) {
			httpapi.route(req, res);
		});

		server.listen(httpport, function () {
			console.log("HTTP API SERVER     | OK |    " + httpport);
		});

		// let wsport = config.get<number>('websocket.port') || 2910;
		let wsport = 2910;
		// let keypath = config.get<string>('websocket.key');
		// let certpath = config.get<string>('websocket.cert');
		var ws = new WS.WS(wsport);
		// bind change event
		var countRq = {};
		var delayRq = {};
		setInterval(function(){
			for(var key in countRq){
				countRq[key] = 0;
			};
		},1000);
		setInterval(function(){
			for(var key in delayRq){
				delayRq[key] = false;
			};
		},10800000);
		setInterval(function(){
			for(var key in delayRq){
				if(delayRq[key]){
					ws.change(key,"type.pageview");
				}
			};
		},1000);
		httpapi.onchange = function (appid, code) {
			if(delayRq[appid] == undefined) {
				delayRq[appid] = false;
				countRq[appid] = 0;
			}else if(delayRq[appid] == false){
				countRq[appid]++;
				if(countRq[appid] >= 10){
					delayRq[appid] = true;
					countRq[appid] = 0;
				}
				ws.change(appid, code);
			}

		};
		ws.run();
	});
});
