import * as mongodb from 'mongodb';
import * as url from 'url';
import * as express from 'express';
import * as referer from './referer';

import * as location from './location';

export class ActionMgr {
	private location: location.LocationMgr;
	constructor(private db: mongodb.Db, private converter, private prefix: string, private mapping: string, private valuemgr, private referer: referer.RefererType) {
		this.location = new location.LocationMgr(db, prefix);
	}

	// purpose: check if an mtid is valid
	// a mtid is valid if there is one user record based on mtid
	// if a mtid is ano-mtid, convert it to iden-mtid
	public ismtidValid(appid: string, mtids: string, callback) {
		let collection = this.prefix + "app" + appid;
		let collectionmapping = this.prefix + this.mapping;
		let mtid = new mongodb.ObjectID(mtids);
		var me = this;

		me.converter.toID('_isUser', function (isUser) {

						me.db.collection(collectionmapping).find({ anomtid: mtid }).limit(1).toArray(function (err, r) {
				if (r.length !== 0) mtid = r[0].idemtid;
				// check if user existed
				var query = { _id: mtid };
				query[isUser] = true;
				me.db.collection(collection).find(query).limit(1).toArray(function (err, ret) {
					if (ret.length === 0) callback(false);
					else callback(true);
				});
						});
		});
	};

