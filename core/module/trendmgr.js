exports.TrendMgr = function (db, mongodb, async, converter, prefix, col) {
	var me = this;
	this.queryRaw = function (appid, trid, callback) {
		var collection = prefix + col;
		var results = [];

		db.collection(collection).find({_id: new mongodb.ObjectID(trid)}, {_id: 0}).toArray(function (err, r) {
			if (err) throw err;
			getQueryTrending(r[0], converter, function (query) {
				var collection = prefix + appid;
				db.collection(collection).aggregate(query).toArray(function (err, array) {
					if (err) throw err;
					results = array;

					async.forEachOf(results, function (value, key, asyncCallback) {
						converter.toOriginal(value.temp, function (r) {
							results[key].temp = r;
							asyncCallback(null);
						});
					}, function (err) {
						if (err) throw err;
						callback(null, results);
					});
				});
			});
		});
	};

	this.query = function (req, res) {
		var appid = req.params.appid;
		var trid = req.params.id;
		me.queryRaw(appid, trid, function (err, results) {
			res.json(results);
		});
	};

	function getQueryTrending(object, converter, callback) {
		var query = [];
		converter.toID(object.object, function (r) {
			object.object = r;

			// -- START MATCH CLAUSE
			query.push({$match: {_typeid: new mongodb.ObjectID(object.typeid)}});

			if (object._segment != undefined) {
				query[0]['$match']['_segments'] = {
					'$in': [object._segment]
				};
			}

			if (object.startTime != undefined) {
				query[0]['$match']['_ctime'] = {
					$gte: object.startTime
				};
			}

			if (object.endTime != undefined) {
				if (query[0]['$match']['_ctime'] != undefined) {
					query[0]['$match']['_ctime']['$lte'] = object.endTime;
				} else {
					query[0]['$match']['_ctime'] = {
						$lte: object.endTime
					};
				}
			}
			// -- END OF MATCH CLAUSE

			// -- START GROUP FUNCTION
			object.order = object.order || 1;

			if (object.operation == 'count') {
				query.push({$group: {_id: '$' + object.object, count: {'$sum': 1}, temp: {$first: "$$ROOT"}}});
				query.push({$sort: {count: object.order}});

				object.limit = object.limit || 10;
				query.push({$limit: object.limit});

				converter.toObject(query[0]['$match'], function (r) {
					query[0]['$match'] = r;
					callback(null, query);
				});
				return;
			}

			converter.toID(object.param, function (r) {
				object.param = r;

				if (object.operation == 'avg') {
					query.push({
						$group: {
							_id: '$' + object.object,
							result: {'$avg': '$' + object.param},
							temp: {$first: "$$ROOT"}
						}
					});
					query.push({$sort: {result: object.order}});
				} else if (object.operation == 'sum') {
					query.push({
						$group: {
							_id: '$' + object.object,
							result: {'$sum': '$' + object.param},
							temp: {$first: "$$ROOT"}
						}
					});
					query.push({$sort: {result: object.order}});
				}
				object.limit = object.limit || 10;
				query.push({$limit: object.limit});
				query[0]['$match'] = converter.toObject(query[0]['$match']);
				callback(query);
			});

		});
	}
};