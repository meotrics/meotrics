var config = require('config');
var MongoClient = require('mongodb').MongoClient;

function buildconnstr()
{
	var host = config.get("mongod.host");
	var port= config.get("mongod.port");
	var database= config.get("mongod.database");
	return "mongodb://" + host + ":" + port + "/" + database;
}

function route(app, db, segmgr) {
	app.get('/', function (req, res) {
		res.send('Hello World!');
	});

	//APP------------------------------------------------------------------------
	//set up an new app
	app.post('/app', function (req, res) {

	});

	//ACTION TYPE-----------------------------------------------------------------
	//update or create an action type
	app.post('/actiontype', function (req, res) {
		var sampleActionType = {
			name: "purchase",

		}
	});

	//get actiontype by id or list
	app.get('/actiontype', function (req, res) {
		var sampleActionType = {
			name: "purchase",

		}
	});

	//delete an actiontype
	app.delete('/actiontype', function (req, res) {

	});


	//TREND-------------------------------------------------------------------------

	//get a trend
	app.get('/trend', function (req, res) {

	});




	//CLIENT------------------------------------------------------------------------
	//record and event
	app.post('/r', function recordAnEvent(req, res) {
		//NOTE: use array instead of json for faster network throughput
	});

	//identify an user
	app.post('/i', function recordAnEvent(req, res) {
		//NOTE: use array instead of json for faster network throughput

	});

	//set up new cookie for new visitor
	app.post('/s', function recordAnEvent(req, res) {
		//NOTE: use array instead of json for faster network throughput
	});


	//SEGMENTATION--------------------------------------------------------------

	app.get('/segment', function (req, res) {
		segmgr.create(req.params, function(responsetext){
			res.send(responsetext);
			res.end()
		});
	})
}

//THE ENTRY POINT
//Using connection pool. Initialize mongodb once
MongoClient.connect(buildconnstr(), function (err, db) {
	if (err) throw err;
	
	var SegmentMgr = require('./segmentmgr.js').SegmentMgr;
	var express = require('express');
	
	//create component
	var segmgr = new SegmentMgr(db);
	
	//set up new express application
	var app = express();
	route(app, db,segmgr);
	
	//run the app
	var port = config.get("port") || 2108;
	app.listen(port, function () {
		console.log('Meotrics core listening on port ' + port + '!');
	});
});