	// purpose: record an action rawly
	// param:
	// + _deltat: number of delayed second before request sent
	// + _mtid : number
	// + _typeid: string // type of action, eg: "purchase", "pageview"
	// + _osid: number // os information, eg: "window", "linux",
	// + _browser : number // eg, "chrome", "firefox", "opera",
	// + _location : number // location code, eg: Hanoi, Vietnam
	// + _ref: string
	// + _device : number
	// + _ip: string // public ip address
	// + _scrres: string // screen resolution of the device
	// + _totalsec: number // total number of sec to finish the action
	// + _url: string
	// + _browserversion : number
	// + _osversion : number
	// + data1
	// + data2
	// + data3
	public saveRaw(appid, data, callback) {
		console.log("=====save raw");
		let me = this;
		var collection = me.db.collection(this.prefix + "app" + appid);
		var collectionmapping = this.prefix + this.mapping;
		var mtid = new mongodb.ObjectID(data._mtid);
		data._mtid = mtid;

		data._segments = [];

		// correct timming
		data._ctime = Math.round(new Date().getTime() / 1000) - (parseInt(data._deltat) ? parseInt(data._deltat) : 0);
		delete data._deltat;

		if (data._link !== undefined) {
			collection.find({_id: new mongodb.ObjectID(data._link)}).limit(1).toArray(function (err, ret) {
				if (err) throw err;
				if (ret.length == 0) throw "link not found: " + data._link + ", in app: " + appid;
				let link = ret[0];
				me.converter.toIDs(['_utm_source', '_utm_campaign', '_utm_term', '_utm_content', '_utm_medium'], function (ids) {
					if (data._utm_source == undefined) data._utm_source = link[ids._utm_source];
					if (data._utm_campaign == undefined) data._utm_campaign = link[ids._utm_campaign];
					if (data._utm_term == undefined) data._utm_term = link[ids._utm_term];
					if (data._utm_content == undefined) data._utm_content = link[ids._utm_content];
					if (data._utm_medium == undefined) data._utm_medium = link[ids._utm_meidum];
					lastsave();
				});
			});
		}
		else
			lastsave();


		function lastsave() {
			console.log("=====lastsave");
			// retrive real mtid because user can still use old mtid
			me.db.collection(collectionmapping).find({ anomtid: mtid }).limit(1).toArray(function (err, r) {
				if (err) throw err;
				if (r.length !== 0) mtid = r[0].idemtid;
				// set referal type
				data._reftype = me.referer.getTypeName(parseInt(me.referer.getRefType(data._url, data._ref)));
				me.valuemgr.cineObject(appid, data._typeid, data);
				me.converter.toObject(data, function (datax) {
					collection.insertOne(datax, function (err, r) {
						if (err) throw err;
						// update location
						me.location.parse(data._ip, function (res) {
							var loc = { _city: res.city, _country: res.country };
							me.valuemgr.cineObject(appid, data._typeid, loc);
							me.valuemgr.cineObject(appid, "user", loc);
							me.converter.toObject(loc, function (datax) {
								collection.update({ _id: r.insertedId }, { $set: loc }, function (err, r) {
									if (err) throw err;
								});
								collection.update({ _id: data._mtid }, { $set: loc }, function (err, r) {
									if (err) throw err;
								});
							});
						});

						callback(r.insertedId);
					});

					//get user infomation
					collection.find({ _id: mtid }).limit(1).toArray(function (err, ret) {
						if (err) throw err;
						var user = ret[0];
						if (user == null) throw "mtid " + mtid + " did not match any user";
						var typeid = data._typeid;
						me.converter.toIDs(['_revenue', '_firstcampaign', '_lastcampaign', '_campaign', '_ctime', '_mtid', '_reftype',
							'_segments', '_url', '_typeid', '_referer', '_totalsec', 'registed', '_reftype', 'lastactionid', '_ref',
																'_lang', '_os', '_browser', '_country', '_city', '_devicetype', '_deviceid',
																'_callback', '_numberPurchase', '_listProduct', '_deltat', 'actionid', '_lastSeen', '_utm_campaign'], function (ids) {
								// increase revenue
								var simpleprop = {};
								if (typeid === 'purchase') {
									if (user[ids._revenue] === undefined) user[ids._revenue] = 0;
									//get purchase amount
									if (data.quantity == undefined) data.quantity = 1;
									if (data.price == undefined) data.price = 0;
									if (data.amount == undefined) data.amount = data.price * data.quantity;
									datax[ids._revenue] = user[ids._revenue] + data.amount;

									// Increase number of purchasing
									if (user[ids._numberPurchase] == null) {
										user[ids._numberPurchase] = 0;
									}
									datax[ids._numberPurchase] = user[ids._numberPurchase] + 1;

									// Add purchase product up to 5
									if (user[ids._listProduct] == null) {
										user[ids._listProduct] = [];
									}
									if (user[ids._listProduct].length > 4) {
																			 user[ids._listProduct].shift();
									}
									datax[ids._listProduct] = user[ids._listProduct].push(data.pname);

								}
								if (typeid === 'pageview') {
									datax[ids._campaign] = datax[ids._utm_campaign];
									datax[ids._lastcampaign] = datax[ids._utm_campaign];
									datax[ids._firstcampaign] = datax[ids._utm_campaign];
								}

								if (typeid === 'register' || typeid === 'login') {
									simpleprop[ids.registed] = true;
								}

								// Add last active
								datax[ids._lastSeen] = Date.now();

								// update system-based prop
								// filter out unneeded array prop
								var arrayprop = {};
								for (var p in datax) if (datax.hasOwnProperty(p))
									if (p.startsWith('_'))
										arrayprop[p] = datax[p];

								// update non-system prop for user
								for (p in simpleprop) if (simpleprop.hasOwnProperty(p))
									if (p.startsWith('_') == false)
										arrayprop[p] = simpleprop[p];

								if (Object.keys(arrayprop).length !== 0)
										me.updateArrayBasedUserInfo(collection, mtid, ids, user, arrayprop);
							});
					});
				});
			});
		}
	}


