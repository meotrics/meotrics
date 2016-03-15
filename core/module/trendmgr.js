exports.TrendMgr = function (db, mongodb) {
	this.list = function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).find({_id: new mongodb.ObjectID(trid)}, {_id: 0, _appid: 0}).toArray()
				.then(function (results) {
					res.json(results);
				}).catch(mtthrow);
	};

	this.listadd = function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "trend";
		db.collection(collection).find({_appid: appid}, {_appid: 0}).toArray()
				.then(function (results) {
					res.json(results);
				}).catch(mtthrow);
	}

	this.create = function (req, res) {
		var data = req.body;
		data._appid = Number(req.params.appid);
		var collection = prefix + "trend";

		db.collection(collection).insertOne(data)
				.then(function (r) {
					res.json({_id: r.insertedId});
				}).catch(mtthrow);
	}

	this.update = function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).updateOne({_id: new mongodb.ObjectID(trid)}, {$set: data})
				.then(function (results) {
					res.status(200).end();
				}).catch(mtthrow);
	}

	this.delete = function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).deleteOne({_id: new mongodb.ObjectID(trid)})
				.then(function (results) {
					res.status(200).end();
				}).catch(mtthrow);
	}

	this.load = function (req, res) {
		var appid = req.params.appid;
		var collection = prefix + "trend";
		var results = [];
		async.waterfall([
			function (callback) {
				var trid = req.params.id;
				db.collection(collection).find({_id: new mongodb.ObjectID(trid)}, {_id: 0}).toArray()
						.then(function (r) {
							callback(null, r[0], converter);
						}).catch(function (e) {
					callback(e);
				});
			}, getQueryTrending
			, function (query, callback) {
				collection = prefix + appid;

				db.collection(collection).aggregate(query).toArray()
						.then(function (array) {
							results = array;
							async.forEachOf(results, function (value, key, asyncCallback) {
								converter.toOriginal(value.temp)
										.then(function (r) {
											results[key].temp = r;
											asyncCallback(null);
										}).catch(function (e) {
									asyncCallback(e);
								});
							}, function (err) {
								if (err) {
									callback(err);
								} else {
									res.json(results);
									callback(null);
								}
							});
						}).catch(function (err) {
					callback(err);
				});
			}
		], mtthrow);
	}
};