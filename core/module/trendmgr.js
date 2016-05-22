exports.TrendMgr = function (db, mongodb, async, converter, prefix, col) {
	var me = this;
	this.queryRaw = function (appid, trid, segid, starttime, endtime, callback) {
		var collection = prefix + col;
		db.collection(collection).find({_id: new mongodb.ObjectID(trid)}, {_id: 0}).limit(1).next(function (err, trenddoc) {
			if (err) throw err;
			if (trenddoc === null) return callback(null);
			if (segid !== undefined && segid !== '' && segid !== '_') trenddoc._segment = segid;
			if (starttime !== undefined && starttime !== '') trenddoc.startTime = parseInt( Math.round(new Date(starttime) / 1000));
			if (endtime !== undefined && endtime !== '') trenddoc.endTime = parseInt(Math.round(new Date(endtime) / 1000 + 86400));

			getQueryTrending(trenddoc, converter, function (query) {
				var collection = prefix + "app" + appid;
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
		var segid = req.params.segid;
		var starttime = req.params.starttime;
		var endtime = req.params.endtime;
		me.queryRaw(appid, trid, segid, starttime, endtime, function (results) {
			if (results === null)
				return res.status(404).end();
			res.json(results);
		});
	};

	function getQueryTrending(object, converter, callback) {
		var query = [];
		converter.toIDs([object.object, "_typeid", "_ctime", "_segment", object.param], function (ids) {
			object.object = ids[object.object];
			object.param = ids[object.param];

			var match = {$match: {}};
			match.$match[ids._typeid] = object.typeid;
			// -- START MATCH CLAUSE
			query.push(match);
			if (object._segment !== undefined) {
				match[ids._segments] = {
					$in: [new mongodb.ObjectId(object._segment)]
				};
			}

			if (object.startTime !== undefined) {
				match[ids._ctime] = {
					$gte: object.startTime
				};
			}

			if (object.endTime !== undefined) {
				if (match[ids._ctime] !== undefined) {
					match[ids._ctime].$lte = object.endTime;
				} else {
					match[ids._ctime] = {
						$lte: object.endTime
					};
				}
			}
			// -- END OF MATCH CLAUSE

			// -- START GROUP FUNCTION
			object.order = parseInt(object.order) || 1;

			if (object.operation === 'count') {
				if (object.param !== undefined && object.param !== '_id') {
					query.push({$group: {_id: {obj: '$' + object.object, par: '$' + object.param}, temp: {$first: "$$ROOT"}}});
					query.push({$group: {_id: '$_id.obj', result: {$sum: 1}, temp: {$first: '$temp'}}});
				}
				else {
					query.push({$group: {_id: '$' + object.object, result: {$sum: 1}, temp: {$first: "$$ROOT"}}});
				}
				query.push({$sort: {result: object.order}});

				object.limit = object.limit || 10;
				query.push({$limit: object.limit});
				callback(query);
				return;
			}

			if (object.operation === 'avg') {
				query.push({
					$group: {
						_id: '$' + object.object,
						result: {$avg: '$' + object.param},
						temp: {$first: "$$ROOT"}
					}
				});
				query.push({$sort: {result: object.order}});
			} else if (object.operation === 'sum') {
				query.push({
					$group: {
						_id: '$' + object.object,
						result: {$sum: '$' + object.param},
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