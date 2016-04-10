var SegmentExr = require('../module/segment.js').SegmentExr;


var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');

var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();


MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function(err, db) {
	if (err) throw err;
	var seg = new SegmentExr(db, mongodb, converter, async, config);


	var testJson2 = [{
		type: "purchase",
		f: "avg",
		field: "quantity",
		operator: ">",
		value: 3,
		conditions: []
	}];

	var segment = {
		_id: 123,
		condition: testJson2,
		_appid: process.argv[2]
	};

	console.time('mr');
	seg.runSegment(segment, function(out) {
		console.timeEnd('mr');
		console.log(JSON.stringify(out));
	});

});

//testsegment <appid>