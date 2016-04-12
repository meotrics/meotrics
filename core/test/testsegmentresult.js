
var SegmentResult = require('../module/segmentresult.js').SegmentResult;
var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');

var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();


MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function(err, db) {
	if (err) throw err;
	var seg = new SegmentResult(db, mongodb, converter, async, config.get('mongod.prefix'));
	console.time('gb');
	seg.groupby(process.argv[2],"123456789012345678012345","gender","string", undefined, undefined,function(ret){
		console.log(ret);
		console.timeEnd('gb');
	});
});

//testsegmentresult <appid>