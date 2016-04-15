"use strict";

function route(app, com) {
	var propmgr = com.propmgr;
	var actionMgr = com.actionMgr;
	var trendMgr = com.trendMgr;
	var typeCRUD = com.typeCrud;
	var trendCRUD = com.trendCrud;
	var segCRUD = com.segCrud;
	var propCRUD = com.propCrud;
	var camCRUD = com.camCrud;
	var appmgr = com.appmgr;


	//APP------------------------------------------------------------------------
	//set up an new app
	app.post('/app', function (req, res) {

	});
	// CRUD actiontype
	app.postEx('/actiontype/:appid', typeCRUD.create); // create an actiontype
	app.getEx('/actiontype/:appid', typeCRUD.list);	// get all actiontypes
	app.getEx('/actiontype/:appid/:id', typeCRUD.match); // get an actiontype
	app.putEx('/actiontype/:appid/:id', typeCRUD.update); // update an actiontype
	app.deleteEx('/actiontype/:appid/:id', typeCRUD.delete); // delete an actiontype

	// CRUD trend
	app.postEx('/trend/:appid', trendCRUD.create);
	app.getEx('/trend/:appid', trendCRUD.list);
	app.getEx('/trend/:appid/:id', trendCRUD.match);
	app.putEx('/trend/:appid/:id', trendCRUD.update);
	app.deleteEx('/trend/:appid/:id', trendCRUD.delete);
	// Query trend
	app.get('/trend/query/:appid/:id', trendMgr.query);

	// CRUD segment
	app.postEx('/segment/:appid', function (req, res) {
		segCRUD.create(req, res, function (id) {
			com.segMgr.excuteSegment(id);
		});
	});

	app.getEx('/segment/:appid', segCRUD.list);
	app.getEx('/segment/:appid/:id', function (req, res) {
		segCRUD.match(req, res, function () {
			com.segMgr.excuteSegment(req.params.id);
		});
	});

	app.putEx('/segment/:appid/:id', segCRUD.update);
	app.deleteEx('/segment/:appid/:id', segCRUD.delete);

	// CRUD user
	app.postEx('/userprop/:appid', propCRUD.create);
	app.getEx('/userprop/:appid', propCRUD.list);
	app.getEx('/userprop/:appid/:id', propCRUD.match);
	app.putEx('/userprop/:appid/:id', propCRUD.update);
	app.deleteEx('/userprop/:appid/:id', propCRUD.delete);

	app.get('/prop/:appid', propmgr.list);

	// CRUD campaign
	app.postEx('/campaign/:appid', camCRUD.create);
	app.getEx('/campaign/:appid', camCRUD.list);
	app.getEx('/campaign/:appid/:id', camCRUD.match);
	app.putEx('/campaign/:appid/:id', camCRUD.update);
	app.deleteEx('/campaign/:appid/:id', camCRUD.delete);

	// save a action
	app.postEx('/r/:appid', actionMgr.save);
	// identity an user
	app.postEx('/i/:appid', actionMgr.identify);
	// set up a new cookie
	app.getEx('/s/:appid', actionMgr.setup);

	app.postEx('/f/:appid/:actionid', actionMgr.fix);

	app.get('/app/init/:appid', function (req, res) {
		appmgr.initApp(req.params.appid, function () {
			res.status(200).end();
		})
	});

	//check whether user has setup tracking code
	app.get('/api/status/:appid', function (req, res) {
		appmgr.isSetup(req.params.appid, function (ret) {
			res.send(ret);
			res.status(200).end();
		});
	});

	//update or
	app.get('segment/query/:appid/:id/:field1/:field2', function (req, res) {
		com.segMgr.querySegment(req.params.appid, req.params.id, req.params.field1, req.param.field2, function (results) {
			res.json(results);
		});
	});
}
function buildconnstr(config) {
	var host = config.get("mongod.host") || "127.0.0.1";
	var port = config.get("mongod.port") || 27017;
	var database = config.get("mongod.database") || "test";
	return "mongodb://" + host + ":" + port + "/" + database;
}

var mongodb = require('mongodb');
var config = require('config');

//<<<<<<<<<<<<THE ENTRY POINT

//Using connection pool. Initialize mongodb once
mongodb.MongoClient.connect(buildconnstr(config), function (err, db) {
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

	var TrendMgr = require('./module/trendmgr.js').TrendMgr;
	var ActionMgr = require('./module/actionmgr.js').ActionMgr;
	var PropMgr = require('./module/propmgr.js').PropMgr;
	var AppMgr = require('./module/appmgr.js').AppMgr;
	var SegMgr = require('./module/segment.js').SegmentExr;


	var async = require('async');
	var component = {};
	component.trendMgr = new TrendMgr(db, mongodb, async, converter, prefix, "trend");
	component.actionMgr = new ActionMgr(db, mongodb, async, converter, prefix, "mapping");
	component.propmgr = new PropMgr();
	component.typeCrud = new CRUD(db, mongodb, async, converter, prefix, "actiontype");
	component.trendCrud = new CRUD(db, mongodb, async, converter, prefix, "trend");
	component.segCrud = new CRUD(db, mongodb, async, converter, prefix, "segment");
	component.appmgr = new AppMgr(db, mongodb, async, converter, prefix, component.typeCrud, component.segCrud);
	component.propCrud = new CRUD(db, mongodb, async, converter, prefix, "userprop");
	component.camCrud = new CRUD(db, mongodb, async, converter, prefix, "campaign");
	component.segMgr = new SegMgr(db, mongodb, async, converter, prefix);

	//routing http
	route(app, component);

	//run the app
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
	var httpapi = new HttpApi(config.get('apiserver.codepath'), component.actionMgr, fs, ua, MD);
	var server = http.createServer(function (req, res) {
		var qs = require('querystring');
		var url = require('url');
		console.log('e');
		var url_parts = url.parse(req.url, true);
		if (req.method == 'POST') {
			var body = '';
			req.on('data', function (data) {
				body += data;
			});
			req.on('end', function () {
				req.params = qs.parse(body);
				console.log('d');
				handle(req, res, url_parts.pathname);
			});
		}
		else if (req.method == 'GET') {
			req.params = url_parts.query;
			handle(req, res, url_parts.pathname);
		}

		function handle(req, res, path) {
			console.log(path.split('/'));
			//split path
			var parts = path.split('/');
			if (parts[1] == 'api') {
				req.appid = parts[2];
				var action = parts[3];
				if (action == 'track') {
					httpapi.track(req, res);
				}
				else if (action = 'code') {
					httpapi.code(req, res);
				}
				else if (action == 'clear') {
					httpapi.clear(req, res);
				} else if (action = 'info') {
					httpapi.info(req, res);
				} else if (action = 'fix') {
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
	});

	server.listen(httpport, function () {
		console.log("HTTP API SERVER is running at port " + httpport);
	});

});



