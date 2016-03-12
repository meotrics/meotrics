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
	if(err){
		console.log(err);
		setTimeout(function(){
			throw err;
		});
	}
}


function getQueryTrending(object, converter, callback) {
	var query = [];
	converter.toID(object.object)
		.then(function(r){
			object.object = r;

			// -- START MATCH CLAUSE
			query.push({ $match: { _typeid: new mongodb.ObjectID(object.event) } });

			if (object._segment != undefined) {
				query[0]['$match']['_segments'] = {
					'$in': [object._segment]
				};
			}

			if(object.startTime != undefined){
				query[0]['$match']['_ctime'] = {
					$gte: object.startTime
				};
			}

			if(object.endTime != undefined){
				if(query[0]['$match']['_ctime'] != undefined){
					query[0]['$match']['_ctime']['$lte'] = object.endTime;
				}else{
					query[0]['$match']['_ctime'] = {
						$lte: object.endTime
					};
				}
			}
			// -- END OF MATCH CLAUSE

			// -- START GROUP FUNCTION
			object.order = object.order || 1;

			if(object.operation == 'count') {
				query.push({$group: {_id: '$'+object.object, count: {'$sum': 1}, temp: {$first : "$$ROOT"}}});
				query.push({$sort: {count: object.order}});

				object.limit = object.limit || 10;
				query.push({$limit: object.limit});

				converter.toObject(query[0]['$match'])		
					.then(function(r){
						query[0]['$match'] = r;
						callback(null, query);
					}).catch(function(e){
						callback(e);
					});
			}else{
				converter.toID(object.param)
					.then(function(r){
						object.param = r;

						if(object.operation == 'avg'){
							query.push({$group: {_id: '$'+object.object,result: {'$avg': '$'+object.param}, temp: {$first : "$$ROOT"} }});
							query.push({$sort: {result: object.order}});
						} else if(object.operation == 'sum'){
							query.push({$group: {_id: '$'+object.object,result: {'$sum': '$'+object.param}, temp: {$first : "$$ROOT"} }});				
							query.push({$sort: {result: object.order}});
						}

						object.limit = object.limit || 10;
						query.push({$limit: object.limit});

						return converter.toObject(query[0]['$match']);			
					}).then(function(r){
						query[0]['$match'] = r;
						callback(null, query);
					}).catch(function(e){
						callback(e);
					});
			}		
		}).catch(function(e){
			callback(e);
		});
}

