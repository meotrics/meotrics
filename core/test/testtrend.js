var TrendMgr = require('../module/trendmgr.js').TrendMgr;

var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');
var CRUD = require('../module/crud.js').CRUD;

var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();

var appid = 1;

var prefix = config.get('mongod.prefix');
MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function (err, db) {
	if (err) throw err;
	var trend = new TrendMgr(db, mongodb, async, converter, prefix, "trend");
	var trendCrud = new CRUD(db, mongodb, async, converter, prefix, "trend");

	var t = {
		operation: 'avg',
		param: 'price',
		object: 'pid',
		typeid: 'purchase',
		order: 1
	};

	//create a trend
	trendCrud.createRaw(appid, t, function (data) {
		console.log(data);
		trend.queryRaw(appid, data, function ( results) {
			console.log(results);

			var t2 = {
				operation: 'count',
				param: '_mtid',
				object: 'url',
				typeid: 'pageview',
				order: -1
			};
			console.time('t2');
			trendCrud.createRaw(appid, t2, function (data) {
				trend.queryRaw(appid, data, function ( results) {
					console.timeEnd('t2');
					console.log(results);
				});
			});
		});
	});
});