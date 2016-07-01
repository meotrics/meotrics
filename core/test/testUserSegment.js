var UserSegment = require('../module/usersegment.js').UserSegment;
var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');

var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();


MongoClient.connect("mongodb://" + config.get("mongod.host") + ":" + config.get('mongod.port') + '/' + config.get('mongod.database'), function (err, db) {
	if (err) throw err;
	var userSegment = new UserSegment(db, mongodb, async, converter, config.get('mongod.prefix'));

	userSegment.getUsers('apphmt5', [], ['_os', 'gender', '_city', '_listProduct', '_numberPurchase'], 0, function(users){
		console.log(users);
	});

	// this.getUsers = function(appName, segmentId, fields, start, callback)
	// neu khong chon segment nao thi de la []
	// start la vi tri bat dau lay
	// fields la cac field can lay gia tri, co 3 field mac dinh hien tai la name, _mtid, email
	// a nho nhap dung ten cua field tuong ung trong db
	// e mo` mai~ chua test duoc may cai field vua them vao user nhu _numberProduct, _productPurchasing ...
});
