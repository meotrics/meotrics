"use strict";

exports.TypeMgr = function (db, mongodb, converter, async, prefix, typecrud, col) {
	var systemprops = [
		{pname: "Campaign", pcode: "_utm_campaign"},
		{pname: "Campaign Source", pcode: "_utm_source"},
		{pname: "Campaign Medium", pcode: "_utm_medium"},
		{pname: "Campaign Term", pcode: "_utm_term"},
		{pname: "Campaign Content", pcode: "_utm_content"},
		{pname: "Time", pcode: "_ctime"},
		{pname: "Channel", pcode: "_reftype"},
		{pname: "Referer", pcode: "_ref"},
		{pname: "Country", pcode: "_country" },
		{pname: "City", pcode: "_city"},
		{pname: "Location", pcode: "_location"},
		{pname: "Operating System", pcode: "_os"},
		{pname: "OS Version", pcode: "_osver"},
		{pname: "Browser", pcode: "_browser" },
		{pname: "Browser Version", pcode: "_browserver"},
		{pname: "Device", pcode: "_deviceid" },
		{pname: "Device Type", pcode: "_devicetype" },
		{pname: "Screen Resolution", pcode: "_scr"},
		{pname: "Language", pcode: "_lang"}
	];
	this.create = function (req, res, callback) {
		// TODO: check if typename existed
		// TODO: check if fields is system
		typecrud.create(req, res, callback);
	};

	this.update = function (req, res, callback) {
		typecrud.update(req, res, callback);
	};

	this.list = function (req, res, callback) {
		var appid = req.params.appid;
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
							if (r.fields instanceof Array)
								r.fields = r.fields.concat(systemprops);
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
				if (callback) callback(results);
			});
		});
	};

	this.delete = function(req, res, callback){
		typecrud.delete(req, res, callback);
	};

	this.match = function(req, res, callback){
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
						if (r.fields instanceof Array)
							r.fields = r.fields.concat(systemprops);
						res.json(r);
						if(callback) callback();
					});
				} else {
					res.status(200).end();
				}
			});
		});
	};
};