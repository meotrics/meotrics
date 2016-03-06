"use strict";


var config = require('config');
var mongodb = require('mongodb');
var MongoClient = mongodb.MongoClient;

function buildconnstr() {
	var host = config.get("mongod.host") || "127.0.0.1";
	var port = config.get("mongod.port") || 27017;
	var database = config.get("mongod.database") || "test";
	return "mongodb://" + host + ":" + port + "/" + database;
}

function mtthrow(err)
{
	setTimeout(function(){
		throw err;
	});
}

function getQueryTrending(object) {
	var query = [];
	console.log(object);
	query.push({ $match: { typeid: new mongodb.ObjectID(object.event) } });

	if (object.segment != undefined) {
		var field = "segment_" + object.segment;
		query[0].$match[field] = true;
	}

	if(object.operation == 'count') {
		query.push({$group: {
							_id: '$'+object.object,
							count: {'$sum': 1}
						}});
						query.push({$sort: {count: object.order}});
	}else if(object.operation == 'avg'){
						query.push({$group: {
							_id: '$'+object.object,
							result: {$avg: '$'+object.param}
						}});
						query.push({$sort: {result: object.order}});
	}else if(object.operation == 'sum'){
						query.push({$group: {
							_id: '$'+object.object,
							result: {'$sum': '$'+object.param}
						}});
						if(object.order === undefined) object.order = 1;
						query.push({$sort: {result: object.order}});
					}
					if(object.limit === undefined) object.limit = 10;
					query.push({$limit: object.limit});
					
	return query;
}