	// purpose: add new data to arrays in user
	// param:
	// + collection: collection to query user information
	// + mtid: mongodb.ObjectID mtid of user
	// + data: data to be append to user
	private updateArrayBasedUserInfo(collection: mongodb.Collection, mtid: mongodb.ObjectID, ids, user, datax, callback?:()=>void) {
		// append new element to the array or create one
		var arr = [];
		user[ids._lastcampaign] = datax[ids._campaign];
			this.mergeInfo(datax,user, ids);
		delete datax[ids._mtid];
		delete datax[ids._ctime];
		delete datax[ids._segments];
		delete datax[ids._url];
		delete datax[ids._typeid];
		delete datax[ids._referer];
		delete datax[ids._totalsec];
		delete datax[ids._totalsec];
		delete datax[ids._deltat];
		delete datax[ids._callback];
		delete datax[ids._link];
		delete datax[ids.lastactionid];
		delete datax[ids.actionid];
	
		// remove unneed prop in user
		for (var p in user) if (user.hasOwnProperty(p)) {
			var found = false;
			for (var r in datax) if (datax.hasOwnProperty(r)) 
				if (r == p) {
					found = true;
					break;
				}
			if (found == false) delete user[p];
		}
	
		//sync data from datax to user
		for (var p in datax) if (datax.hasOwnProperty(p)) {

			if (p === ids._revenue || p === ids._numberPurchase || p === ids._listProduct || p === ids._lastSeen)
			{
				user[p] = datax[p];
				continue;
			}
			if (user[p] !== undefined) arr = user[p];
			else arr = [];

			if (arr instanceof Array === false) arr = [arr];

			 user[p] = arr.concat(datax[p]);
			// user[p] =[...new Set(user[p])];
			user[p] =[...Array.from(new Set(user[p]))];
			// user[p] =[...new Set(user[p])+''];

		}
			delete user._id;
		collection.update({ _id: mtid }, { $set: user }, function (err, r) {
			if (err) throw err;
			if (callback !== undefined) callback();
		});
	}

	// purpose: record an action
	// url: {appid}/r
	public save(req, res, callback) {
		var me = this;
		var data = req.body;
		var appid = req.params.appid;

		me.saveRaw(appid, data, function (actionid) {
			res.writeHead(200, { 'Content-Type': 'text/plain' });
			res.end(actionid);
			callback(actionid);
		});
	}

	// purpose: fix an existing action
	// param:
	// + appid: id of the app
	// + actionid: ObjectID, id of action
	// + data: action data
	public fixRaw(appid: string, actionids: string, lastactionidstr: string, data, callback: () => void) {
		console.log("=====fix raw");
		let me = this;
		if(actionids == null) return callback(); //wrong actionid
		let actionid = new mongodb.ObjectID(actionids);
		if (data._mtid) data._mtid = new mongodb.ObjectID(data._mtid);
		var collection = me.db.collection(me.prefix + "app" + appid);
		//make sure dont change typeid
		delete data._typeid;
			me.converter.toIDs(['_mtid','_utm_source', '_utm_campaign', '_utm_term', '_utm_content',
			'_utm_medium', '_revenue', '_firstcampaign', '_lastcampaign', '_campaign', '_ctime', '_mtid','_devicetype',
			'_segments', '_url', '_typeid', '_referer', '_totalsec', 'registed', '_reftype', '_deltat', 'actionid',
			'lastactionid', '_ref', '_callback', '_numberPurchase', '_listProduct'], function (ids) {
		if (lastactionidstr !== null && lastactionidstr !== undefined && lastactionidstr !== '') {
			let lastactionid = new mongodb.ObjectID(lastactionidstr);

			collection.find({ _id: lastactionid }).limit(1).toArray(function (err, r) {
				if (err) throw err;
				if (r.length == 0) throw "wrong last action id: " + lastactionidstr;
				let lastaction = r[0];
				data._link = lastactionid;
			
					if (data._utm_source == undefined) data._utm_source = lastaction[ids._utm_source];
					if (data._utm_campaign == undefined) data._utm_campaign = lastaction[ids._utm_campaign];
					if (data._utm_term == undefined) data._utm_term = lastaction[ids._utm_term];
					if (data._utm_content == undefined) data._utm_content = lastaction[ids._utm_content];
					if (data._utm_medium == undefined) data._utm_medium = lastaction[ids._utm_meidum];
					return store(ids);
				});
			}else{
			return store(ids);
		}
		});
		function store(ids) {
			console.log("=====store");
			//me.updateChainCampaign(appid, actionids, data);
			data._reftype = me.referer.getTypeName(parseInt(me.referer.getRefType(data._url, data._ref)));
			me.valuemgr.cineObject(appid, "pageview", data);
			// set referal type
			collection.find({_id: actionid}).limit(1).toArray(function(err,r){
						if(err) throw err;
						if(r.length ===0) throw "action not found 400: " + actionid;
						
						data._mtid = r[0][ids._mtid];
				console.log("data.mtid in store: "+ data._mtid);
				me.converter.toObject(data, function (datax) {
					collection.update({ _id: actionid }, { $set: datax }, function (err, r) {
						if (err) throw err;
						collection.find({ _id: data._mtid }).limit(1).toArray(function (err, r) {
							if (err) throw err;
							if (r.length == 0) throw "user not found 309: " + data._mtid;
								me.updateArrayBasedUserInfo(collection, data._mtid, ids, r[0], datax, function () {
									callback();
								});
						});
					});
				});

			});
		}
	}

