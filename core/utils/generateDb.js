var MongoClient = require('mongodb').MongoClient,
		idmanager = require('./fakeidmanager.js'),
		converter = new idmanager.IdManager();
config = require('config');

var UserG = require('./generateUsers.js');
var ActionG = require('./generateLog.js');

var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');

var collection = process.argv[2];
var nusers = process.argv[3] || 2000;
var npageviews = process.argv[4] || 100000;
var npurchases = process.argv[5] || 5000;
var codenamepageview = process.argv[6] || 'pageview';
var codenamepurchase = process.argv[7] || 'purchase';
UserG.generate(converter, url, nusers, collection, function () {
	ActionG.generate(converter, url, npageviews, npurchases, collection, codenamepageview, codenamepurchase, function () {
		process.exit();
	});
});


//node utils/generateDb.js meotrics_1 [2000 100000 50000 pageview purchase]