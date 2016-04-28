"use strict";
function route(app, com) {
	//APP------------------------------------------------------------------------
	//set up an new app
	app.post('/app', function (req, res) {

	});
	// CRUD actiontype
	app.postEx('/actiontype/:appid', com.typemgr.create); // create an actiontype
	app.getEx('/actiontype/:appid', com.typemgr.list);	// get all actiontypes
	app.getEx('/actiontype/:appid/:id', com.typemgr.match); // get an actiontype
	app.putEx('/actiontype/:appid/:id', com.typemgr.update); // update an actiontype
	app.deleteEx('/actiontype/:appid/:id', com.typemgr.delete); // delete an actiontype

	// CRUD trend
	app.postEx('/trend/:appid', com.trendCRUD.create);
	app.getEx('/trend/:appid', com.trendCRUD.list);
	app.getEx('/trend/:appid/:id', com.trendCRUD.match);
	app.putEx('/trend/:appid/:id', com.trendCRUD.update);
	app.deleteEx('/trend/:appid/:id', com.trendCRUD.delete);
	// Query trend

	app.get('/trend/query/:appid/:id/:segid?/:starttime?/:endtime?', com.trendMgr.query);

	// CRUD segment
	app.postEx('/segment/:appid', function (req, res) {
		com.segCRUD.create(req, res, function (id) {
			com.segMgr.excuteSegment(id);
		});
	});

	app.getEx('/segment/:appid', com.segCRUD.list);
	app.getEx('/segment/:appid/:id', function (req, res) {
		com.segCRUD.match(req, res, function () {
			com.segMgr.excuteSegment(req.params.id);
		});
	});

	app.putEx('/segment/:appid/:id', com.segCRUD.update);
	app.deleteEx('/segment/:appid/:id', com.segCRUD.delete);

	//update or
	app.get('/segment/query1/:appid/:id/:field1/', function (req, res) {
		com.segMgr.querySegment(req.params.appid, req.params.id, req.params.field1, undefined, function (results) {
			res.json(results);
		});
	});

	//update or
	app.get('/segment/query2/:appid/:id/:field1/:field2', function (req, res) {
		com.segMgr.querySegment(req.params.appid, req.params.id, req.params.field1, req.params.field2, function (results) {
			res.json(results);
		});
	});

	// CRUD user
	app.postEx('/userprop/:appid', com.propCRUD.create);
	app.getEx('/userprop/:appid', com.propCRUD.list);
	app.getEx('/userprop/:appid/:id', com.propCRUD.match);
	app.putEx('/userprop/:appid/:id', com.propCRUD.update);
	app.deleteEx('/userprop/:appid/:id', com.propCRUD.delete);

	app.get('/prop/:appid', com.propmgr.list);

	// CRUD campaign
	app.postEx('/campaign/:appid', com.camCRUD.create);
	app.getEx('/campaign/:appid', com.camCRUD.list);
	app.getEx('/campaign/:appid/:id', com.camCRUD.match);
	app.putEx('/campaign/:appid/:id', com.camCRUD.update);
	app.deleteEx('/campaign/:appid/:id', com.camCRUD.delete);

	// save a action
	app.postEx('/r/:appid', com.actionMgr.save);
	// identity an user
	app.postEx('/i/:appid', com.actionMgr.identify);
	// set up a new cookie
	app.getEx('/s/:appid', com.actionMgr.setup);

	app.postEx('/f/:appid/:actionid', com.actionMgr.fix);

	app.get('/app/init/:appid', function (req, res) {
		com.appmgr.initApp(req.params.appid, function () {
			res.status(200).end();
		});
	});

	app.get('/dashboard/:appid', function(req, res){
		com.dashboard.getDashboard(req.params.appid,function(result){
			res.json(result);
		});
	});
	
	// count number of action in app
	app.getEx('/api/counter/:appid', function (req, res) {
		com.appmgr.countAction(req.params.appid, function (ret) {
			res.send(ret + "");
		});
	});

	//check whether user has setup tracking code
	app.get('/api/status/:appid', function (req, res) {
		com.appmgr.isSetup(req.params.appid, function (ret) {
			res.send(ret + "");
			res.status(200).end();
		});
	});
}

function buildconnstr(config) {
	var host = config.get("mongod.host") || "127.0.0.1";
	var port = config.get("mongod.port") || 27017;
	var database = config.get("mongod.database") || "test";
	return "mongodb://" + host + ":" + port + "/" + database;
}