function route(app, db, segmgr, prefix, mongodb) {
	var bodyParser = require('body-parser');
	var async = require('async');
	var converter = require('./utils/idmanager.js');
	converter = new converter.IdManager();
	// parse application/x-www-form-urlencoded
	// app.use(bodyParser.urlencoded({ extended: false }))

	// parse application/json
	app.use(bodyParser.json());

	//APP------------------------------------------------------------------------
	//set up an new app
	app.post('/app', function (req, res) {

	});

	// ACTION TYPE-----------------------------------------------------------------
	// create an action type
	app.post('/actiontype/:appid', function (req, res) {
		var data = req.body;
		data._appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).insertOne(data) // add options: w, j, timeout ...
			.then(function (r) {
				res.json({_id: r.insertedId });
			}).catch(mtthrow);
	});

	//get all actiontype
	app.get('/actiontype/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).find({ _appid: appid }, { _appid: 0 }).toArray()
		.then(function (results) {
			res.json(results);
		}).catch(mtthrow);
	});

	app.get('/actiontype/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).find({ _id: new mongodb.ObjectID(atid) }, { _appid: 0 }).toArray()
		.then(function (results) {
			res.json(results);
		}).catch(mtthrow);
	});

	//delete an actiontype
	app.delete('/actiontype/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(atid) })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	//delete all actiontypes in an app
	app.delete('/actiontype/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).deleteMany({ _appid: appid })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	// update actiontype
	app.put('/actiontype/:appid/:id', function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).updateOne({ _id: new mongodb.ObjectID(atid) }, { $set: data })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});


	//TREND-------------------------------------------------------------------------
	//create a trend
	app.post('/trend/:appid', function (req, res) {
		var data = req.body;
		data._appid = Number(req.params.appid);
		var collection = prefix + "trend";

		db.collection(collection).insertOne(data)
			.then(function (r) {
				res.json({ _id: r.insertedId });
			}).catch(mtthrow);
	});

	//get all trends in a app
	app.get('/trend/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "trend";
		db.collection(collection).find({ _appid: appid }, { _appid: 0 }).toArray()
			.then(function (results){
				res.json(results);
			}).catch(mtthrow);
	});

	//list a trend from a app
	app.get('/trend/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0, _appid: 0 }).toArray()
		.then(function (results) {
			res.json(results);
		}).catch(mtthrow);
	});


	// Update a trend
	app.put('/trend/:appid/:id', function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).updateOne({ _id: new mongodb.ObjectID(trid) }, { $set: data })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	app.delete('/trend/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "trend";
		db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(trid) })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	//delete all trends in an app
	app.delete('/trend/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "trend";
		db.collection(collection).deleteMany({ _appid: appid })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});


	// load trend ....
	app.get('/trend/query/:appid/:id', function (req, res) {
		var appid = req.params.appid;
		var collection = prefix + "trend";
		var results = [];
		async.waterfall([
			function(callback){
				var trid = req.params.id;
				db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0}).toArray()
					.then(function (r) {
						callback(null, r[0], converter);
					}).catch(function(e){
						callback(e);
					});
			}, getQueryTrending
			, function(query, callback){
				collection = prefix + appid;

				db.collection(collection).aggregate(query).toArray()
					.then(function(array){
						results = array;
						async.forEachOf(results, function(value, key, asyncCallback){
							converter.toOriginal(value.temp)
								.then(function(r){
									results[key].temp = r;
									asyncCallback(null);
								}).catch(function(e){
									asyncCallback(e);
								});
						}, function(err){
							if(err){
								callback(err);
							}else{
								res.json(results);
								callback(null);
							}
						});	
					}).catch(function(err){
						callback(err);
					});
			}
		], mtthrow);
	});

	//CLIENT------------------------------------------------------------------------

	/* Ghi nhận một action mới
	Tham số: {
		_mtid : number, ( Em tuong? lay _id cua mongodb?????)
		_typeid: number // type of action, eg: "purchase", "pageview", ( Them cai typeid cua "user" nua a nhe, luu chung ma)
		osid: number // os information, eg: "window", "linux", 
		browserid : number // eg, "chrome", "firefox", "opera", 
		locationid : number // location code, eg: Hanoi, Vietnam 
		referer: string
		campaignid : number
		deviceid : number
		_ctime: date // created time (Cai nay co van de ae minh lay time cua ae minh hay cua thang user, em nghi sai so k qua nhieu lay cua minh di)
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
		// Convert string to ObjectID in mongodgodb
		data._mtid = new mongodb.ObjectID(data._mtid);
		data._typeid = new mongodb.ObjectID(data._typeid);
		// Add created time 
		data._ctime = Math.round(new Date() / 1000);
		converter.toObject(data) 
			.then(function(results){
				return db.collection(collection).insertOne(results);
			}).then(function (r) {
				res.status(200).end();
			}).catch(mtthrow);
	});

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
		app.post('/i/:appid', function (req, res) {
			var data = req.body;
			var collection = prefix + req.params.appid;
			var userConverted;

			async.waterfall([
				function (callback) {
					var query;
					converter.toObject({_isUser: true, uid: data.user.uid})
						.then(function(r){
							query = r;
							return converter.toObject(data.user);
						}).then(function(r){
							userConverted = r;
							return db.collection(collection).findOneAndUpdate(query, {$set: userConverted}, { projection: { _id: 1 } });
						}).then(function (r) {
							if (r.value != null) {
								var _mtid = r.value._id;
								res.send(_mtid);
								callback(null, true, _mtid);
							} else {
								res.send(data.cookie);
								callback(null, false, '');
							}
						}).catch(function (err) {
							res.status(500).end();
							// [ERROR]
							callback(err);
						});
				},function (isCreated, _mtid, callback) {
					if (isCreated) {
						callback(null, true, _mtid);
					} else {
						db.collection(collection).updateOne({ _id: new mongodb.ObjectID(data.cookie) }, {$set: userConverted}, function (err, result) {
							if (err) callback(err);
							else callback(null, false, '');
						});
					}
				}, function (needUpdate, _mtid, callback) {
					if (needUpdate) { 
						converter.toID('_mtid')
							.then(function(id){
								var query = {};
								var update = {};
								query[id] = new mongodb.ObjectID(data.cookie);
								update[id] = new mongodb.ObjectID(_mtid);
								return db.collection(collection).updateMany(query, { $set: update });
							}).then(function (r) {
								return db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(data.cookie) });
							})then(function(r){
								callback(null);
							}).catch(function (err) {
								callback(err);
							});
					} else {
						callback(null);
					}
				}
			], mtthrow);
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
		converter.toID('_isUser')
			.then(function(r){
				var query = {};
				query[r] = true;
				return db.collection(collection).insertOne(query);
			}).then(function (results) {
				var _mtid = results.insertedId;
				res.send(_mtid);	
			}).catch(mtthrow);
		
	});

	//SEGMENTATION-------------------------------------------------------------

	//create a segment
	app.post('/segment/:appid', function (req, res) {
		var data = req.body;
		data.appid = Number(req.params.appid);

		var collection = prefix + "segment";
		db.collection(collection).insertOne(data) // add options: w, j, timeout ...
		.then(function (r) {
			res.json({_id: r.insertedId });
		}).catch(mtthrow);
	});

	//get all segments in a app
	app.get('/segment/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "segment";
		db.collection(collection).find({ appid: appid }, { appid: 0 }).toArray()
		.then(function (results) {
			res.json(results);
		}).catch(mtthrow);
	});

	//list a segment from a app
	app.get('/segment/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "segment";
		db.collection(collection).find({ _id: new mongodb.ObjectID(trid) }, { _id: 0, appid: 0 }).toArray()
		.then(function (results) {
			res.json(results);
		}).catch(mtthrow);
	});


	// Update a segment
	app.put('/segment/:appid/:id', function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "segment";
		db.collection(collection).updateOne({ _id: new mongodb.ObjectID(trid) }, { $set: data })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	app.delete('/segment/:appid/:id', function (req, res) {
		// var appid = req.params.appid;
		var trid = req.params.id;
		var collection = prefix + "segment";
		db.collection(collection).deleteOne({ _id: new mongodb.ObjectID(trid) })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	//delete all segments in an app
	app.delete('/segment/:appid', function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "segment";
		db.collection(collection).deleteMany({ appid: appid })
		.then(function (results) {
			res.status(200).end();
		}).catch(mtthrow);
	});

	//update or create a segment
	// app.post('segment', function (req, res) {
	// 	segmgr.create(req.params, function () {
	// 		res.sstatus(200).end();
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
	var prefix = config.get("mongod.prefix") || "meotrics_";

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

