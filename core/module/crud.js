exports.Crud = function (db, mongodb, converter, prefix, mtthrow, collection) {

	this.create = function (req, res) {
		var collection = prefix + collection;
		var data = req.body;
		data._appid = Number(req.params.appid);
		converter.toObject(data)
				.then(function (r) {
					return db.collection(collection).insertOne(r);
				}).then(function (r) {
			var _typeid = r.insertedId;
			res.send(_typeid);
		}).catch(mtthrow);
	};

	this.list = function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + collection;
		converter.toID('_appid')
				.then(function (r) {
					var query = {};
					var projection = {};
					query[r] = appid;
					projection[r] = 0;
					return db.collection(collection).find(query, projection);
				}).then(function (cursor) {
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
							mtthrow(err);
						} else {
							res.json(results);
						}
					});
		}).catch(mtthrow);
	};

	this.match = function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + collection;
		converter.toID('_appid')
				.then(function (r) {
					var query = {
						_id: new mongodb.ObjectID(atid)
					};
					var projection = {};
					projection[r] = 0;
					projection['_id'] = 0;
					return db.collection(collection).find(query, projection).toArray();
				}).then(function (r) {
			if (r.length != 0) {
				converter.toOriginal(r[0]).then(function (r) {
					res.json(r);
				}).catch(mtthrow);
			} else {
				res.status(200).end();
			}
		}).catch(mtthrow);
	};

	this.delete = function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix +collection;
		db.collection(collection).deleteOne({_id: new mongodb.ObjectID(atid)})
				.then(function (r) {
					res.status(200).end();
				}).catch(mtthrow);
	};

	this.update = function(req,res)
	{
			var data = req.body;
			var atid = req.params.id;
			var collection = prefix + collection;
			converter.toObject(data)
					.then(function (r) {
						return db.collection(collection).updateOne({_id: new mongodb.ObjectID(atid)}, {$set: r})
					}).then(function (results) {
				res.status(200).end();
			}).catch(mtthrow);
	};
};