	public x(req, res, callback) {
		var me = this;
		var data = req.params;
		console.log(req.actionid);
		if(req.actionid.length != 24){
			res.writeHead(200);
			res.end();
			callback();
		}else{
			var collection = me.prefix + "app" + req.appid;
			var actionid = new mongodb.ObjectID(req.actionid);
			me.converter.toIDs(['_ctime', 'totalsec'], function (ids) {
				var projection = {};
				projection[ids._ctime] = 1;
				me.db.collection(collection).find({ _id: actionid }, projection).limit(1).toArray(function (err, r) {
					if (err) throw err;
					if (r.length === 0) throw "not found pageview to close, actionid: " + actionid;
					var newaction = {};
					newaction[ids.totalsec] = data.sessiontime;// Math.round(new Date().getTime() / 1000) - (parseInt(data._deltat) ? parseInt(data._deltat) : 0) - r[0][ids._ctime];
					me.db.collection(collection).update({ _id: actionid }, { $set: newaction }, function (err, r) {
						if (err) throw err;
						res.writeHead(200);
						res.end();
						callback();
					});
				});
			});
		}
	}

	private mergeInfo(olduser, userx, ids)
		{
				if (olduser[ids._os] != null) {
							if (olduser[ids._os] instanceof Array) {
								if (userx[ids._os] != null)
									userx[ids._os] = olduser[ids._os].concat(userx[ids._os]);
							}
							else {
								if (userx[ids._os] != null)
									userx[ids._os] = [olduser[ids._os]].concat(userx[ids._os]);
							}
				}
				if(userx[ids._os]!= null && userx[ids._os] !== undefined)
					userx[ids._os] = [...Array.from((userx[ids._os]))];
						if (olduser[ids._segment] != undefined) {
							if (olduser[ids._segment] instanceof Array) {
								if (userx[ids._segment] != null)
									userx[ids._segment] = olduser[ids._segment].concat(userx[ids._segment]);
							}
							else {
								if (userx[ids._segment] != undefined)
									userx[ids._segment] = [olduser[ids._segment]].concat(userx[ids._segment]);
							}
						}
			if(userx[ids._segment]!= null && userx[ids._segment] !== undefined)
						userx[ids._segment] = [...Array.from((userx[ids._segment]))];


			if (olduser[ids._ref] != undefined) {
				if (olduser[ids._ref] instanceof Array) {
					if (userx[ids._ref] != null)
						userx[ids._ref] = olduser[ids._ref].concat(userx[ids._ref]);
				}
				else {
					if (userx[ids._ref] != undefined)
						userx[ids._ref] = [olduser[ids._ref]].concat(userx[ids._ref]);
				}
			}
			if(userx[ids._ref]!= null && userx[ids._ref] !== undefined)
				userx[ids._ref] = [...Array.from((userx[ids._ref]))];



						if (olduser[ids._devicetype] != undefined) {
							if (olduser[ids._devicetype] instanceof Array) {
								if (userx[ids._devicetype] != undefined)
									userx[ids._devicetype] = olduser[ids._devicetype].concat(userx[ids._devicetype]);
							}
							else {
								if (userx[ids._devicetype] != null)
									userx[ids._devicetype] = [olduser[ids._devicetype]].concat(userx[ids._devicetype]);
							}
						}
			if(userx[ids._devicetype]!= null && userx[ids._devicetype] !== undefined)
				userx[ids._devicetype] =[...Array.from((userx[ids._devicetype]))];

						if (olduser[ids._deviceid] != null) {
							if (olduser[ids._deviceid] instanceof Array) {
								if (userx[ids._deviceid] != null)
									userx[ids._deviceid] = olduser[ids._deviceid].concat(userx[ids._deviceid]);
							}
							else {
								if (userx[ids._deviceid] != null)
									userx[ids._deviceid] = [olduser[ids._deviceid]].concat(userx[ids._deviceid]);
							}
						}
			if(userx[ids._deviceid]!= null && userx[ids._deviceid] !== undefined)
				userx[ids._deviceid] = [...Array.from((userx[ids._deviceid]))];

						if (olduser[ids._lang] != undefined) {
							if (olduser[ids._lang] instanceof Array) {
								if (userx[ids._lang] != null)
									userx[ids._lang] = olduser[ids._lang].concat(userx[ids._lang]);
							}
							else {
								if (userx[ids._lang] != undefined)
									userx[ids._lang] = [olduser[ids._lang]].concat(userx[ids._lang]);
							}
						}
			if(userx[ids._lang]!= null && userx[ids._lang] !== undefined)
				userx[ids._lang] = [...Array.from((userx[ids._lang]))];

						if (olduser[ids._city] != undefined) {
							if (olduser[ids._city] instanceof Array) {
								if (userx[ids._city] != undefined)
									userx[ids._city] = olduser[ids._city].concat(userx[ids._city]);
							}
							else {
								if (userx[ids._city] !== undefined || userx[ids._city] !== null)
									userx[ids._city] = [olduser[ids._city]].concat(userx[ids._city]);
							}
						}
			if(userx[ids._city]!= null && userx[ids._city] !== undefined)
				userx[ids._city] = [...Array.from((userx[ids._city]))];

						if (olduser[ids._country] != undefined) {
							if (olduser[ids._country] instanceof Array) {
								if (userx[ids._country] != undefined)
									userx[ids._country] = olduser[ids._country].concat(userx[ids._country]);
							}
							else {
								if (userx[ids._country] != undefined)
									userx[ids._country] = [olduser[ids._country]].concat(userx[ids._country]);
							}
						}
			if(userx[ids._country]!= null && userx[ids._country] !== undefined)
				userx[ids._country] = [...Array.from((userx[ids._country]))];

						if (olduser[ids._browser] != undefined) {
							if (olduser[ids._browser] instanceof Array) {
								if (userx[ids._browser] != undefined)
									userx[ids._browser] = olduser[ids._browser].concat(userx[ids._browser]);
							}
							else {
									if (userx[ids._browser] != undefined)
									userx[ids._browser] = [olduser[ids._browser]].concat(userx[ids._browser]);
							}
						}
			if(userx[ids._browser]!= null && userx[ids._browser] !== undefined)
				userx[ids._browser] = [...Array.from((userx[ids._browser]))];

						if (olduser[ids._campaign] != undefined) {
							if (olduser[ids._campaign] instanceof Array) {
								if (userx[ids._campaign] != undefined)
									userx[ids._campaign] = olduser[ids._campaign].concat(userx[ids._campaign]);
							}
							else {
								if (userx[ids._campaign] != undefined)
									userx[ids._campaign] = [olduser[ids._campaign]].concat(userx[ids._campaign]);
							}
							if(userx[ids._campaign]!= null && userx[ids._browser] !== undefined)
							userx[ids._campaign] = [...Array.from((userx[ids._campaign]))];
						}


						userx[ids._utm_campaign] = [].concat(userx[ids._utm_campaign]).concat(olduser[ids._utm_campaign]);
			if(userx[ids._utm_campaign]!= null && userx[ids._utm_campaign] !== undefined)
						userx[ids._utm_campaign] =  [...Array.from((userx[ids._utm_campaign]))];

						if (userx[ids._firstcampaign] == undefined) userx[ids._firstcampaign] = olduser[ids._firstcampaign];
												
						if (userx[ids._lastcampaign] == undefined) userx[ids._lastcampaign] = olduser[ids._lastcampaign];

}


