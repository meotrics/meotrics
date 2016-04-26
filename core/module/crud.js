"use strict";
exports.CRUD = function (db, mongodb, async, converter, prefix, col) {
	var me = this;

	this.createRaw = function (appid, data, callback) {
		var collection = prefix + col;
		data._appid = Number(appid);
		data._ctime = Math.round(new Date() / 1000);
		converter.toObject(data, function (r) {
			db.collection(collection).insertOne(r, function (err, ret) {
				if (err) throw err;
				var _typeid = ret.insertedId;
				callback(_typeid);
			});
		});
	};

	this.create = function (req, res, callback) {
		me.createRaw(req.params.appid, req.body, function (id) {
			res.send(id);
			if (callback ) callback(id);
		});
	};

	this.list = function (req, res, callback) {
		var appid = Number(req.params.appid);
		var collection = prefix + col;
		converter.toIDs(['_appid', '_isDraft'], function (ids) {
			var query = {$and: []};
			var projection = {};

			var andStatement = {};
			andStatement[ids._appid] = appid;
			query.$and.push(andStatement);

			andStatement = {$or: []};
			var orStatement = {};
			orStatement[ids._isDraft] = false;
			andStatement.$or.push(orStatement);
			orStatement = {};
			orStatement[ids._isDraft] = {$exists: false};
			andStatement.$or.push(orStatement);

			query.$and.push(andStatement);
			projection[ids._appid] = 0;
			projection[ids._isDraft] = 0;

			var cursor = db.collection(collection).find(query, projection);
			var done = false;
			var results = [];
			async.whilst(function () {
				return done === false;
			}, function (callback) {
				cursor.next(function (err, r) {
					if (err) throw err;
					if (r) {
						converter.toOriginal(r, function (r) {
							results.push(r);
							callback(null);
						});
					} else {
						done = true;
						callback(null, results);
					}
				});
			}, function (err, results) {
				if (err) throw err;
				res.json(results);
				if (callback ) callback(results);
			});
		});
	};

	this.match = function (req, res, callback) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + col;
		converter.toID('_appid', function (r) {
			var query = {_id: new mongodb.ObjectID(atid)};
			var projection = {};
			projection[r] = 0;
			db.collection(collection).find(query, projection).toArray(function (err, r) {
				if (err) throw err;

				if (r.length !== 0) {
					converter.toOriginal(r[0], function (r) {
						res.json(r);
						if(callback) callback();
					});
				} else {
					res.status(200).end();
				}
			});
		});
	};

	this.delete = function (req, res, callback) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + col;
		db.collection(collection).deleteOne({_id: new mongodb.ObjectID(atid)}, function (err, r) {
			if (err) throw err;
			res.status(200).end();
			if (callback !== undefined) callback();
		});
	};

	this.update = function (req, res, callback) {
		var data = req.body;
		var atid = req.params.id;
		var collection = prefix + col;
		converter.toObject(data, function (r) {
			db.collection(collection).updateOne({_id: new mongodb.ObjectID(atid)}, {$set: r}, function (err, results) {
				if (err) throw  err;
				res.status(200).end();
				if (callback !== undefined) callback();
			});
		});
	};

	this.deleteDraf = function (req, res, callback) {
		var appid = Number(req.params.appid);
		var collection = prefix + col;
		converter.toIDs(['_appid', '_isDraft'], function (ids) {
			var query = {};
			query[ids._appid] = appid;
			query[ids._isDraft] = true;
			db.collection(collection).deleteMany(query, function (err, r) {
				if (err) throw err;
				res.status(200).end();
				if (callback) callback();
			});
		});
	};
};