import * as mongodb from 'mongodb';
import * as url from 'url';
import * as express from 'express';

export class ActionMgr {
	constructor(private db: mongodb.Db, private converter, private prefix: string, private mapping: string) {
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
		let me = this;
		var collection = this.prefix + "app" + appid;
		var collectionmapping = this.prefix + this.mapping;
		var mtid = new mongodb.ObjectID(data._mtid);
		data._mtid = mtid;

		// extract campaign
		if (data._url === null || data._url === undefined) data._url = "";
		var query = url.parse(data._url, true).query;
		var utm_source = query.utm_source;
		var utm_campaign = query.utm_campaign;
		var utm_term = query.utm_term;
		var utm_content = query.utm_content;
		var utm_medium = query.utm_medium;

		data._segments = [];

		// correct timming
		data._ctime = Math.round(new Date().getTime() / 1000) - (parseInt(data._deltat) ? parseInt(data._deltat) : 0);
		delete data._deltat;

		// retrive real mtid because user can still use old mtid
		me.db.collection(collectionmapping).find({ anomtid: mtid }).limit(1).toArray(function (err, r) {
			if (err) throw err;
			if (r.length !== 0) mtid = r[0].idemtid;

			if (data._utm_source === undefined) data._utm_source = utm_source;
			if (data._utm_campaign === undefined) data._utm_campaign = utm_campaign;
			if (data._utm_term === undefined) data._utm_term = utm_term;
			if (data._utm_content === undefined) data._utm_content = utm_content;
			if (data._utm_medium === undefined) data._utm_medium = utm_medium;
			me.converter.toObject(data, function (datax) {
				console.log(collection);
				me.db.collection(collection).insertOne(datax, function (err, r) {
					if (err) throw err;
					callback(r.insertedId);
				});

				//get user infomation
				me.db.collection(collection).find({ _id: mtid }).limit(1).toArray(function (err, ret) {
					if (err) throw err;
					var user = ret[0];
					if (user === undefined) throw "mtid " + mtid + " did not match any user";
					var typeid = data._typeid;
					me.converter.toIDs(['_revenue', '_firstcampaign', '_lastcampaign', '_campaign', '_ctime', '_mtid', '_segments', '_url', '_typeid', '_referer', '_totalsec'], function (ids) {
						// increase revenue
						var simpleprop = {};

						if (typeid === 'purchase') {
							if (user[ids._revenue] === undefined) user[ids._revenue] = 0;
							simpleprop[ids._revenue] = user[ids._revenue] + data.amount;
						}

						if (typeid === 'pageview') {
							// record campaign
							if (utm_campaign) {
								if (user[ids._firstcampaign] === undefined) {
									simpleprop[ids._firstcampaign] = utm_campaign;
								}
								simpleprop[ids._lastcampaign] = utm_campaign;
								datax[ids._campaign] = utm_campaign;
							}
						}

						// update user
						if (Object.keys(simpleprop).length !== 0)
							me.db.collection(collection).updateOne({ _id: mtid }, { $set: simpleprop }, function (err, r) {
								if (err) throw err;
							});

						// filter out unneeded array prop
						var arrayprop = {};
						for (var p in datax) if (datax.hasOwnProperty(p))
							if (p.startsWith('_'))
								arrayprop[p] = datax[p];
						delete arrayprop[ids._mtid];
						delete arrayprop[ids._ctime];
						delete arrayprop[ids._segments];
						delete arrayprop[ids._url];
						delete arrayprop[ids._typeid];
						delete arrayprop[ids._referer];
						delete arrayprop[ids._totalsec];
						delete arrayprop[ids._revenue];
						delete arrayprop[ids._firstcampaign];
						delete arrayprop[ids._lastcampaign];
						delete arrayprop[ids._totalsec];

						if (Object.keys(arrayprop).length !== 0)
							updateArrayBasedUserInfo(collection, mtid, user, arrayprop);
					});
				});
			});
		});

		// purpose: add new data to arrays in user
		// param:
		// + collection: collection to query user information
		// + mtid: mongodb.ObjectID mtid of user
		// + data: data to be append to user
		function updateArrayBasedUserInfo(collection, mtid, user, data) {
			me.converter.toObject(data, function (datax) {

				// append new element to the array or create one
				var arr = [];
				for (var p in datax) if (datax.hasOwnProperty(p)) {
					if (user[p] !== undefined) {
						arr = user[p];
					}
					if (arr instanceof Array === false)
						arr = [arr];

					arr = arr.concat(datax[p]).sort();
				}
				me.db.collection(collection).updateOne({ _id: mtid }, { "$set": user }, function (err, r) {
					if (err) throw err;
				});
			});
		}
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
	public fixRaw(appid: string, actionids: string, data, callback: () => void) {
		let me = this;
		let actionid = new mongodb.ObjectID(actionids);
		if (data._mtid) data._mtid = new mongodb.ObjectID(data._mtid);
		var collection = me.prefix + "app" + appid;
		//make sure dont change typeid
		delete data._typeid;
		me.converter.toObject(data, function (datax) {
			me.db.collection(collection).updateOne({ _id: actionid }, { $set: datax }, function (err, r) {
				if (err) throw err;
				callback();
			});
		});
	}

	public fix(req: express.Request, res: express.Response, callback) {
		var me = this;
		var data = req.params;
		me.fixRaw(req.params.appid, req.params.actionid, data, function () {
			res.writeHead(200);
			res.end();
			callback();
		});
	}

	public x(req, res, callback) {
		var me = this;
		var data = req.params;
		var collection = me.prefix + "app"+ req.appid;
		var actionid = new mongodb.ObjectID(req.actionid);
		me.converter.toIDs(['_ctime', 'totalsec'], function (ids) {
			var projection = {};
			projection[ids._ctime] = 1;
			me.db.collection(collection).find({ _id: actionid }, projection).limit(1).toArray(function (err, r) {
				if (err) throw err;
				if (r.length === 0) throw "not found pageview to close, actionid: " + actionid;
				var newaction = {};
				newaction[ids.totalsec] = Math.round(new Date().getTime() / 1000) - (parseInt(data._deltat) ? parseInt(data._deltat) : 0) - r[0][ids._ctime];
				me.db.collection(collection).updateOne({ _id: actionid }, { $set: newaction }, function (err, r) {
					if (err) throw err;
					res.writeHead(200);
					res.end();
					callback();
				});
			});
		});
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

		var themtid = new mongodb.ObjectID(data.mtid);
		me.converter.toIDs(['_isUser', 'userid', '_mtid'], function (ids) {
			me.converter.toObject(user, function (userx) {
				// check for case 4
				if (userid === undefined) return updateUserInfo(themtid, userx, callback);

				var query = {};
				query[ids._isUser] = true;
				query[ids.userid] = userid;
				me.db.collection(collection).findOneAndUpdate(query, { $set: userx }, { projection: { _id: 1 } }, function (err, r) {
					if (err) throw err;

					// case 3 : user doesn't exist
					if (r.value === null) return updateUserInfo(themtid, userx, callback);

					// user exist
					var ide_mtid = r.value._id;
					// check for case 1
					if (themtid === ide_mtid) return updateUserInfo(themtid, userx, callback);

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

					// delete ano-mtid record IF EXISTED
					me.db.collection(collection).deleteOne({ _id: themtid }, function () {
					});

					return updateUserInfo(ide_mtid, userx, callback);
				});
			});
		});

		// purpose: update info which mtid is mtid
		function updateUserInfo(mtid, userx, callback) {
			let me = this;
			callback(mtid);
			me.db.collection(collection).updateOne({ _id: mtid }, { $set: userx }, function (err, result) {
				if (err) throw err;
			});
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
			_stime: Math.round(new Date().getTime() / 1000),
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
						break;
					}
					else delete user[p];
				me.db.collection(collection).updateOne({ _id: mtid }, { $set: user }, function (err, r) {
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

}