function route(app, db, segmgr, prefix, mongodb) {
	var bodyParser = require('body-parser');
	var async = require('async');
			
	// parse application/x-www-form-urlencoded
	// app.use(bodyParser.urlencoded({ extended: false }))

	// parse application/json
	app.use(bodyParser.json());

	//APP------------------------------------------------------------------------
	//set up an new app
	app.post('/app', function (req, res) {

	});

	//ACTION TYPE-----------------------------------------------------------------
	// create an action type
	app.post('/actiontype/:appid', function (req, res) {
				var data = req.body;
				data.appid = Number(req.params.appid);
				var collection = prefix + "actiontype";
				db.collection(collection).insertOne(data) // add options: w, j, timeout ...
			.then(function (r) {
				res.json({ s: true, _id: r.insertedId });
			}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
			});
	});

	//get all actiontype
	app.get('/actiontype/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).find({ appid: appid }, { appid: 0 }).toArray()
						.then(function (results) {
				res.json({ s: true, results });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	app.get('/actiontype/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).find({ _id: new mongodb.ObjectID(atid) }, { appid: 0 }).toArray()
						.then(function (results) {
				res.json({ s: true, results });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//delete an actiontype
	app.delete('/actiontype/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(atid) })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//delete all actiontypes in an app
	app.delete('/actiontype/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).deleteMany({ appid: appid })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	// update actiontype
	app.put('/actiontype/:appid/:id', function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).updateOne({ _id: new mongodb.ObjectID(atid) }, { $set: data })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});


	//TREND-------------------------------------------------------------------------
	//create a trend
	app.post('/trend/:appid', function (req, res) {
		var data = req.body;
		data.appid = Number(req.params.appid);
		var collection = prefix + "trend";

		db.collection(collection).insertOne(data) // add options: w, j, timeout ...
						.then(function (r) {
				res.json({ s: true, _id: r.insertedId });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//get all trends in a app
	app.get('/trend/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "trend";
		db.collection(collection).find({ appid: appid }, { appid: 0 }).toArray()
						.then(function (results) {
				res.json({ s: true, results });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//list a trend from a app
	app.get('/trend/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0, appid: 0 }).toArray()
						.then(function (results) {
				res.json({ s: true, results });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});


	// Update a trend
	app.put('/trend/:appid/:id', function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).updateOne({ _id: new mongodb.ObjectID(trid) }, { $set: data })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	app.delete('/trend/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(trid) })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//delete all trends in an app
	app.delete('/trend/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "trend";
		db.collection(collection).deleteMany({ appid: appid })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});


	// load trend ....
	app.get('/trend/query/:appid/:id', function (req, res) {
		var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0, appid: 0 }).toArray()
			.then(function (results) {
				var trendData = results[0];
				collection = prefix + appid;
				console.log(trendData)
				console.log(getQueryTrending(trendData));
				return db.collection(collection).aggregate(getQueryTrending(trendData)).toArray();
						}).then(function (results) {
				res.json({ s: true, results });
			}).catch(mtthrow);
	});



	//CLIENT------------------------------------------------------------------------
				
	/* Ghi nhận một action mới
	Tham số: {
		mtid : number, ( Em tuong? lay _id cua mongodb?????)
		typeid: number // type of action, eg: "purchase", "pageview", ( Them cai typeid cua "user" nua a nhe, luu chung ma)
		osid: number // os information, eg: "window", "linux", 
		browserid : number // eg, "chrome", "firefox", "opera", 
		locationid : number // location code, eg: Hanoi, Vietnam 
		referer: string
		campaignid : number
		deviceid : number
		ctime: date // created time (Cai nay co van de ae minh lay time cua ae minh hay cua thang user, em nghi sai so k qua nhieu lay cua minh di)
		ip: string // public ip address
		screenres: string // screen resolution of the device
		totalsec: number // total number of sec to finish the action
		url: string
		browserversion : number
		osversion : number
		userfields = {field1:10, field2: 345};
	}

	NOTEEE: Quy dinh ma~ code som' di a con` cho vao code luon
	*/

	app.post('/r/:appid', function (req, res) {
		var data = req.body;
		var collection = prefix + req.params.appid;
		// Convert string to ObjectID in mongodb
		data.mtid = new mongodb.ObjectID(data.mtid);
		data.typeid = new mongodb.ObjectID(data.typeid);
		// This is for add prefix to userfields
		var userfields = data.userfields;
		delete data.userfields;
		var keys = Object.keys(userfields);
		for (var i = 0; i < keys.length; i++) {
						data["f_" + keys[i]] = userfields[keys[i]];
		}
		// Add created time 
		data.ctime = Math.round(new Date() / 1000);
		db.collection(collection).insertOne(data)
						.then(function (r) {
				res.json({ s: true });
						}).catch(function (e) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	/* Phương thức này dùng để báo cho hệ thống biết một anonymous user thực ra là
		một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
		
		Tham số:
		{
			appid: number,
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
	app.post('/i/:appid', function (req, res) {
		var data = req.body;
		var collection = prefix + req.params.appid;
		var uid = data.user.uid;

		async.waterfall([
						function (callback) {
				delete data.user.uid;
				db.collection(collection).findOneAndUpdate({ isUser: true, uid: uid }, { $set: data.user }, { projection: { _id: 1 } })
					.then(function (r) {
						if (r.value != null) {
							var mtid = r.value._id;
							res.json({ s: true, mtid: mtid });
							callback(null, true, mtid);
						} else {
							res.json({ s: true, mtid: data.cookie });
							callback(null, false);
						}
					}).catch(function (err) {
						// [ERROR]
						callback(err, res);
					});
						}, function (isCreated, mtid, callback) {
				if (isCreated) {
					callback(null, true, mtid);
				} else {
					data.user.uid = uid;
					data.user.isUser = true;

					db.collection(collection).updateOne({ _id: new mongodb.ObjectID(data.cookie) }, data.user, function (err, result) {
						if (err) callback(err, null);
						else callback(null, false);
					});
				}
						}, function (needUpdate, mtid, callback) {
				if (needUpdate) {
					db.collection(collection).updateMany({ mtid: new mongodb.ObjectID(data.cookie) }, { $set: { mtid: new mongodb.ObjectID(mtid) } })
						.then(function (r) {
							db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(data.cookie) }, {}, function (err, result) {
											callback(err, null);
										});
									}).catch(function (err) {
										callback(err, null);
									});
							} else {
								callback(null);
							}
						}
		], function (err, res) {
						if (err) {
				throw { err: err, res: res };
						}
		});
	});

	/* Thiết lập cookie mới cho người dùng mới
	Tham số: {
		appid: number
	}
	Điều kiện:
	Một bản ghi user được tạo trong collection của appid, thông tin trống rỗng
	Đầu ra:
	mtid vừa được tạo
	*/

	app.get('/s/:appid', function (req, res) {
		var collection = prefix + req.params.appid;
		db.collection(collection).insertOne({})
						.then(function (results) {
				var mtid = results.insertedId;
				res.json({ s: true, mtid: mtid });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//SEGMENTATION-------------------------------------------------------------
				
	//create a segment
	app.post('/segment/:appid', function (req, res) {
		var data = req.body;
		data.appid = Number(req.params.appid);

		var collection = prefix + "segment";
		db.collection(collection).insertOne(data) // add options: w, j, timeout ...
						.then(function (r) {
				res.json({ s: true, _id: r.insertedId });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//get all segments in a app
	app.get('/segment/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "segment";
		db.collection(collection).find({ appid: appid }, { appid: 0 }).toArray()
						.then(function (results) {
				res.json({ s: true, results });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//list a segment from a app
	app.get('/segment/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "segment";
		db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0, appid: 0 }).toArray()
						.then(function (results) {
				res.json({ s: true, results });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

				
	// Update a segment
	app.put('/segment/:appid/:id', function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "segment";
		db.collection(collection).updateOne({ _id: new mongodb.ObjectID(trid) }, { $set: data })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	app.delete('/segment/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "segment";
		db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(trid) })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});

	//delete all segments in an app
	app.delete('/segment/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "segment";
		db.collection(collection).deleteMany({ appid: appid })
						.then(function (results) {
				res.json({ s: true });
						}).catch(function (err) {
				// [ERROR]
				throw { err: err, res: res };
						});
	});
				
	//update or create a segment
	// app.post('segment', function (req, res) {
	// 	segmgr.create(req.params, function () {
	// 		res.send(200);
	// 	});
	// })
}

//THE ENTRY POINT
//Using connection pool. Initialize mongodb once
MongoClient.connect(buildconnstr(), function (err, db) {
	if (err) throw err;

	// var SegmentMgr = require('segment').SegmentMgr;
	var express = require('express');
				
	//create component
	// var segmgr = new SegmentMgr(db);
				
	//set up new express application
	var app = express();
	// route(app, db, segmgr);
	var prefix = config.get("prefix") || "meotrics_";

	route(app, db, null, prefix, mongodb);
				
	//run the app
	var port = config.get("port") || 2108;
	app.listen(port, function () {
				console.log('Meotrics core listening on port ' + port + '!');
	});

	// app.get('/segment', function (req, res) {
	// 	segmgr.create(req.params, function (responsetext) {
	// 		res.send(responsetext);
	// 		res.end()
	// 	});
	// })
});