	// purpose: identify or update info of an visitor
	// param:
	//  data.mtid: string, //mtid của anonymous user
	//  data.user: {[userid], name, email, age, birth, gender, ...}
	// condition:
	// + case 1 : userid exist, iden-mtid is equal mtid
	//         client want to update info of an existing user
	//         just update the info based on mtid.
	// + case 2: user.userid exist, iden-mtid (mtid found by user.userid) is not equals to mtid
	//         mtid is now an ano-mtid (mtid for an anonymous visitor)
	//         add a mapping beetwen ano-mtid and ide-mtid, after this
	//         all action done by ano-mtid is converted to ide-mtid
	//         update info, delete ano-mtid user record if existed
	// + case 3: user.userid doesn't exist
	//         client want identify ano-mtid into registed user
	//         in this case, create new user with ide-mtid equal ano-mtid.
	//         just simply add userid field to old ano-mtid record, and
	//         udpate new info
	// + case 4: user.userid does not present
	//         client want to update info of an user
	//         do exactly as case 1.
	// output: return mtid of identified visitor
	public identifyRaw(appid, data, callback) {
		let me = this;
		var collection = me.prefix + "app" + appid;
		var collectionmapping = me.prefix + me.mapping;
		var user = data.user;
		var userid = user.userid;

		// protect system properties, allow working only on user-based props
		// user-based prop is not started with an underscore '_' character
		var userex = {};
		for (var p in user) if (user.hasOwnProperty(p))
			if (p.startsWith('_') === false)
				userex[p] = user[p];
		user = userex;

		var themtid: mongodb.ObjectID = new mongodb.ObjectID(data.mtid);
		me.converter.toIDs(['_isUser', 'userid', '_mtid', '_segment', '_os','_ref', '_deviceid', '_devicetype', '_lang', '_city', '_country', '_browser', '_firstcampaign', '_lastcampaign', '_campaign'], function (ids) {
			me.valuemgr.cineObject(appid, 'user', user);
			me.converter.toObject(user, function (userx) {
				// check for case 4
				if (userid === undefined) return updateUserInfo(me.db, themtid, userx, callback);

				var query = {};
				query[ids._isUser] = true;
				query[ids.userid] = userid;
				me.db.collection(collection).findOneAndUpdate(query, { $set: userx }, { projection: { _id: 1 } }, function (err, r) {
					if (err) throw err;

					// case 3 : user doesn't exist
					if (r.value === null) return updateUserInfo(me.db, themtid, userx, callback);

					// user exist
					var ide_mtid: mongodb.ObjectID = r.value._id;
					// check for case 1
					if (themtid.toHexString() === ide_mtid.toHexString()) return updateUserInfo(me.db, themtid, userx, callback);

					// case 2
					// add to mapping collection
					me.db.collection(collectionmapping).insertOne({
						anomtid: themtid,
						idemtid: ide_mtid,
						ctime: new Date()
					}, function (err) {
						if (err) throw err;
					});

					//convert all ano-mtid to ide-mtid
					var query = {};
					var update = {};
					query[ids._mtid] = themtid;
					update[ids._mtid] = ide_mtid;
					me.db.collection(collection).updateMany(query, { $set: update }, function (err) {
						if (err) throw err;
					});
					// merge and delete ano-mtid record IF EXISTED
					me.db.collection(collection).find({ _id: themtid }).limit(1).toArray(function (err, r) {
						if (err) throw err;
						if (r.length === 0) return;
						var olduser = r[0];
						me.mergeInfo(olduser, userx, ids);
						me.db.collection(collection).deleteOne({ _id: themtid }, function (err) {
							if (err) throw err;
						});
						return updateUserInfo(me.db, ide_mtid, userx, callback);
					});
				});
			});
		});

		// purpose: update info which mtid is mtid
		function updateUserInfo(db: mongodb.Db, mtid, userx, callback) {
			callback(mtid);
			if (Object.keys(userx).length !== 0) {
				me.converter.toIDs(['email', 'phone', '_stime'], function (ids) { 
				if (userx[ids.email] !== undefined || userx[ids.phone] !== undefined)
				{
					db.collection(collection).find({ _id: mtid }).limit(1).toArray(function (err, r) {
						if (err) throw err;
						var userx = {};
						if (r[0][ids._stime] === undefined || r[0][ids._stime] === null) {
							userx[ids._stime] = Math.round(new Date().getTime() / 1000);
							db.collection(collection).update({ _id: mtid }, { $set: userx }, function (err, result) {
								if (err) throw err;
							});
						}
					});
					}
				});

				db.collection(collection).update({ _id: mtid }, { $set: userx }, function (err, result) {
					if (err) throw err;
				});
			}
		}
	}

