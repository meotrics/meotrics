﻿import * as mongodb from 'mongodb';
import * as async from 'async';

export class TrendMgr {
	public constructor(private db: mongodb.Db, private converter, private prefix: string, private col: string) {
	}

	public queryRaw(appid: string, trid: string, segid: string, starttime: number, endtime: number, callback: (a: any[]) => void) {
		let me = this;
		var collection = me.prefix + me.col;

		me.db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0 }).limit(1).next(function (err, trenddoc) {
			if (err) throw err;
			if (trenddoc === null) return callback(null);
			if (segid !== undefined && segid !== '' && segid !== '_') trenddoc._segment = segid;
			if (starttime !== undefined) trenddoc.startTime = starttime;
			if (endtime !== undefined) trenddoc.endTime = endtime;

			me.getQueryTrending(trenddoc, function (query) {
				var collection = me.prefix + "app" + appid;
				me.db.collection(collection).aggregate(query).toArray(function (err, results) {
					if (err) throw err;
					async.forEachOf(results, function (value, key, asyncCallback) {
						me.converter.toOriginal(value.temp, function (r) {
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
	}

	public query(req, res) {
		var appid = req.params.appid;
		var trid = req.params.id;
		var segid = req.params.segid;
		var starttime = parseInt(req.params.starttime);
		var endtime = parseInt(req.params.endtime);
		this.queryRaw(appid, trid, segid, starttime, endtime, function (results) {
			if (results === null)
				return res.status(404).end();
			res.json(results);
		});
	};

	private getQueryTrending(object, callback: (string) => void) {
		var me = this;
		var query = [];
		me.converter.toIDs([object.object, "_typeid", "_ctime", "_segments", "_mtid", object.param], function (ids) {
			object.object = ids[object.object];
			object.param = ids[object.param];

			var match = { $match: {} };
			match.$match[ids._typeid] = object.typeid;
			// -- START MATCH CLAUSE
			query.push(match);
			
			if (object._segment !== undefined) {
				var lookup = {
					$lookup:
					{
						from: me.prefix + "app" + object._appid,
						localField: ids._mtid,
						foreignField: "_id",
						as: "_user"
					}
				};
				query.push(lookup);
				var matchSegment = {
					$match: {}
				};
				matchSegment.$match[`_user.0.${ids._segments}`] = { $in: [new mongodb.ObjectID(object._segment)] };
				query.push(matchSegment);
			}
			
			if (object.startTime !== undefined || object.startTime !== null) {
				match.$match[ids._ctime] = { $gte: object.startTime };
			}

			if (object.endTime !== undefined || object.endTime !== null) {
				if (match.$match[ids._ctime] !== undefined) match.$match[ids._ctime].$lte = object.endTime;
				else match.$match[ids._ctime] = { $lte: object.endTime };
			}
			// -- END OF MATCH CLAUSE

			// -- START GROUP FUNCTION
			object.order = parseInt(object.order) || 1;

			if (object.operation === 'count') {
				if (object.param !== undefined && object.param !== '_id') {
										query.push({ $group: { _id: { obj: '$' + object.object, par: '$' + object.param }, temp: { $first: "$$ROOT" } } });
										query.push({ $group: { _id: '$_id.obj', result: { $sum: 1 }, temp: { $first: '$temp' } } });
				}
				else {
										query.push({ $group: { _id: '$' + object.object, result: { $sum: 1 }, temp: { $first: "$$ROOT" } } });
				}
				query.push({ $sort: { result: object.order } });

				object.limit = object.limit || 10;
				query.push({ $limit: object.limit });
				callback(query);
				return;
			}

			if (object.operation === 'avg') {
				query.push({ $group: { _id: '$' + object.object, result: { $avg: '$' + object.param }, temp: { $first: "$$ROOT" } } });
				query.push({ $sort: { result: object.order } });
			} else if (object.operation === 'sum') {
				query.push({ $group: { _id: '$' + object.object, result: { $sum: '$' + object.param }, temp: { $first: "$$ROOT" } } });
				query.push({ $sort: { result: object.order } });
			}
			object.limit = object.limit || 10;
			query.push({ $limit: object.limit });

			callback(query);
		});
	}
}
