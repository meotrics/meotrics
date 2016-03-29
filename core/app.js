"use strict";

var config = require('config');
var mongodb = require('mongodb');
var MongoClient = mongodb.MongoClient;

function buildconnstr() {
	var host = config.get("mongod.host") || "127.0.0.1";
	var port = config.get("mongod.port") || 27017;
	var database = config.get("mongod.database") || "test";
	return "mongodb://" + host + ":" + port + "/" + database;
}

function mtthrow(err) {
	if (err) {
		console.log(err);
		setTimeout(function () {
			throw err;
		});
	}
}


function route(app, db, segmgr, prefix, mongodb, converter) {
	var bodyParser = require('body-parser');
	var async = require('async');
	var CRUD = require('./module/crud.js').CRUD;
	var TrendMgr = require('./module/trendmgr.js').TrendMgr;
	var ActionMgr = require('./module/actionmgr.js').ActionMgr;
	
	var actionMgr = new ActionMgr(db, mongodb, async, converter, prefix, mtthrow);
	var trendMrg = new TrendMgr(db, mongodb, async, converter, prefix, mtthrow, "trend");
	var typeCRUD = new CRUD(db, mongodb, async, converter, prefix, mtthrow, "actiontype");
	var trendCRUD = new CRUD(db, mongodb, async, converter, prefix, mtthrow, "trend");
	var segCRUD = new CRUD(db, mongodb, async, converter, prefix, mtthrow, "segment");
	var propCRUD = new CRUD(db, mongodb, async, converter, prefix, mtthrow, "userprop");
	var camCRUD = new CRUD(db, mongodb, async, converter, prefix, mtthrow, "campaign");
	var appmgr = new (require('./module/appmgr.js').AppMgr)(db, mongodb, async, converter, prefix, typeCRUD, segCRUD);

	// parse application/json
	app.use(bodyParser.json());

	//APP------------------------------------------------------------------------
	//set up an new app
	app.post('/app', function (req, res) {

	});
	// CRUD actiontype
	app.post('/actiontype/:appid', typeCRUD.create); // create an actiontype
	app.get('/actiontype/:appid', typeCRUD.list);	// get all actiontypes
	app.get('/actiontype/:appid/:id', typeCRUD.match); // get an actiontype
	app.put('/actiontype/:appid/:id', typeCRUD.update); // update an actiontype
	app.delete('/actiontype/:appid/:id', typeCRUD.delete); // delete an actiontype

	// CRUD trend
	app.post('/trend/:appid', trendCRUD.create);
	app.get('/trend/:appid', trendCRUD.list);
	app.get('/trend/:appid/:id', trendCRUD.match);
	app.put('/trend/:appid/:id', trendCRUD.update);
	app.delete('/trend/:appid/:id', trendCRUD.delete);
	// Query trend
	app.get('/trend/query/:appid/:id', trendMrg.query);

	// CRUD segment
	app.post('/segment/:appid', segCRUD.create);
	app.get('/segment/:appid', segCRUD.list);
	app.get('/segment/:appid/:id', segCRUD.match);
	app.put('/segment/:appid/:id', segCRUD.update);
	app.delete('/segment/:appid/:id', segCRUD.delete);

	// CRUD user
	app.post('/userprop/:appid', propCRUD.create);
	app.get('/userprop/:appid', propCRUD.list);
	app.get('/userprop/:appid/:id', propCRUD.match);
	app.put('/userprop/:appid/:id', propCRUD.update);
	app.delete('/userprop/:appid/:id', propCRUD.delete);

	// CRUD campaign
	app.post('/campaign/:appid', camCRUD.create);
	app.get('/campaign/:appid', camCRUD.list);
	app.get('/campaign/:appid/:id', camCRUD.match);
	app.put('/campaign/:appid/:id', camCRUD.update);
	app.delete('/campaign/:appid/:id', camCRUD.delete);

	// save a action
	app.post('/r/:appid', actionMgr.save);
	// identity an user
	app.post('/i/:appid', actionMgr.identify);
	// set up a new cookie
	app.get('/s/:appid', actionMgr.setup);

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


	//update or create a segment
	// app.post('segment', function (req, res) {
	// 	segmgr.create(req.params, function () {
	// 		res.sstatus(200).end();
	// 	});
	// })
}

//THE ENTRY POINT
//Using connection pool. Initialize mongodb once
MongoClient.connect(buildconnstr(), function (err, db) {
	if (err) throw err;

	// var SegmentMgr = require('segment').SegmentMgr;
	var express = require('express');

	//create component
	// var segmgr = new SegmentMgr(db);

	//set up new express application
	var app = express();
	// route(app, db, segmgr);
	var prefix = config.get("mongod.prefix") || "meotrics_";

	var converter = require('./utils/fakeidmanager.js');
	converter = new converter.IdManager();

	route(app, db, null, prefix, mongodb, converter);

	//run the app
	var port = config.get("port") || 2108;
	app.listen(port, function () {
		console.log('Meotrics core listening on port ' + port + '!');
	});

	// app.get('/segment', function (req, res) {
	// 	segmgr.create(req.params, function (responsetext) {
	// 		res.send(responsetext);
	// 		res.end()
	// 	});
	// })
});

