"use strict";

exports.TypeMgr = function (db, mongodb, converter, async, prefix, typecrud, col) {
	var systemprops = [
		{pname: "Time", pcode: "_ctime"},
		{pname: "Campaign", pcode: "_utm_campaign"},
		{pname: "Campaign Source", pcode: "_utm_source"},
		{pname: "Campaign Medium", pcode: "_utm_medium"},
		{pname: "Campaign Term", pcode: "_utm_term"},
		{pname: "Campaign Content", pcode: "_utm_content"},
		{pname: "URL", pcode: "_url"},
		{pname: "Operating System", pcode: "_os"},
		{pname: "Language", pcode: "_lang"},
		{pname: "City", pcode: "_city"},
		{pname: "Referer", pcode: "_ref"},
		{pname: "Screen Resolution", pcode: "_scr"},
		{pname: "Browser Version", pcode: "_browser"},
		{pname: "OS Version", pcode: "_osver"},
		{pname: "Location", pcode: "_location"}
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
		typecrud(req, res, callback);
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