import * as mongodb from 'mongodb';
import * as config from 'config';
import * as express from 'express';
import * as WS from './module/ws';
import * as CrudApi from './module/crudapi';

import bodyParser = require('body-parser');
var converter = require('./utils/fakeidmanager.js');
var appException = require('./module/appException.js');
import * as http  from 'http';

var HttpApi = require('./module/httpapi.js').HttpApi;

function buildconnstr():string {
	var host = config.get("mongod.host") || "127.0.0.1";
	var port = config.get("mongod.port") || 27017;
	var database = config.get("mongod.database") || "test";
	return "mongodb://" + host + ":" + port + "/" + database;
}

//Using connection pool. Initialize mongodb once
var option:mongodb.MongoClientOptions = {};
option.server = {};
option.server.poolSize = 40;

mongodb.MongoClient.connect(buildconnstr(), option, function (err:mongodb.MongoError, db:mongodb.Db) {
	if (err) throw err;

	//set up new express application
	var app = express();
	appException(app);
	app.use(bodyParser.json()); // parse application/json
	converter = new converter.IdManager();
	var prefix:string = config.get<string>("mongod.prefix") || "meotrics_";
	var crudapi = new CrudApi.CrudApi(db,converter,prefix, config.get("dashboard.delay"));
	crudapi.route(app); //bind route
	
	//run the backend bashboard
	var crudport = config.get("port") || 2108;
	app.listen(crudport, function () {
		console.log('Meotrics CORE API is listening at port ' + crudport);
	});

	var httpport = config.get<number>('apiserver.port') || 1711;
	var httpapi = new HttpApi(db,converter, prefix,config.get('apiserver.codepath'), crudapi.valuemgr);
	var server = http.createServer(function (req:http.ServerRequest, res:http.ServerResponse) {
		httpapi.route(req, res);
	});

	server.listen(httpport, function () {
		console.log("HTTP API SERVER is running at port " + httpport);
	});

	let wsport = config.get<number>('websocket.port') || 2910;
	let keypath = config.get<string>('websocket.key');
	let certpath = config.get<string>('websocket.cert');
	var ws = new WS.WS( wsport, keypath, certpath );

	// bind change event
	httpapi.onchange = ws.change;
	ws.run();
});