	// purposer: phương thức này dùng để báo cho hệ thống biết một anonymous
	// user thực ra là một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
	// url: {appid}
	// param:
	//  mtid: string, //mtid của anonymous user
	//  user: {[userid], name, email, age, birth, gender, ...}
	public identify(req, res, callback) {
		let me = this;
		me.identifyRaw(req.params.appid, req.body, function (mtid) {
			res.writeHead(200, { 'Content-Type': 'text/plain' });
			res.end(mtid);
			callback(mtid);
		});
	}

	// purpose: set up new record for anonymous user
	// param: appid: id of the app
	public setupRaw(appid: string, callback) {
		var me = this;
		var collection = me.prefix + "app" + appid;
		var user = {
			_isUser: true,
			_segments: [],
			_ctime: Math.round(new Date().getTime() / 1000),
			_mtid: 2910
		};
		me.converter.toObject(user, function (user) {
			me.db.collection(collection).insertOne(user, function (err, results) {
				if (err) throw err;
				var mtid = results.insertedId;

				callback(mtid);
				// update mtid equal id
				for (var p in user) if (user.hasOwnProperty(p))
					if (user[p] === 2910) {
						user[p] = mtid;
					}
					else delete user[p];
				me.db.collection(collection).update({ _id: mtid }, { $set: user }, function (err, r) {
					if (err) throw err;
				});
			});
		});
	}