function dataapiroot(httpapi, trycatch, req, res, qs, url) {
	trycatch(function () {
		var url_parts = url.parse(req.url, true);
		if (req.method === 'POST') {
			var body = '';
			req.on('data', function (data) {
				body += data;
			});
			req.on('end', function () {
				req.params = qs.parse(body);
				handle(req, res, url_parts.pathname);
			});
		}
		else if (req.method === 'GET') {
			req.params = url_parts.query;
			handle(req, res, url_parts.pathname);
		}

		function handle(req, res, path) {
			var parts = path.split('/');
			if (parts[1] === 'api') {
				res.statusCode = 200;
				req.appid = parts[2];
				var action = parts[3];
				if (action === 'track') httpapi.track(req, res);
				else if (action === 'code.js') httpapi.code(req, res);
				else if (action === 'clear') httpapi.clear(req, res);
				else if (action === 'info') httpapi.info(req, res);
				else if (action === 'x') {
					req.actionid = parts[4];
					httpapi.x(req, res);
				}
				else if (action === 'suggest') {
					req.typeid = parts[4];
					req.field = parts[5];
					req.qr = parts[6];
					httpapi.suggest(req, res);
				}
				else if (action === 'fix') {
					req.actionid = parts[4];
					httpapi.fix(req, res);
				} else {
					res.statusCode = 404;
					res.end('action must be one of [code, clear, ingo, fix, track]');
				}
			}
			else {
				res.statusCode = 404;
				res.end('path must be [api]');
			}
		}
	}, function (err) {
		res.statusCode = 500;
		res.end();
		console.log(err, err.stack);
	});
}

var mongodb = require('mongodb');
var config = require('config');

//<<<<<<<<<<<<THE ENTRY POINT

//Using connection pool. Initialize mongodb once
mongodb.MongoClient.connect(buildconnstr(config), {server: {auto_reconnect: true, poolSize: 40}}, function (err, db) {
	if (err) throw err;

	//set up new express application
	var express = require('express');
	var app = express();
	var appException = require('./module/appException.js');
	appException(app);
	var bodyParser = require('body-parser');
	// parse application/json
	app.use(bodyParser.json());

	//create component
	var CRUD = require('./module/crud.js').CRUD;
	var converter = require('./utils/fakeidmanager.js');
	converter = new converter.IdManager();
	var prefix = config.get("mongod.prefix") || "meotrics_";
	var trycatch = require('trycatch');
	var async = require('async');

	var TrendMgr = require('./module/trendmgr.js').TrendMgr;
	var ActionMgr = require('./module/actionmgr.js').ActionMgr;
	var PropMgr = require('./module/propmgr.js').PropMgr;
	var AppMgr = require('./module/appmgr.js').AppMgr;
	var SegMgr = require('./module/segment.js').SegmentExr;
	var TypeMgr = require('./module/typemgr.js').TypeMgr;
	var ValueMgr = require('./module/valuemgr.js').ValueMgr;
	var Dashboard = require('./module/dashboard').Dashboard;

	var component = {};
	component.dashboard = new Dashboard(db, mongodb, converter, prefix, config.get("dashborad.delay"));
	component.trendMgr = new TrendMgr(db, mongodb, async, converter, prefix, "trend");
	component.actionMgr = new ActionMgr(db, mongodb, async, converter, prefix, "mapping");
	component.propmgr = new PropMgr();
	component.typeCRUD = new CRUD(db, mongodb, async, converter, prefix, "actiontype");
	component.typemgr = new TypeMgr(db, mongodb, converter, async, prefix, component.typeCRUD, "actiontype");
	component.trendCRUD = new CRUD(db, mongodb, async, converter, prefix, "trend");
	component.segCRUD = new CRUD(db, mongodb, async, converter, prefix, "segment");
	component.appmgr = new AppMgr(db, converter, prefix, component.typeCRUD, component.segCRUD, component.trendCRUD);
	component.propCRUD = new CRUD(db, mongodb, async, converter, prefix, "userprop");
	component.camCRUD = new CRUD(db, mongodb, async, converter, prefix, "campaign");
	component.segMgr = new SegMgr(db, mongodb, async, converter, prefix);
	component.valuemgr = new ValueMgr(db, prefix);
	//routing http
	route(app, component);

	//run the backend bashboard
	var port = config.get("port") || 2108;
	app.listen(port, function () {
		console.log('Meotrics CORE API is listening at port ' + port);
	});

	var http = require('http');
	var ua = require('ua-parser');
	var MD = require('mobile-detect');
	var fs = require('fs');
	var httpport = config.get('apiserver.port') || 1711;

	var HttpApi = require('./module/httpapi.js').HttpApi;
	var httpapi = new HttpApi(config.get('apiserver.codepath'), component.actionMgr, fs, ua, MD, component.valuemgr);

	var qs = require('querystring');
	var url = require('url');

	var server = http.createServer(function (req, res) {
		dataapiroot(httpapi, trycatch, req, res, qs, url);
	});

	server.listen(httpport, function () {
		console.log("HTTP API SERVER is running at port " + httpport);
	});
});