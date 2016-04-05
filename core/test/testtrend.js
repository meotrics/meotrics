var TrendMgr = require('../module/trendmgr.js').TrendMgr;

var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');

var converter = require('../utils/fakeidmanager.js');
var converter = new converter.IdManager();
function mtthrow(err) {
	setTimeout(function () {
		throw err;
	});
}

var appid = 1;

var prefix = config.get('mongod.prefix');
MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function (err, db) {
	if (err) throw err;
	var seg = new TrendMgr(db, mongodb, async, converter, prefix, mtthrow, appid);


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
		query: testJson2,
		appid: process.argv[2]
	}

	console.time('mr');
	seg.runSegment(segment, function (out) {
		console.timeEnd('mr');
		console.log(JSON.stringify(out));
	});

});