"use strict";
exports.Dashboard = function (db, mongodb, converter, prefix, delaysec) {
	const Lock = require('lock');
	const lock = Lock();
	var me = this;
	// purpose: get dashboard cache
	// the step:
	// 1. if cache not existed then create cache
	// 2. 
	//
	this.getDashboard = function (appid, callback) {
		//
		function generateDashboard(gcallback) {

			converter.toIDs(["_isUser", "_mtid"], function (ids) {


//1 number of today visitor
				var todayvismatch = {};
				todayvismatch[ids._isUser] = {$exists: false};

				var pipelines = [{$match: todayvismatch}, {$group: {_id: "$" + ids._mtid}}, {
					$group: {
						_id: null,
						count: {$sum: 1}
					}
				}];
				db.collection().aggregate(pipelines, function (err, res) {
					if (err) throw err;
					console.log(res);
					
					gcallback();
				});
			});
		}

		db.collection(prefix + "dashboard").find({appid: appid}).limit(1).toArray(function (err, res) {
			if (err) throw err;
			// cache not existed
			if (res.length === 0) {
				lock("c_" + appid, function (release) {
					// recheck because cache could be create after the lock
					db.collection(prefix + "dashboard").find({appid: appid}).limit(1).toArray(function (err, res) {
						if (err) throw err;
						if (res.length === 0) {

							generateDashboard(function (dash) {
								callback(dash);
								dash.appid = appid + "";
								dash.ctime = Math.round(new Date().getTime() / 1000);
								db.collection(prefix + "dashboard").updateOne({appid: appid + ""}, dash, {upsert: true}, function (err) {
									release();
									if (err) throw err;
								});
							});
						} else {
							return release(function () {
								// why not just do callback(res[0]) ?
								// this may look wasted, but give us 100% guarranty that, 
								// the result dashboard alway up to date
								// the problem with res[0] is that, is very likely up to date 
								// (deltatime < delay) but there is no warranty
								me.getDashboard(appid, callback);
							});
						}
					});
				});
				return;
			}

			var dash = res[0];
			var deltaT = Math.round(new Date().getTime() / 1000) - dash.ctime;

			if (deltaT > delaysec) {
				lock(appid + "", function (release) {
					db.collection(prefix + "dashboard").find({appid: appid}).limit(1).toArray(function (err, res) {
						if (err) throw err;
						// this is a must found
						var dash = res[0];
						var deltaT = Math.round(new Date().getTime() / 1000) - dash.ctime;
						if (deltaT > delaysec) {
							// refresh the cache
							generateDashboard(function (dash) {
								callback(dash);
								dash.appid = appid + "";
								dash.ctime = Math.round(new Date().getTime() / 1000);
								db.collection(prefix + "dashboard").update({appid: appid + ""}, dash, {upsert: true}, function (err) {
									release();
									if (err) throw err;
								});
							});
						} else
							return release(function () {
								// callback(dash);
								// again, why not use callback(dash) ? because i want 100% guarranty that the dash
								// is up to date (deltatime < delay)
								me.getDashboard(appid, callback);
							});
					});
				});
			} else {
				callback(dash);
			}
		});
	};

};