	// purpose: set up new record for anonymous user
	// url /{appid}/?deltatime=20
	// param:
	// + appid: id of the app
	// output: new mtid
	public setup(req: express.Request, res: express.Response, callback) {
		this.setupRaw(req.params.appid, function (mtid) {
			res.writeHead(200, { 'Content-Type': 'text/plain' });
			res.end(mtid);
		});
	}

	private updateChainCampaign(appid: string, actionid: string, data: any) {
		var me = this;
		var collection = me.prefix + "app" + appid;
			if(1==1)			return;
me.converter.toIDs(['_utm_source', '_utm_campaign', '_utm_term', '_utm_content', '_utm_medium', '_link'], function (ids) {
			var match = {};
			match[ids._link] = new mongodb.ObjectID(actionid);
		
		me.db.collection(collection).find(match).toArray(function (err, res: any[]) {
				if (err) throw err;

				if (res.length == 0)
					return;

				for (let act of res) {
					if (act[ids._utm_source] === undefined) act[ids._utm_source] = data[ids._utm_source];
					if (act[ids._utm_campaign] === undefined) act[ids._utm_campaign] = data[ids.utm_campaign];
					if (act[ids._utm_term] === undefined) act[ids._utm_term] = data[ids.utm_term];
					if (act[ids._utm_content] === undefined) act[ids._utm_content] = data[ids.utm_content];
					if (act[ids._utm_medium] === undefined) act[ids._utm_medium] = data[ids.utm_medium];
					me.updateChainCampaign(appid, act._id, act);
				}
			});
		});
	}

}
