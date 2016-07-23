var SegmentResult = require('../module/segmentresult.js').SegmentResult;
var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');

var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();


MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function (err, db) {
	if (err) throw err;
	var seg = new SegmentResult(db, mongodb, converter, async, config.get('mongod.prefix'));
//	console.time('gb1');
	seg.groupby(process.argv[2], "123456789012345678012345", "gender", "string", undefined, undefined, function (ret) {
//		console.log(ret);
//		console.timeEnd('gb1');

//		console.time('gb2');
		seg.groupby(process.argv[2], "123456789012345678012345", "_os", "string", undefined, undefined, function (ret) {
//			console.log(ret);
//			console.timeEnd('gb2');

//			console.time('gb3');
			seg.groupby(process.argv[2], "123456789012345678012345", "height", "number", undefined, undefined, function (ret) {
//				console.log(ret);
//				console.timeEnd('gb3');

//				console.time('gb4');
				seg.groupby(process.argv[2], "123456789012345678012345", "age", "number", undefined, undefined, function (ret) {
//					console.log(ret);
//					console.timeEnd('gb4');

//					console.time('gb5');
					seg.groupby(process.argv[2], "123456789012345678012345", "gender", "string", '_os', 'string', function (ret) {
//						console.log(JSON.stringify(ret));
//						console.timeEnd('gb5');

//						console.time('gb6');
						seg.groupby(process.argv[2], "123456789012345678012345", "age", "number", 'gender', 'string', function (ret) {
//							console.log(JSON.stringify(ret));
//							console.timeEnd('gb6');

//							console.time('gb7');
							seg.groupby(process.argv[2], "123456789012345678012345", "age", "number", 'height', 'number', function (ret) {
//								console.log(JSON.stringify(ret));
//								console.timeEnd('gb7');

//								console.time('gb8');
								seg.groupby(process.argv[2], "123456789012345678012345", "gender", "string", 'height', 'number', function (ret) {
//									console.log(JSON.stringify(ret));
//									console.timeEnd('gb8');

//									console.time('gb9');
									seg.groupby(process.argv[2], "123456789012345678012345", "age", "number", '_os', 'string', function ( ret) {
//										console.log(JSON.stringify(ret));
//										console.timeEnd('gb9');
										process.exit(0);
									});
								});
							});
						});
					});
				});
			});
		});
	});
});

//testsegmentresult <appid>
