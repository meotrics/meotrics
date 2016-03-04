"use strict";

var cluster = require('cluster');

if (cluster.isMaster) {
	var config = require('config');
	var numWebservers = config.get("numWebservers");
	console.log('Master cluster setting up ' + numWebservers + ' webservers...');
	for (let i = 0; i < numWebservers; i++) {
		cluster.fork();
	}
} else {
	var config = require('config');
	var mongodb = require('mongodb');
	var MongoClient = mongodb.MongoClient;
	

	

	function buildconnstr() {
		var host = config.get("mongod.host") || "127.0.0.1";
		var port = config.get("mongod.port") || 27017;
		var database = config.get("mongod.database") || "test";
		return "mongodb://" + host + ":" + port + "/" + database;
	}

	function route(app, db, segmgr, prefix) {
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
			var collection = config.get('prefix')+"actiontype";
			db.collection(collection).insertOne(data) // add options: w, j, timeout ...
				.then(function(r){ 
					res.json({s: true, _id: r.insertedId});
				}).catch(function(err){
					// [ERROR]
					res.json({s: false});
				});
		});

		//get all actiontype
		app.get('/actiontype/:appid', function (req, res) {
			var appid = Number(req.params.appid);
			var collection = config.get('prefix')+"actiontype";
			db.collection(collection).find({appid: appid}, {appid: 0}).toArray()
				.then(function(results){
					res.json({s: true, results})
				}).catch(function(e){
					// [ERROR]
					res.json({s: false});
				});
		});

		app.get('/actiontype/:appid/:id', function (req, res){
			// var appid = req.params.appid;
			var atid = req.params.id;
			var collection = config.get('prefix')+"actiontype";
			db.collection(collection).find({_id: new mongodb.ObjectID(atid) }, {appid: 0}).toArray()
				.then(function(results){
					res.json({s: true, results})
				}).catch(function(e){
					// [ERROR]
					res.json({s: false});
				});
		});

		//delete an actiontype
		app.delete('/actiontype/:appid/:id', function (req, res) {
			// var appid = req.params.appid;
			var atid = req.params.id;
			var collection = config.get('prefix')+"actiontype";
			db.collection(collection).deleteOne({_id: new mongodb.ObjectID(atid)})
				.then(function(results){
					res.json({s: true});
				}).catch(function(e){
					// [ERROR]
					res.json({s: false});
				});
		});

		//delete all actiontypes in an app
		app.delete('/actiontype/:appid', function (req, res) {
			var appid = Number(req.params.appid);
			var collection = config.get('prefix')+"actiontype";
			db.collection(collection).deleteMany({appid: appid})
				.then(function(results){
					res.json({s: true})
				}).catch(function(e){
					// [ERROR]
					res.json({s: false});
				});
		});

		// update actiontype
		app.put('/actiontype/:appid/:id', function (req, res){
			var data = req.body;
			// var appid = req.params.appid;
			var atid = req.params.id;
			var collection = config.get('prefix')+"actiontype";
			db.collection(collection).updateOne({_id: new mongodb.ObjectID(atid)}, {$set: data})
				.then(function(results){
					res.json({s: true});
				}).catch(function(e){
					// [ERROR]
					res.json({s: false});
				});
		});


		//TREND-------------------------------------------------------------------------

		//get all trends in a app
		app.get('/trend/:appid', function (req, res) {

		});

		//list a trend from a app
		app.get('/trend/:appid/:id', function(req,res){

		});

		//create a trend
		app.post('/trend/:appid', function(req, res){
				
		});
		// Update a trend
		app.put('/trend/:appid/:id', function(req,res)
		{
			
		});

		app.delete('/trend/:appid/:id', function(req,res)
		{
			
		});


		// Get data trend ....
		app.post('/trend/draf', function(req, res)
		{
			
		});
		
		//CLIENT------------------------------------------------------------------------
		
		/* Ghi nhận một action mới
		Tham số: {
			appid : number, 
			userid: number, ( Em tuong cai nay phu thuoc vao thang khach hang) 
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
			field1, field2, field3, field4 
		}

		NOTEEE: Quy dinh ma~ code som' di a con` cho vao code luon
		*/
		var bufferSpace = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

		app.post('/r', function (req, res) {
			var data = req.body;
			var collection = prefix+data.appid;
			data.buffer_space = bufferSpace;
			delete data.appid;
			db.collection(collection).insertOne(data)
				.then(function(r){
					var _id = r.insertedId;
					db.collection(collection).updateOne({_id: new mongodb.ObjectID(_id)}, {$unset: {buffer_space: ""}});	
				}).catch(function(e){
					// [ERROR]
				});
		});

		/* Phương thức này dùng để báo cho hệ thống biết một anonymous user thực ra là
			một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
			
			Tham số:
			{
				appid: number,
				mtid: number, //auid của anonymous user
				userid, name, email, age, birth, gender, ...
			}
			
			Điều kiện: 
			1. Toàn bộ actions thuộc anonymous user được sang tên cho user, thông tin về
					user được cập nhập
					Chú ý: nếu userid không tồn tại trong hệ thống, thì cập nhật luôn userid của anonymous
					user thành userid ở tham số.
			2. Toàn bộ thông tin về user được cập nhật mới.
			*/
		app.post('/i', function (req, res) {
			var data = req.body;
			var collection = prefix+data.appid;
			var uid = data.user.uid;

			async.waterfall([
				function(callback){
					delete data.user.uid;
					db.collection(collection).findOneAndUpdate({type: "user", uid: uid}, {$set: data.user}, {projection:{_id: 1}})
						.then(function(r){
							console.log(r);
							if(r.value != null){ 
								var mtid = r.value._id;
								res.json({success: true, mtid: mtid});
								if(r.lastErrorObject.n == 1){
									// Update thanh cong
								}else{
									// Update khong thanh cong
									// [ERROR]
								}
								callback(null, true, mtid);
							}else{	
								res.json({success: true, mtid: data.mtid});
								callback(null, false);
							}
						}).catch(function(e){
							// [ERROR]
							callback('Error');
							res.json({success: false});
						});
				}, function(isCreated, mtid, callback){	
					if(isCreated){
						callback(null, true, mtid);
					}else{
						data.user.uid = uid;
						data.user.type = "user";

						db.collection(collection).updateOne({_id: new mongodb.ObjectID(data.mtid)}, data.user)
							.then(function(r){
								// check update thanh cong
								callback(null, false);
							}).catch(function(e){
								// [ERROR]
								callback(null, false);
							});
					}
				}, function(needUpdate, mtid, callback){
					if(needUpdate){
						console.log('haha');
						console.log(data.mtid+" "+mtid);
						db.collection(collection).updateMany({type: "action", uid: new mongodb.ObjectID(data.mtid)}, {$set: {uid: new mongodb.ObjectID(mtid)}})
							.then(function(r){
								// If success delete 

							}).catch(function(e){
								// [ERROR]
								callback('Error');
							});
					}else{
						callback(null);
					}
				}
			], function(err){
				if(err){
					// We need to do that again and again util it success
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

		app.post('/s', function (req, res) {
			var data = req.body;
			var collection = prefix + data.appid;
			db.collection(collection).insertOne({type:'user'})
				.then(function(results){
					if(results.insertedCount == 1){
						var mtid = results.insertedId;
						res.json({success: true, mtid: mtid});
					}else{
						res.json({success: false});
					}
				}).catch(function(err){
					// [ERROR]
					res.json({success: false});
				});
		});

		//SEGMENTATION-------------------------------------------------------------
		
		//make a segment query
		app.get('segment', function(req, res)
		{
			
		});
		
		//list segment
		app.get('segments', function(req,res)
		{});
		
		app.post('segment/draf', function(req, res)
		{});
		
		//delete a segment
		app.delete('segment', function(req, res)
		{
			
		})
		
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
		
		route(app, db, null, prefix);
		
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

}