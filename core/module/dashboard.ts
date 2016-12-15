import * as mongo from 'mongodb';
import * as referer from './referer';

export class DashboardEntity {
	public ctime: number;
	public endtime: number;
	public starttime: number;
	public appid: string;
	public n_new_visitor: number;
	public n_returning_visitor: number;
	public n_new_signup: number;
	public n_avgcartsize: number;
	public total_revenue: number;
	public revenues: number[];
	public n_purchases: number[];
	public revenue_per_customer: number;
	public retention_rate: number;
	public usergrowth_rate: number;
	public conversion_rate: number;
	public highest_revenue_campaign: string;
	public most_effective_ref: string;
	public most_popular_category: string;
	public labels: string[];
	public traffic24: number[];
	public traffic24labels: string[];
}
export class Dashboard {
	private Lock = require('lock');
	private lock = this.Lock();

	private N: number = 12; //max number of node

	//get time scale between two date
	private getTimeRange(starttime: number, endtime: number): number[] {
		// make sure starttime and endtime are number
		starttime = parseInt(starttime + "");
		endtime = parseInt(endtime + "");
		if (endtime < starttime) throw "wrong time input";
		var me = this;
		var ret = [];

		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		starttime = Math.round(startday.getTime() / 1000);
		endtime = Math.round(endday.getTime() / 1000);

		var daydiff = Math.floor((endtime - starttime) / 86400);
		if (daydiff == 0) return [endtime - 86400, endtime];
		if (daydiff == 1) return [starttime, endtime];

		// if not enough day for node, then return all day
		if (daydiff + 1 <= me.N) {
			ret.push(starttime);
			var st = starttime;

			while (st + 86400 < endtime) {
				st += 86400;
				ret.push(st);
			}
			ret.push(endtime);
			return ret;
		}

		// return specific days in all day
		// arary alway start with starttime and end with endtime
		var d = daydiff / (me.N - 1);
		ret.push(starttime);
		for (var i = 1; i < me.N - 1; i++)
			ret.push(starttime + Math.round(i * d * 86400));
		ret.push(endtime);
		return ret;
	}

	public constructor(private db: mongo.Db, private converter, private prefix: string, private ref: referer.RefererType, private delaysec: number) {
	}

