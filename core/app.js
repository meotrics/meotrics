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


function route(app, db, segmgr, prefix, mongodb, converter) {
	var bodyParser = require('body-parser');
	var async = require('async');
	var CRUD = require('./module/crud.js').CRUD;
	var TrendMgr = require('./module/trendmgr.js').TrendMgr;
	var ActionMgr = require('./module/actionmgr.js').ActionMgr;
	var PropMgr = require('./module/propmgr.js').PropMgr;
	var propmgr = new PropMgr();
	var actionMgr = new ActionMgr(db, mongodb, async, converter, prefix, "mapping");
	var trendMrg = new TrendMgr(db, mongodb, async, converter, prefix, "trend");
	var typeCRUD = new CRUD(db, mongodb, async, converter, prefix, "actiontype");
	var trendCRUD = new CRUD(db, mongodb, async, converter, prefix, "trend");
	var segCRUD = new CRUD(db, mongodb, async, converter, prefix, "segment");
	var propCRUD = new CRUD(db, mongodb, async, converter, prefix, "userprop");
	var camCRUD = new CRUD(db, mongodb, async, converter, prefix, "campaign");
	var appmgr = new (require('./module/appmgr.js').AppMgr)(db, mongodb, async, converter, prefix, typeCRUD, segCRUD);

	// parse application/json
	app.use(bodyParser.json());

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
	app.get('/trend/query/:appid/:id', trendMrg.query);

	// CRUD segment
	app.postEx('/segment/:appid', segCRUD.create);
	app.getEx('/segment/:appid', segCRUD.list);
	app.getEx('/segment/:appid/:id', segCRUD.match);
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
	var appException = require('./module/appException.js');
	//create component
	// var segmgr = new SegmentMgr(db);

	//set up new express application
	var app = express();
	appException(app);
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

