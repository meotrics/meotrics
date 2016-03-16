var MongoClient = require('mongodb').MongoClient;
var mongodb = require('mongodb');

var users = null;
var numberUsers = 0;
var db = null;


exports.generate = function (converter, url, npageviews, npurchases, collection, callback) {
	if (callback === undefined) callback = function () {
	};

	console.log('--GENERATE PAGEVIEW--');
	getUsers('pageview', converter, url, npageviews, collection, function () {
		console.log('--GENERATE PURCHASE--');
		getUsers('purchase', converter, url, npurchases, collection, callback);
	});
};

function getUsers(actiontype, converter, url, n, collection, callback) {
	MongoClient.connect(url)
			.then(function (database) {
				console.log('[MongoDB] connected');
				db = database;
				// Listen for some events
				db.on('reconnect', function (data) {
					console.log('[MongoDB] reconnect success');
				});
				db.on('error', function (err) {
					console.log('[MongoDB] error', err.message);
				});
				db.on('close', function (err) {
					console.log('[MongoDB] disconnected');
				});

				return converter.toID('_isUser');
			}).then(function (r) {
		var query = {};
		query[r] = true;
		return db.collection(collection).find(query, {_id: 1}).toArray();
	}).then(function (results) {
		users = results;
		numberUsers = users.length;
		generateDB(actiontype, converter, url, n, collection, callback);
	}).catch(function (err) {
		console.error("[MongoDB]", err.message);
		setTimeout(function () {
			getUsers(actiontype, converter, url, n, collection, callback)
		}, 2000);
	});
}

function generatePageView(ids) {
	var user = users[generateNumber(0, numberUsers - 1)];
	var page = {};
	page[ids._typeid] = new mongodb.ObjectID("56e8c128282aa21428689c6a");
	page[ids.url] = 'http://' + generateNumber(1, 1000) + '.com';
	page[ids.camid] = generateNumber(1, 10);
	page[ids._ctime] = Math.floor(new Date().getTime() / 1000);
	page[ids._mtid] = user._id;
	return page;
}

function generatePurchase(ids) {
	var user = users[generateNumber(0, numberUsers - 1)];
	var pid = generateNumber(1, 1000);
	var purchase = {};
	purchase[ids._typeid] = new mongodb.ObjectID("56e8c0ca282aa21428689c68");
	purchase[ids._ctime] = Math.floor(new Date().getTime() / 1000);
	purchase[ids._mtid] = user._id;
	purchase[ids.cid] = pid % 10;
	purchase[ids.pid] = pid;
	purchase[ids.quantity] = generateNumber(1, 10);
	purchase[ids.price] = generateNumber(10, 200);
	return purchase;
}

function generateNumber(min, max) {
	var range = max - min;

	var number = Math.floor(Math.random() * (range + 1)) + min;
	return number;
}

function generateDB(actiontype, converter, url, n, collection, callback) {
	var count = 0;
	if (actiontype == 'purchase') {
		converter.toIDs(['_typeid', '_ctime', '_mtid', 'cid', 'pid', 'quantity', 'price'], function (ids) {
			for (var i = 0; i < n; i++) {
				count++;
				var r = generatePurchase(ids);
				db.collection(collection).insertOne(r)
						.then(function (results) {
							count--;
							if (count % 1000 == 0)
								console.log((n - count) + ' records');
							if (count == 0) {
								db.close();
								console.log('Done');
								callback();
							}
						}).catch(function (err) {
					console.log('[MongoDB] insert err', err.message);
				});
			}
			;
		});
	} else {
		converter.toIDs(['_typeid', 'url', 'camid', '_ctime', '_mtid'], function (ids) {
			for (var i = 0; i < n; i++) {
				count++;
				var r = generatePageView(ids);
				db.collection(collection).insertOne(r)
						.then(function (results) {
							count--;
							if (count % 1000 == 0)
								console.log((n - count) + ' records');
							if (count == 0) {
								db.close();
								console.log('Done');
								callback();
							}
						}).catch(function (err) {
					console.log('[MongoDB] insert err', err.message);
				});
			}
			;
		});
	}
}
