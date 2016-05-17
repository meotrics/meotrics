"use strict";
import * as mongo from 'mongodb';

export class DashboardEntity {
		public ctime: number;
		public appid: string;
		public n_new_visitor: number;
		public n_returning_visitor: number;
		public total_revenues: number[];
		public total_revenues_per_user: number[];
		public retention_rates\: number;
		public usergrowth_rate: number;
		public conversion_rate: number;
		public highest_revenue_campaign: string;
		public most_effective_ref: string;
}
export class Dashboard {
		private Lock = require('lock');
		private lock = this.Lock();

		public constructor(private db: mongo.Db, private converter, private prefix: string, private delaysec: number) {
		}

		private getRetensionRate(db: mongo.Db, prefix: string, appid: string, ids, callback: (a: number[]) => void) {
		//3 retension rate = number of user purchase within 7 days/ total number of visitor
		//get alltime visitor

		var n_users: number[] = [];
		var n_users_purchase: number[] = [];

		var now = new Date();
		var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let todaysec = Math.round(today.getTime() / 1000);
		let seventhdaybefore = todaysec - 7 * 24 * 3600;
		let b0 = todaysec;
		let b1 = b0 - 24 * 3600;
		let b2 = b1 - 24 * 3600;
		let b3 = b2 - 24 * 3600;
		let b4 = b3 - 24 * 3600;
		let b5 = b4 - 24 * 3600;
		let b6 = b5 - 24 * 3600;
		let b7 = b6 - 24 * 3600;
		let b8 = b7 - 24 * 3600;
		let b9 = b8 - 24 * 3600;
		let b10 = b9 - 24 * 3600;
		let b11 = b10 - 24 * 3600;
		let b12 = b11 - 24 * 3600;
		//let b13 = b12 - 24 * 3600;
		//let b14 = b13 - 24 * 3600;

		let alltimecount = {};
		alltimecount[ids._isUser] = { $exists: true };
		alltimecount[ids._ctime] = { $lt: b5 };
		db.collection(prefix + appid).count(alltimecount, function (err, res) {
			if (err) throw err;
			n_users.push(res);
			alltimecount[ids._ctime] = { $lt: b4 };
			db.collection(prefix + appid).count(alltimecount, function (err, res) {
				if (err) throw err;
				n_users.push(res);
				alltimecount[ids._ctime] = { $lt: b3 };
				db.collection(prefix + appid).count(alltimecount, function (err, res) {
					if (err) throw err;
					n_users.push(res);
					alltimecount[ids._ctime] = { $lt: b2 };
					db.collection(prefix + appid).count(alltimecount, function (err, res) {
						if (err) throw err;
						n_users.push(res);
						alltimecount[ids._ctime] = { $lt: b1 };
						db.collection(prefix + appid).count(alltimecount, function (err, res) {
							if (err) throw err;
							n_users.push(res);
							alltimecount[ids._ctime] = { $lt: b0 };
							db.collection(prefix + appid).count(alltimecount, function (err, res) {
								if (err) throw err;
								n_users.push(res);
								db.collection(prefix + appid).count(alltimecount, function (err, res) {
									if (err) throw err;
									n_users.push(res);

									// number of user purchase within 7 days of 7 days

									var retension_pipelines = [{ $match: {} }, { $group: { _id: "$" + ids._mtid } }, {
										$group: {
											_id: null, count: { $sum: 1 }
										}
									}];

									retension_pipelines[0]['$match'][ids._typeid] = "purchase";
									retension_pipelines[0]['$match'][ids._ctime] = { $ge: b12 };
									retension_pipelines[0]['$match'][ids._ctime] = { $lt: b5 };
									db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
										if (err) throw err;
										n_users_purchase.push(res.length === 0 ? 0 : res[0].count);
										retension_pipelines[0]['$match'][ids._typeid] = "purchase";
										retension_pipelines[0]['$match'][ids._ctime] = { $ge: b11 };
										retension_pipelines[0]['$match'][ids._ctime] = { $lt: b4 };
										db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
											if (err) throw err;
											n_users_purchase.push(res.length === 0 ? 0 : res[0].count);
											retension_pipelines[0]['$match'][ids._typeid] = "purchase";
											retension_pipelines[0]['$match'][ids._ctime] = { $ge: b10 };
											retension_pipelines[0]['$match'][ids._ctime] = { $lt: b3 };
											db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
												if (err) throw err;
												n_users_purchase.push(res.length === 0 ? 0 : res[0].count);
												retension_pipelines[0]['$match'][ids._typeid] = "purchase";
												retension_pipelines[0]['$match'][ids._ctime] = { $ge: b9 };
												retension_pipelines[0]['$match'][ids._ctime] = { $lt: b2 };
												db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
													if (err) throw err;
													n_users_purchase.push(res.length === 0 ? 0 : res[0].count);
													retension_pipelines[0]['$match'][ids._typeid] = "purchase";
													retension_pipelines[0]['$match'][ids._ctime] = { $ge: b8 };
													retension_pipelines[0]['$match'][ids._ctime] = { $lt: b1 };
													db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
														if (err) throw err;
														n_users_purchase.push(res.length === 0 ? 0 : res[0].count);
														retension_pipelines[0]['$match'][ids._typeid] = "purchase";
														retension_pipelines[0]['$match'][ids._ctime] = { $ge: b7 };
														retension_pipelines[0]['$match'][ids._ctime] = { $lt: b0 };
														db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
															if (err) throw err;
															n_users_purchase.push(res.length === 0 ? 0 : res[0].count);
															retension_pipelines[0]['$match'][ids._typeid] = "purchase";
															retension_pipelines[0]['$match'][ids._ctime] = { $ge: b6 };
															db.collection(prefix + appid).aggregate(retension_pipelines, function (err, res) {
																if (err) throw err;
																n_users_purchase.push(res.length === 0 ? 0 : res[0].count);

																let retension_rates = [];
																for (let i = 0; i < 6; i++) {
																	retension_rates.push(n_users[i] === 0 ? 0 : n_users_purchase[i] * 1.0 / n_users[i]);
																}

																callback(retension_rates);
															});
														});
													});
												});
											});
										});
									});
								});
							});
						});
					});
				});
			});

		});
		}

		public getDashboard(appid: string, callback: (d: DashboardEntity) => void) {
		let me = this;
		function generateDashboard(gcallback: (d: DashboardEntity) => void) {
			var dashboard: DashboardEntity = new DashboardEntity();
			me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid"], function (ids) {
				var now = new Date();
				var today = new Date(now.getFullYear(), now.getMonth(), now.getDate())

				let todaysec = Math.round(today.getTime() / 1000);
				let seventhdaybefore = todaysec - 7 * 24 * 3600;
				//1 number of new visitor today
				var todayvismatch = {};
				todayvismatch[ids._isUser] = { $exists: false };
				todayvismatch[ids._ctime] = { $ge: todaysec };

				var pipelines = [{ $match: todayvismatch }, { $group: { _id: "$" + ids._mtid } }, {
					$group: {
						_id: null, count: { $sum: 1 }
					}
				}];
				me.db.collection(me.prefix + appid).aggregate(pipelines, function (err, res) {
					if (err) throw err;
					if (res.length === 0)
						dashboard.n_returning_visitor = 0;
					else dashboard.n_returning_visitor = res[0].count;

					// 2 number of returning visitor
					todayvismatch[ids._ctime] = { $lt: todaysec };
					me.db.collection(me.prefix + appid).aggregate(pipelines, function (err, res) {
						if (err) throw err;
						if (res.length === 0)
							dashboard.n_new_visitor = 0;
						else dashboard.n_new_visitor = res[0].count;

						// 3 retenstion rate
						me.getRetensionRate(me.db, me.prefix, appid, ids, function (retention_rates: number[]) {
							dashboard.retention_rates\ = retention_rates;
						});








					});
				});

			});
		}

		me.db.collection(me.prefix + "dashboard").find({ appid: appid }).limit(1).toArray(function (err, res) {
			if (err) throw err;
			// cache not existed
			if (res.length === 0) {
				me.lock("c_" + appid, function (release) {
					// recheck because cache could be create after the lock
					me.db.collection(me.prefix + "dashboard").find({ appid: appid }).limit(1).toArray(function (err, res) {
						if (err) throw err;
						if (res.length === 0) {

							generateDashboard(function (dash: DashboardEntity) {
								callback(dash);
								dash.appid = appid + "";
								dash.ctime = Math.round(new Date().getTime() / 1000);
								me.db.collection(me.prefix + "dashboard").updateOne({ appid: appid + "" }, dash, { upsert: true }, function (err) {
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

			if (deltaT > me.delaysec) {
				me.lock(appid + "", function (release) {
					me.db.collection(me.prefix + "dashboard").find({ appid: appid }).limit(1).toArray(function (err, res) {
						if (err) throw err;
						// this is a must found
						var dash = res[0];
						var deltaT = Math.round(new Date().getTime() / 1000) - dash.ctime;
						if (deltaT > me.delaysec) {
							// refresh the cache
							generateDashboard(function (dash: DashboardEntity) {
								callback(dash);
								dash.appid = appid + "";
								dash.ctime = Math.round(new Date().getTime() / 1000);
								me.db.collection(me.prefix + "dashboard").update({ appid: appid + "" }, dash, { upsert: true }, function (err) {
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
		}
}
