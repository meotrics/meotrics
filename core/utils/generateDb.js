(function () {
	'use strict';

	var MongoClient = require('mongodb').MongoClient,
			idmanager = require('./fakeidmanager.js'),
			converter = new idmanager.IdManager(),
			config = require('config'),
			Valuemgr = require('../module/valuemgr.js').ValueMgr;

	var UserG = require('./generateUsers.js');
	var ActionG = require('./generateLog.js');

	var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');
	var prefix = config.get('mongod.prefix');
	var appid = process.argv[2];
	var collection = prefix + appid;
	var nusers = process.argv[3] || 2000;
	var npageviews = process.argv[4] || 100000;
	var npurchases = process.argv[5] || 5000;
	var codenamepageview = process.argv[6] || 'pageview';
	var codenamepurchase = process.argv[7] || 'purchase';

	MongoClient.connect(url).then(function (db) {

		var valuemgr = new Valuemgr(db, prefix);
		UserG.generate(appid, valuemgr, converter, url, nusers, collection, function () {
			ActionG.generate(appid, valuemgr, converter, url, npageviews, npurchases, collection, codenamepageview, codenamepurchase, function () {
				process.exit();
			});
		});
	});
//node utils/generateDb.js 1 [2000 100000 50000 pageview purchase]
})();