	public getSignup(appid: string, starttime:number, endtime:number, callback: (ret) => void) {
	let me = this;
	var ret = {}
		 me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "userid"], function (ids) {
							me.getNewSignup(me.db, me.prefix, appid, ids, starttime, endtime, function (su) {
									ret['signup'] = su;
									callback(ret);
								});
						});
		}

	private getTraffic24(db: mongo.Db, prefix: string, appid: string, ids, callback: (data: number[], labels: string[]) => void) {
		var now = new Date();

		var nowsec = Math.round(now.getTime() / 1000);
		var label = ['24', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12',
			'13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];

		// xac dinh gio
		var curhour = now.getHours();
		var queryCont = [];
		var labelarr = [];
		// get Rangue query
		for (var i = 0; i < 24; i++) {
			var curlabel = label[(curhour - i + 24) % 24];
			labelarr.push(curlabel);
			queryCont.push({
        $cond: [{
					$and: [
						{ $lte: ["$" + ids._ctime, nowsec - i * 3600] },
						{ $gt: ["$" + ids._ctime, nowsec - (i + 1) * 3600] }
					]
				}, curlabel, ""]
      });
		}

		var queryMatch = {};
		queryMatch[ids._ctime] = { $gt: nowsec - 24 * 3600 , $lte: nowsec};
		var query = [{ $match: queryMatch }, {
			$project: { "_id": 0, _mtid: 1, hour: { $concat: queryCont } }
		}, {
				$group: { _id: { hour: "$hour", _mtid: "$" + ids._mtid } }
			}, {
				$group: { _id: "$_id.hour", count: { $sum: 1 } }
			}];
		
		db.collection(prefix + "app" + appid).aggregate(query, function (err, res) {
			if (err) throw err;
			var data = [];
			for (var i = 0; i < 24; i++)
			{
				var value = 0;
				for (var j = 0; j < res.length; j++)
				{
					if (res[j]._id === label[(curhour - i + 24) % 24])
					{
						value = res[j].count;
						break;
					}
				}
				data.push(value);
			}
			// var a = [];
			// var b = [];
			// for(var item in data){
			// 	a.push(data[data.length- item -1]);
			// 	b.push(labelarr[labelarr.length - item -1]);
			// }
			// console.log(a.reverse(), b.reverse());
			// console.log(data.reverse(), data);
			// console.log(labelarr.reverse(), labelarr);
			callback(data.reverse(), labelarr.reverse());
		});
	}

	private getGrowthRate(db: mongo.Db, prefix: string, appid: string, ids, starttime:number, endtime:number, callback: (a: number) => void) {
		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;
		var query = {};
		query[ids._stime] = {
			$lte: startsec
		}
		query[ids._isUser] = true;
		//count number of user at starttime
		db.collection(prefix + "app" + appid).count(query, function (err, c) {
			if (err) throw err;
			var startc = c;
			if(startc==0){
				return callback(0);
			}
			query[ids._stime] = {
				$gt: startsec, $lte: endsec
			}
			db.collection(prefix + "app" + appid).count(query, function (err, c) {
				if (err) throw err;
				var deltac = c;
				if (deltac + startc == 0) return callback(0);
				else return callback(deltac * 100/ startc );
			});
		});
	}

	private getTotalRevenue(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endttime: number, callback: (revenue: number, npurchase: number, nuser: number) => void) {
		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endttime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		var revenue_pipeline = [{ $match: {} }, {
			$group: {
				_id: "$" + ids._mtid, sum: { $sum: "$" + ids.amount }, purchase: { $sum: 1 }
			}
		}, {
				$group: {
					_id: null, sum: { $sum: "$sum" }, count: { $sum: 1 }, npurchase: { $sum: "$purchase" }
				}
			}];
		revenue_pipeline[0]['$match'][ids._isUser] = { $exists: false };
		revenue_pipeline[0]['$match'][ids._typeid] = {$regex: /^purchase/i};
		revenue_pipeline[0]['$match'][ids._ctime] = { $gte: startsec, $lt: endsec };
		db.collection(prefix + "app" + appid).aggregate(revenue_pipeline, function (err, res) {
			if (err) throw err;
			var revenue = res.length === 0 ? 0 : res[0].sum;
			var nuser = res.length === 0 ? 0 : res[0].count;
			var npurchase = res.length === 0 ? 0 : res[0].npurchase;
			callback(revenue, npurchase, nuser);
		});
	}

	private getRevenue(db: mongo.Db, prefix: string, appid: string, ids, time: number, callback: (revenue: number, numberofpurchase: number) => void) {
		var now = new Date(time * 1000);
		var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let todaysec = Math.round(today.getTime() / 1000);
		var revenue_pipeline = [{ $match: {} }, {
			$group: {
				_id: null, sum: { $sum: "$" + ids.amount }, count: { $sum: 1 }
			}
		}];

		revenue_pipeline[0]['$match'][ids._isUser] = { $exists: false };
		revenue_pipeline[0]['$match'][ids._typeid] = {$regex: /^purchase/i};
		revenue_pipeline[0]['$match'][ids._ctime] = { $gte: todaysec, $lt: todaysec + 86400 };
		db.collection(prefix + "app" + appid).aggregate(revenue_pipeline, function (err, res) {
			if (err) throw err;
			var revenue = res.length === 0 ? 0 : res[0].sum;
			var n_purchase = res.length === 0 ? 0 : res[0].count;
			callback(revenue, n_purchase);
		});
	}



	private getRevenues(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endtime: number, callback: (revenues: number[], purchases: number[]) => void) {
		let me = this;
		var revenues = [];
		var purchases = [];
		var time = me.getTimeRange(starttime, endtime);
		var c = time.length;
		for (let i = 0; i < time.length; i++) {
			me.getRevenue(db, prefix, appid, ids, time[i], function (revenue, purchase) {
				c--;
				revenues[i] = revenue;
				purchases[i] = purchase;
				if (c == 0) {
					callback(revenues, purchases);
				}
			});
		}
	 }

	private getRetensionRate(db: mongo.Db, prefix: string, appid: string, ids, callback: (rate: number) => void) {
		var me = this;
		var now = new Date();
		var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let todaysec = Math.round(today.getTime() / 1000);
		let b6 = todaysec - 24 * 6 * 3600;

		var n_newuser, n6_user;

		let alltimecount = {};
		alltimecount[ids._isUser] = true;
		alltimecount[ids._ctime] = { $lt: b6 };
		db.collection(prefix + "app" + appid).count(alltimecount, function (err, res) {
			if (err) throw err;
			n6_user = res;
			if (n6_user == 0) return callback(0);
			alltimecount[ids._ctime] = { $gte: b6 };
			db.collection(prefix + "app" + appid).count(alltimecount, function (err, res) {
				if (err) throw err;
				n_newuser = res;
				//get today user visit
				var todayuservisitq = [{}, {}, {}, {}, {}];
				todayuservisitq[0]['$match'] = { $or: [{}, {}] };
				todayuservisitq[0]['$match']['$or'][0][ids._isUser] = true;
				todayuservisitq[0]['$match']['$or'][1][ids._isUser] = { $exists: false };
				todayuservisitq[0]['$match']['$or'][1][ids._ctime] = { $gte: todaysec };
				todayuservisitq[1]['$project'] = {};
				todayuservisitq[1]['$project'][ids._mtid] = 1;
				todayuservisitq[1]['$project'][ids.userid] = { $cond: { if: { $ifNull: ["$userid", false] }, then: 1, else: 0 } };
				todayuservisitq[2]['$group'] = { _id: "$" + ids._mtid, userid: { $sum: "$" + ids.userid }, count: { $sum: 1 } };
				todayuservisitq[3]['$match'] = { userid: { $gt: 0 }, count: { $gt: 1 } };
				todayuservisitq[4]['$group'] = { _id: null, count: { $sum: 1 } };
				
				me.db.collection(me.prefix + "app" + appid).aggregate(todayuservisitq, function (err, res) {
					if (err) throw err;
					var count = 0;
					if (res.length !== 0)
						count = res[0].count;
					var rate = (count - n_newuser) / n6_user * 100;
					callback(rate);
				});
			});

		});
	}

	private getConversionRate(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endtime: number, callback: (a: number) => void) {
		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		let allvisitor = {};
		allvisitor[ids._isUser] = { $exists: false };
		allvisitor[ids._ctime] = { $gte: startsec, $lt: endsec };

		var piplelines = [{ $match: allvisitor }, { $group: { _id: "$" + ids._mtid } }, { $group: { _id: null, count: { $sum: 1 } } }];

		//get unique visitor
		db.collection(prefix + "app" + appid).aggregate(piplelines, function (err, res) {
			if (err) throw err;
			var nuservisit = res.length === 0 ? 0 : res[0].count;

			piplelines[0]['$match'] = {};
			piplelines[0]['$match'][ids._typeid] = {$regex: /^purchase/i};
			piplelines[0]['$match'][ids._isUser] = { $exists: false };
			piplelines[0]['$match'][ids._ctime] = { $gte: startsec, $lt: endsec };

			db.collection(prefix + "app" + appid).aggregate(piplelines, function (err, res) {
				if (err) throw err;
				var nuserpurchase = res.length === 0 ? 0 : res[0].count;
				var conversionrate = nuservisit == 0 ? 0 : nuserpurchase / nuservisit * 100;
				callback(conversionrate);
			});

		});
	}

	public getMostPopulerCategory(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endtime: number, callback: (cat: string) => void) {
		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		let pipeline = [
			{ $match: {} }, {
				$group: {
					_id: "$" + ids.cid,
					cname: { $first: "$" + ids.cname },
					revenue: { $sum: "$" + ids.amount }
				}
			}, {
				$sort: { revenue: -1 }
			}, {
				$limit: 1
			}];

		pipeline[0]['$match'][ids._typeid] = {$regex: /^purchase/i};
		pipeline[0]['$match'][ids._ctime] = { $gte: startsec, $lt: endsec };

		db.collection(prefix + "app" + appid).aggregate(pipeline, function (err, res) {
			if (err) throw err;
			if (res.length == 0) return callback(undefined);
			return callback(res[0].cname);
		});
	}

	public getHighestRevenueCampaign(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endtime: number, callback: (cat: string) => void) {
		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		let pipeline = [
			{ $match: {} }, { $unwind: "$" + ids._utm_campaign }, {
				$group: {
					_id: "$" + ids._utm_campaign,
					revenue: { $sum: "$" + ids.amount }
				}
			}, {
				$sort: { revenue: -1 }
			}, {
				$limit: 1
			}];

		pipeline[0]['$match'][ids._typeid] = {$regex: /^purchase/i};
		pipeline[0]['$match'][ids._ctime] = { $gte: startsec, $lt: endsec };
		pipeline[0]['$match'][ids._utm_campaign] = { $exists: true, $ne: null };
		db.collection(prefix + "app" + appid).aggregate(pipeline, function (err, res) {
			if (err) throw err;
			if (res.length == 0) return callback(undefined);
			return callback(res[0]._id);
		});
	}

	public getMostEffectiveReferal(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endtime: number, callback: (ref: string) => void) {
		var me = this;
		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		let pipeline = [
			{ $match: {} }, {
				$group: {
					_id: "$" + ids._reftype,
					count: { $sum: 1 }
				}
			}, {
				$sort: { count: -1 }
			}, {
				$limit: 1
			}];

		pipeline[0]['$match'][ids._typeid] = {$regex: /^purchase/i};
		pipeline[0]['$match'][ids._ctime] = { $gte: startsec, $lt: endsec };
		db.collection(prefix + "app" + appid).aggregate(pipeline, function (err, res) {
			if (err) throw err;
			if (res.length == 0) return callback(undefined);
			return callback(me.ref.getTypeName(res[0]._id));
		});
	}

	public generateLabel(startime: number, endtime: number,callback) {
		let me = this;
		if (startime === undefined) {
			endtime = Math.floor(new Date().getTime() / 1000);
			startime = endtime - 30 * 86400;
		}

		var now = new Date(startime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		var labels = [];
		var ranges = this.getTimeRange(startime, endtime);
		for (var i of ranges) {
			var date = new Date(i * 1000);
			labels.push(date.toUTCString().split(' ')[2] + " " + date.getDate());
		}
		callback(labels);
	 }

	public getPageview(appid: string, callback:(ret)=>void) {
		let me = this;
		var ret = {}
		me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "userid"], function (ids) {
			me.getTodayVisitor(me.db, me.prefix, appid, ids, function (n_new_visitor, n_returning_visitor) {
				ret['newVisitors'] = n_new_visitor;
				ret['returningVisitors'] = n_returning_visitor;
				callback(ret);
			});
		});
	}


	public getTodayVisitor(db: mongo.Db, prefix: string, appid: string, ids, callback: (n_new_visitor, n_returning_visitor) => void) {
		var now = new Date();
		var n_returning_visitor, n_new_visitor;
				var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

				let todaysec = Math.round(today.getTime() / 1000);
				//1 number of new visitor today
				var todayvismatch = {};
				todayvismatch[ids._ctime] = { $gte: todaysec };

				var pipelines = [{ $match: todayvismatch }, { $group: { _id: "$" + ids._mtid } }, {
			$group: {
				_id: null, count: { $sum: 1 }
			}
				}];
				db.collection(prefix + "app" + appid).aggregate(pipelines, function (err, res) {
			if (err) throw err;
			if (res.length === 0)
				n_returning_visitor = 0;
			else n_returning_visitor = res[0].count;

			// 2 number of returning visitor
			todayvismatch[ids._isUser] = true;
			todayvismatch[ids._ctime] = { $gte: todaysec };

			db.collection(prefix + "app" + appid).count(todayvismatch, function (err, res) {
				if (err) throw err;
				n_new_visitor = res;
				n_returning_visitor -= res;
				callback(n_new_visitor, n_returning_visitor);
			});
				});
	}

	private getNewSignup(db: mongo.Db, prefix: string, appid: string, ids, starttime: number, endtime: number, callback: (a: number) => void) {

		var now = new Date(starttime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		let me = this;
		let match = {};
		match[ids._ctime] = { $gte: startsec, $lt: endsec };
		match[ids._typeid] = 'register';
		match[ids._isUser] = { $exists: false };
		db.collection(prefix + "app" + appid).count(match, function (err, res) {
			if (err) throw err;
			callback(res);
		});
	}

	public getRevenuesTime(appid: string, startime: number, endtime: number,callback){
		let me = this;
		if (startime === undefined) {
			endtime = Math.floor(new Date().getTime() / 1000);
			startime = endtime - 30 * 86400;
		}

		var now = new Date(startime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;
		me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
			me.getRevenues(me.db, me.prefix, appid, ids, startime, endtime, function (revenues, n_purchases) {
				var revenue = {};
				revenue.revenues = revenues;
				revenue.n_purchases = n_purchases;
				callback(revenue);
			});
		});

	}

	public getTraffic24Time(appid: string,callback){
		let me = this;
		me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
			me.getTraffic24(me.db, me.prefix, appid, ids, function (data, labels) {
				var traffic = {};
				traffic.traffic24 = data;
				traffic.traffic24labels = labels;
				callback(traffic);
			});
		});
	}

	public getGrowthRateTime(appid: string, startime: number, endtime: number,callback){
		let me = this;
		if (startime === undefined) {
			endtime = Math.floor(new Date().getTime() / 1000);
			startime = endtime - 30 * 86400;
		}

		var now = new Date(startime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
			me.getGrowthRate(me.db, me.prefix, appid, ids, startime, endtime, function (growrate: number) {
				growrate = Math.round(growrate*100)/100;
				callback(growrate);
			});
		});
	}

	public getRetensionRateTime(appid: string,callback){
			let me = this;
			me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
				me.getRetensionRate(me.db, me.prefix, appid, ids, function (rate:number) {
					callback(rate);
				});
			});
	}

	public getTotalRevenueTime(appid: string, startime: number, endtime: number,callback){
			let me = this;
			if (startime === undefined) {
				endtime = Math.floor(new Date().getTime() / 1000);
				startime = endtime - 30 * 86400;
			}

			var now = new Date(startime * 1000);
			var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			now = new Date(endtime * 1000);
			var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			let startsec = Math.round(startday.getTime() / 1000);
			let endsec = Math.round(endday.getTime() / 1000) + 86400;

			me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
				me.getTotalRevenue(me.db, me.prefix, appid, ids, startime, endtime, function (revenue, npurchase, nuser) {
					var data = {};
					data.total_revenue = revenue;
					data.n_avgcartsize = npurchase === 0 ? 0 : revenue / npurchase;
					data.revenue_per_customer = nuser === 0 ? 0 : revenue / nuser;
					callback(data);
				});
			});
		}

	// me.getConversionRate(me.db, me.prefix, appid, ids, startime, endtime, function (cs: number) {
	// // 									dashboard.conversion_rate = cs;

	public getConversionRateTime(appid: string, startime: number, endtime: number,callback){
		let me = this;
		if (startime === undefined) {
			endtime = Math.floor(new Date().getTime() / 1000);
			startime = endtime - 30 * 86400;
		}

		var now = new Date(startime * 1000);
		var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		now = new Date(endtime * 1000);
		var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		let startsec = Math.round(startday.getTime() / 1000);
		let endsec = Math.round(endday.getTime() / 1000) + 86400;

		me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
			me.getConversionRate(me.db, me.prefix, appid, ids, startime, endtime, function (cs: number) {
				// dashboard.conversion_rate = cs;
				callback(cs);
			});
		});
	}
				
	// public getDashboard(appid: string, startime: number, endtime: number, callback: (d: DashboardEntity) => void) {
	// 	let me = this;
	// 	if (startime === undefined) {
	// 		endtime = Math.floor(new Date().getTime() / 1000);
	// 		startime = endtime - 30 * 86400;
	// 	}
    //
	// 	var now = new Date(startime * 1000);
	// 	var startday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
	// 	now = new Date(endtime * 1000);
	// 	var endday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
	// 	let startsec = Math.round(startday.getTime() / 1000);
	// 	let endsec = Math.round(endday.getTime() / 1000) + 86400;
    //
	// 	function generateDashboard(gcallback: (d: DashboardEntity) => void) {
	// 		var dashboard: DashboardEntity = new DashboardEntity();
    //
	// 		dashboard.labels = me.generateLabel(startime, endtime);
	// 		me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
	// 			me.getTodayVisitor(me.db, me.prefix, appid, ids, function (n_new_visitor, n_returning_visitor) {
	// 				dashboard.n_new_visitor = n_new_visitor;
	// 				dashboard.n_returning_visitor = n_returning_visitor;
	// 				me.getNewSignup(me.db, me.prefix, appid, ids, startime, endtime, function (ret) {
	// 					dashboard.n_new_signup = ret;
    //
	// 					me.getRevenues(me.db, me.prefix, appid, ids, startime, endtime, function (revenues, n_purchases) {
	// 						dashboard.revenues = revenues;
	// 						dashboard.n_purchases = n_purchases;
    //
	// 						me.getTotalRevenue(me.db, me.prefix, appid, ids, startime, endtime, function (revenue, npurchase, nuser) {
	// 							dashboard.total_revenue = revenue;
	// 							dashboard.n_avgcartsize = npurchase === 0 ? 0 : revenue / npurchase;
	// 							dashboard.revenue_per_customer = nuser === 0 ? 0 : revenue / nuser;
    //
	// 							me.getGrowthRate(me.db, me.prefix, appid, ids, startime, endtime, function (growrate: number) {
	// 								dashboard.usergrowth_rate = growrate;
    //
	// 								me.getConversionRate(me.db, me.prefix, appid, ids, startime, endtime, function (cs: number) {
	// 									dashboard.conversion_rate = cs;
    //
	// 									me.getRetensionRate(me.db, me.prefix, appid, ids, function (rate: number) {
	// 										dashboard.retention_rate = rate;
	// 										me.getHighestRevenueCampaign(me.db, me.prefix, appid, ids, startime, endtime, function (campaign: string) {
	// 											dashboard.highest_revenue_campaign = campaign;
	// 											me.getMostPopulerCategory(me.db, me.prefix, appid, ids, startime, endtime, function (cat: string) {
	// 												dashboard.most_popular_category = cat;
	// 												me.getMostEffectiveReferal(me.db, me.prefix, appid, ids, startime, endtime, function (ref: string) {
	// 													dashboard.most_effective_ref = ref;
	// 													me.getTraffic24(me.db, me.prefix, appid, ids, function (data, labels) {
    //
	// 														dashboard.traffic24 = data;
	// 														dashboard.traffic24labels = labels;
    //
	// 														gcallback(dashboard);
	// 													});
	// 												});
	// 											});
	// 										});
	// 									});
	// 								});
	// 							});
	// 						});
	// 					});
	// 				});
	// 			});
	// 		});
	// 	}
    //
	// 	me.db.collection(me.prefix + "dashboard").find({ appid: appid, endtime: endsec, starttime: startsec }).limit(1).toArray(function (err, res) {
	// 		if (err) throw err;
	// 		// cache not existed
	// 		if (res.length === 0) {
	// 			me.lock("c_" + appid + "-" + startsec + "-" + endsec, function (release) {
	// 				// recheck because cache could be create after the lock
	// 				me.db.collection(me.prefix + "dashboard").find({ appid: appid, endtime: endsec, starttime: startsec }).limit(1).toArray(function (err, res) {
	// 					if (err) throw err;
	// 					if (res.length === 0) {
	// 						generateDashboard(function (dash: DashboardEntity) {
	// 							callback(dash);
	// 							dash.appid = appid + "";
	// 							dash.ctime = Math.round(new Date().getTime() / 1000);
	// 							dash.starttime = startsec;
	// 							dash.endtime = endsec;
	// 							me.db.collection(me.prefix + "dashboard").updateOne({ appid: appid + "", endtime: endsec, starttime: startsec }, dash, { upsert: true }, function (err) {
	// 								release()();
	// 								if (err) throw err;
	// 							});
	// 						});
	// 					} else {
	// 						return release(function () {
	// 							// why not just do callback(res[0]) ?
	// 							// this may look wasted, but give us 100% guarranty that,
	// 							// the result dashboard alway up to date
	// 							// the problem with res[0] is that, is very likely up to date
	// 							// (deltatime < delay) but there is no warranty
	// 							me.getDashboard(appid, startime, endtime, callback);
	// 						})();
	// 					}
	// 				});
	// 			});
	// 			return;
	// 		}
    //
	// 		var dash: DashboardEntity = res[0];
	// 		var deltaT = Math.round(new Date().getTime() / 1000) - dash.ctime;
    //
	// 		if (deltaT > me.delaysec) {
	// 			me.lock("c_" + appid + "-" + startsec + "-" + endsec, function (release) {
	// 				me.db.collection(me.prefix + "dashboard").find({ appid: appid, endtime: endsec, starttime: startsec }).limit(1).toArray(function (err, res) {
	// 					if (err) throw err;
	// 					// this is a must found
	// 					var dash = res[0];
	// 					var deltaT = Math.round(new Date().getTime() / 1000) - dash.ctime;
	// 					if (deltaT > me.delaysec) {
	// 						// refresh the cache
	// 						generateDashboard(function (dash: DashboardEntity) {
	// 							callback(dash);
	// 							dash.appid = appid + "";
	// 							dash.ctime = Math.round(new Date().getTime() / 1000);
	// 							dash.starttime = startsec;
	// 							dash.endtime = endsec;
	// 							me.db.collection(me.prefix + "dashboard").updateOne({ appid: appid + "", endtime: endsec, starttime: startsec }, dash, { upsert: true }, function (err) {
	// 								release(function () {
	// 								})();
	// 								if (err) throw err;
	// 							});
	// 						});
	// 					} else
	// 						return release(function () {
	// 							// callback(dash);
	// 							// again, why not use callback(dash) ? because i want 100% guarranty that the dash
	// 							// is up to date (deltatime < delay)
	// 							me.getDashboard(appid, startime, endtime, callback);
	// 						})();
	// 				});
	// 			});
	// 		} else {
	// 			me.converter.toIDs(["_isUser", "_mtid", "_ctime", "_typeid", "_reftype", "userid", "cname", "amount", "cid", "_stime", "_utm_campaign"], function (ids) {
	// 				me.getNewSignup(me.db, me.prefix, appid, ids, startime, endtime, function (newsignup) {
	// 					me.getTodayVisitor(me.db, me.prefix, appid, ids, function (newvistor, returning) {
	// 						dash.n_new_signup = newsignup;
	// 						dash.n_returning_visitor = returning;
	// 						dash.n_new_visitor = newvistor;
	// 						callback(dash);
	// 					});
	// 				});
	// 			});
	// 		}
	// 	});
	// }
}
