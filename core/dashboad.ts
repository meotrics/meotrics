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
		crudapi.route(app); //bind route

		//run the backend bashboard
		var crudport = config.get("port") || 2108;
		app.listen(crudport, function () {
			console.log('Meotrics CORE API   | OK |    ' + crudport);
		});

	});
});
