return;
var SegmentExr = require('../module/segment.js').SegmentExr;
var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');

var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();


MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function (err, db) {
	if (err) throw err;
	var seg = new SegmentExr(db, mongodb, async, converter, config.get('mongod.prefix'));

	var testJson2 = [{
		type: "purchase",
		f: "avg",
		field: "quantity",
		operator: ">",
		value: 3,
		conditions: []
	}];

	var testSegment2 = {
		_id: new mongodb.ObjectId('123456789012345678012346'),
		condition: [{type: "user", "conditions": ["gender", "eq", "male"]}],
		_appid: process.argv[2]
	};

	var segment = {
		_id: new mongodb.ObjectId('123456789012345678012345'),
		condition: testJson2,
		_appid: process.argv[2]
	};

	//console.time('mr1');
	seg.runSegment(segment, function (out) {
		//console.timeEnd('mr1');
		//console.log(JSON.stringify(out));
		//console.time('mr2');
		seg.runSegment(testSegment2, function(out){
			//console.timeEnd('mr2');
			//console.log(JSON.stringify(out));
		})
	});

});

//testsegment <appid>
