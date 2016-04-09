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

	/* Phương thức này dùng để báo cho hệ thống biết một anonymous user thực ra là
	 một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png

	 Tham số:
	 {
	 cookie: string, //mtid của anonymous user
	 user: {userid, name, email, age, birth, gender, ...}
	 }

	 Điều kiện:
	 1. Toàn bộ actions thuộc anonymous user được sang tên cho user, thông tin về
	 user được cập nhập
	 Chú ý: nếu userid không tồn tại trong hệ thống, thì cập nhật luôn userid của anonymous
	 user thành userid ở tham số.
	 2. Toàn bộ thông tin về user được cập nhật mới.
	 */
	this.identify = function (req, res, next) {
		var data = req.body;
		var collection = prefix + req.params.appid;
		var collectionmapping = prefix + mapping;
		var userConverted;

		async.waterfall([function (callback) {
			var query;
			converter.toObject({_isUser: true, userid: data.user.userid}).then(function (r) {
				query = r;
				return converter.toObject(data.user);
			}).then(function (r) {
				userConverted = r;
				return db.collection(collection).findOneAndUpdate(query, {$set: userConverted}, {projection: {_id: 1}});
			}).then(function (r) {
				if (r.value != null) {
					var _mtid = r.value._id;
					db.collection(collectionmapping).insertOne({
						key: new mongodb.ObjectID(data.cookie),
						value: new mongodb.ObjectID(_mtid),
						created_at: new Date()
					});
					res.send(_mtid);
					callback(null, true, _mtid);
				} else {
					res.send(data.cookie);
					callback(null, false, data.cookie);
				}
			}).catch(function (err) {
				// [ERROR]
				res.status(500).end(err.message);
				callback(err);
			});
		}, function (isCreated, _mtid, callback) {
			if (isCreated) {
				callback(null, true, _mtid);
			} else {
				db.collection(collection).updateOne({_id: new mongodb.ObjectID(data.cookie)}, {$set: userConverted}, function (err, result) {
					if (err) callback(err);
					else callback(null, false, _mtid);
				});
			}
		}, function (needUpdate, _mtid, callback) {
			if (needUpdate) {
				converter.toID('_mtid')
						.then(function (id) {
							var query = {};
							var update = {};
							query[id] = new mongodb.ObjectID(data.cookie);
							update[id] = new mongodb.ObjectID(_mtid);
							return db.collection(collection).updateMany(query, {$set: update});
						}).then(function (r) {
					return db.collection(collection).deleteOne({_id: new mongodb.ObjectID(data.cookie)});
				}).then(function (r) {
					callback(null, _mtid);
				}).catch(function (err) {
					callback(err);
				});
			} else {
				callback(null, _mtid);
			}
		}
		], function (err, _mtid) {
			if (err) {
				// TODO:
			}
		});
	};

	// Purpose: set up new record for anonymous user
	// Url /{appid}/?deltatime=20
	// Param:
	// + appid: id of the app
	// + deltatime: number of second had elapsed before the request sent
	// Output: new mtid
	this.setup = function (req, res, callback) {
		var deltatime = req.body._deltatime || 0;
		var collection = prefix + req.params.appid;
		var user = {
			_isUser: true,
			_segments: [],
			_stime : Math.round(new Date() / 1000) - deltatime
		};
		converter.toObject(user, function (user) {
			db.collection(collection).insertOne(user, function (err, results) {
				if(err) throw err;
				var mtid = results.insertedId;
				res.send(mtid);
				if(callback) callback(mtid);
			});
		});
	};
};