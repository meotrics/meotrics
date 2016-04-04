exports.CRUD = function (db, mongodb, async, converter, prefix, col) {
	var me = this;

	this.createRaw = function (appid, data, callback, next) {
		var collection = prefix + col;
		data._appid = Number(appid);
		data._ctime = Math.round(new Date() / 1000);
		converter.toObject(data).then(function (r) {
			return db.collection(collection).insertOne(r);
		}).then(function (r) {
			var _typeid = r.insertedId;
			callback(_typeid);
		}).catch(next);
	};

	this.create = function (req, res, next) {
		me.createRaw(req.params.appid, req.body, function (typeid) {
			res.send(typeid);
		}, next);
	};

	this.list = function (req, res, next) {
		var appid = Number(req.params.appid);
		var collection = prefix + col;
		converter.toIDs(['_appid', '_isDraft'], function (ids) {
			var query = {'$and': []};
			var projection = {};

			var andStatement = {};
			andStatement[ids['_appid']] = appid;
			query['$and'].push(andStatement);

			andStatement = {'$or': []};
			var orStatement = {};
			orStatement[ids['_isDraft']] = false;
			andStatement['$or'].push(orStatement);
			orStatement = {};
			orStatement[ids['_isDraft']] = {'$exists': false};
			andStatement['$or'].push(orStatement);

			query['$and'].push(andStatement);
			projection[ids['_appid']] = 0;
			projection[ids['_isDraft']] = 0;

			var cursor = db.collection(collection).find(query, projection);
			var done = false;
			var results = [];
			async.whilst(
					function () {
						return done == false;
					},
					function (callback) {
						cursor.next().then(function (r) {
							if (r) {
								converter.toOriginal(r).then(function (r) {
									results.push(r);
									callback(null);
								}).catch(function (err) {
									callback(err);
								});
							} else {
								done = true;
								callback(null, results);
							}
						}).catch(function (err) {
							callback(err);
						});
					}, function (err, results) {
						if (err) {
							console.log('haha');
							next(err);
						} else {
							res.json(results);
						}
					});

		});
	}

	this.match = function (req, res, next) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + col;
		converter.toID('_appid')
				.then(function (r) {
					var query = {
						_id: new mongodb.ObjectID(atid)
					};
					var projection = {};
					projection[r] = 0;
					return db.collection(collection).find(query, projection).toArray();
				}).then(function (r) {
					if (r.length != 0) {
						converter.toOriginal(r[0]).then(function (r) {
							res.json(r);
						}).catch(next);
					} else {
						res.status(200).end();
					}
				}).catch(next);
	};

	this.delete = function (req, res, next) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + col;
		db.collection(collection).deleteOne({_id: new mongodb.ObjectID(atid)})
			.then(function (r) {
				res.status(200).end();
			}).catch(next);
	};

	this.update = function (req, res, next) {
		var data = req.body;
		var atid = req.params.id;
		var collection = prefix + col;
		converter.toObject(data)
				.then(function (r) {
					return db.collection(collection).updateOne({_id: new mongodb.ObjectID(atid)}, {$set: r})
				}).then(function (results) {
			res.status(200).end();
		}).catch(next);
	};

	this.deleteDraf = function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + col;
		converter.toIDs(['_appid', '_isDraft'], function (ids) {
			var query = {};
			query[ids['_appid']] = appid;
			query[ids['_isDraft']] = true;
			db.collection(collection).deleteMany(query).then(function (r) {
				res.status(200).end();
			}).catch(next);
		});
	}
};