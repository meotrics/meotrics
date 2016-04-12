exports.TrendMgr = function (db, mongodb, async, converter, prefix, col) {
	var me = this;
	this.queryRaw = function (appid, trid, callback) {
		var collection = prefix + col;
		db.collection(collection).find({_id: new mongodb.ObjectID(trid)}, {_id: 0}).limit(1).next(function (err, trenddoc) {
			if (err) throw err;
			getQueryTrending(trenddoc, converter, function (query) {
				var collection = prefix + appid;
				db.collection(collection).aggregate(query).toArray(function (err, results) {
					if (err) throw err;
					async.forEachOf(results, function (value, key, asyncCallback) {
						converter.toOriginal(value.temp, function (r) {
							results[key].temp = r;
							asyncCallback(null);
						});
					}, function (err) {
						if (err) throw err;
						callback(results);
					});
				});
			});
		});
	};

	this.query = function (req, res) {
		var appid = req.params.appid;
		var trid = req.params.id;
		me.queryRaw(appid, trid, function (results) {
			res.json(results);
		});
	};

	function getQueryTrending(object, converter, callback) {
		var query = [];
		converter.toIDs([object.object, "_typeid", "_ctime", "_segment", object.param], function (ids) {
			object.object = ids[object.object];
			object.param = ids[object.param];

			var match = {$match: {}};
			match['$match'][ids["_typeid"]] = object.typeid;
			// -- START MATCH CLAUSE
			query.push(match);
			if (object._segment !== undefined) {
				match[ids['_segments']] = {
					'$in': [object._segment]
				};
			}

			if (object.startTime !== undefined) {
				match[ids['_ctime']] = {
					$gte: object.startTime
				};
			}

			if (object.endTime !== undefined) {
				if (match[ids['_ctime']] !== undefined) {
					match[ids['_ctime']]['$lte'] = object.endTime;
				} else {
					match[ids['_ctime']] = {
						$lte: object.endTime
					};
				}
			}
			// -- END OF MATCH CLAUSE

			// -- START GROUP FUNCTION
			object.order = object.order || 1;

			if (object.operation == 'count') {
				query.push({$group: {_id: '$' + object.object, result: {'$sum': 1}, temp: {$first: "$$ROOT"}}});
				query.push({$sort: {result: object.order}});

				object.limit = object.limit || 10;
				query.push({$limit: object.limit});

				callback(query);
				return;
			}

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

			callback(query);
		});
	}
};