exports.ActionMgr = function (db, mongodb, async, converter, prefix, mapping) {
	//CLIENT------------------------------------------------------------------------
	var Util = require('./util');
	var util = new Util(db, mongodb, async, converter, prefix);

	var url = require('url');

	/* Ghi nhận một action mới
	 Tham số: {
	 _mtid : number
	 _typeid: number // type of action, eg: "purchase", "pageview"
	 osid: number // os information, eg: "window", "linux",
	 browserid : number // eg, "chrome", "firefox", "opera",
	 locationid : number // location code, eg: Hanoi, Vietnam
	 referer: string
	 deviceid : number
	 _ctime: date // created time
	 ip: string // public ip address
	 screenres: string // screen resolution of the device
	 totalsec: number // total number of sec to finish the action
	 url: string
	 browserversion : number
	 osversion : number
	 userfields = {field1:10, field2: 345};
	 _data: data
	 }
	 */

	this.save = function (req, res, callback) {
		var data = req.body;
		var query = url.parse(data.url, true).query;
		// extract campaign

		var utm_source = query.utm_source;
		var utm_campaign = query.utm_campaign;


		var appid = req.params.appid;

		var mtid = data._mtid;
		var temp = data.user;
		delete data.user;
		var collection = prefix + appid;
		var collectionmapping = prefix + mapping;
		// Convert string to ObjectID in mongodgodb
		data._mtid = new mongodb.ObjectID(data._mtid);
		data._segments = [];
		// Add created time
		data._ctime = Math.round(new Date() / 1000);

		// retrive real mtid because user can still use old mtid
		db.collection(collectionmapping).find({key: data._mtid}, {key: 1, value: 1}).limit(1).toArray(function (err, r) {
			if (err) throw err;
			if (r.length != 0) data._mtid = r[0].value;

			converter.toObject(data, function (results) {
				db.collection(collection).insertOne(results, function (err, r) {
					if (err) throw err;
					res.status(200).end();
					if (callback) callback();
				});
			});

			//get user infomation
			db.collection(collection).find({_id: data._mtid}).limit(1).toArray(function (err, ret) {
				var user = ret[0];
				var typeid = data._typeid;
				converter.toIds('_revenue', '_firstcampaign', '_lastcampaign', '_campaign', function (ids) {
					// increase revenue
					if (typeid == 'purchase') {
						if (user[ids._revenue] == undefined) user[ids._revenue] = 0;
						user[ids._revenue] += data.data.amount;
					}

					if (typeid == 'pageview') {
						// record campaign
						if (utm_campaign) {
							if (user[ids._firstcampaign] == undefined) {
								user[ids._firstcampaign] = utm_campaign;
							}

							user[ids._lastcampaign] = utm_campaign;
							if (user[ids.campaign] == undefined) user[ids.campaign] = [];
							if (user[ids.campaign].indexOf(utm_campaign) == -1)
								user[ids.campaign] = user[ids.campaign].concat(utm_campaign).sort();
						}
					}

					util.updateUserInf(appid, _mtid, temp);

				});
			});
		});
	};

	// purposer: phương thức này dùng để báo cho hệ thống biết một anonymous
	// user thực ra là một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
	// url: {appid}
	// param:
	// + mtid: string, //mtid của anonymous user
	// + user: {[userid], name, email, age, birth, gender, ...}
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
	this.identify = function (req, res, callback) {
		var data = req.body;
		var collection = prefix + req.params.appid;
		var collectionmapping = prefix + mapping;
		var user = data.user;
		var userid = user.userid;

		// protect system properties, allow working only on user-based props
		// user-based prop is not started with an underscore '_' character
		var userex = {};
		for (var p in user) if (user.hasOwnProperty(p))
			if (p.startsWith('_') == false)
				userex[p] = user[p];
		user = userex;

		var themtid = new mongodb.ObjectID(data.mtid);
		converter.toIDs(['_isUser', 'userid', '_mtid'], function (ids) {
			converter.toObject(user, function (userx) {
				// check for case 4
				if (userid == undefined) return updateUserInfo(themtid, userx, callback);

				var query = {};
				query[ids._isUser] = true;
				query[ids.userid] = userid;
				db.collection(collection).findOneAndUpdate(query, {$set: userx}, {projection: {_id: 1}}, function (err, r) {
					if (err) throw err;

					// case 3 : user doesn't exist
					if (r.value == null) return updateUserInfo(themtid, userx, callback);

					// user exist
					var ide_mtid = r.value._id;
					// check for case 1
					if (themtid == ide_mtid) return updateUserInfo(themtid, userx, callback);

					// case 2
					// add to mapping collection
					db.collection(collectionmapping).insertOne({
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
					db.collection(collection).updateMany(query, {$set: update}, function (err) {
						if (err) throw err;
					});

					// delete ano-mtid record IF EXISTED
					db.collection(collection).deleteOne({_id: themtid}, function () {
					});

					return updateUserInfo(ide_mtid, userx, callback)
				});
			});
		});

		// purpose: update info which mtid is mtid
		function updateUserInfo(mtid, userx, callback) {
			res.send(mtid);
			db.collection(collection).updateOne({_id: mtid}, {$set: userx}, function (err, result) {
				if (err) throw err;
				callback(mtid);
			});
		}

	};

	// purpose: set up new record for anonymous user
	// Url /{appid}/?deltatime=20
	// param:
	// + appid: id of the app
	// + deltatime: number of second had elapsed before the request sent
	// output: new mtid
	this.setup = function (req, res, callback) {
		var deltatime = req.body._deltatime || 0;
		var collection = prefix + req.params.appid;
		var user = {
			_isUser: true,
			_segments: [],
			_stime: Math.round(new Date() / 1000) - deltatime
		};
		converter.toObject(user, function (user) {
			db.collection(collection).insertOne(user, function (err, results) {
				if (err) throw err;
				var mtid = results.insertedId;
				res.send(mtid);
				if (callback) callback(mtid);
			});
		});
	};
};