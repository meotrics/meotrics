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
	var MongoClient = require('mongodb').MongoClient;
	var uuid = require('node-uuid');
	var bodyParser = require('body-parser');

	function buildconnstr() {
		var host = config.get("mongod.host") || "127.0.0.1";
		var port = config.get("mongod.port") || 27017;
		var database = config.get("mongod.database") || "test";
		return "mongodb://" + host + ":" + port + "/" + database;
	}

	function route(app, db, segmgr) {
		// parse application/x-www-form-urlencoded
		app.use(bodyParser.urlencoded({ extended: false }))

		// parse application/json
		app.use(bodyParser.json())	

		app.get('/', function (req, res) {
			res.send('Hello World!');
		});


		//APP------------------------------------------------------------------------
		//set up an new app
		app.post('/app', function (req, res) {

		});

		//ACTION TYPE-----------------------------------------------------------------
		//update or create an action type
		app.post('/actiontype', function (req, res) {
			var sampleActionType = {
				name: "purchase"

			}
		});

		//get actiontype by id or list
		app.get('/actiontype', function (req, res) {
			var sampleActionType = {
				name: "purchase"

			}
		});

		//delete an actiontype
		app.delete('/actiontype', function (req, res) {

		});


		//TREND-------------------------------------------------------------------------

		//get a trend
		app.get('/trend', function (req, res) {

		});
	
		//create or update a trend
		app.post('/trend', function(req, res){
			
		});

		//list trend
		app.get('/trends', function(req,res)
		{});
		
		app.delete('/trend', function(req,res)
		{
			
		});

		
		app.post('/trend/draf', function(req, res)
		{
			
		});
		
		//CLIENT------------------------------------------------------------------------
		
		/* Ghi nhận một action mới
		Tham số: {
			appid : number, 
			userid: number, 
			mtid : number, 
			typeid: number // type of action, eg: "purchase", "pageview"
			osid: number // os information, eg: "window", "linux", 
			browserid : number // eg, "chrome", "firefox", "opera", 
			locationid : number // location code, eg: Hanoi, Vietnam 
			referer: string
			campaignid : number
			deviceid : number
			ctime: date // created time
			ip: string // public ip address
			screenres: string // screen resolution of the device
			totalsec: number // total number of sec to finish the action
			url: string
			browserversion : number
			osversion : number
			field1, field2, field3, field4
		}
		*/
		app.post('/r', function (req, res) {
			var data = req.body;
			var collection = "meotrics_"+data.appid;
			delete data.appid;
			db.collection(collection).insertOne(data)
				.then(function(r){

				}).catch(function(e){

				});
		});

		/* Phương thức này dùng để báo cho hệ thống biết một anonymous user thực ra là
			một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
			
			Tham số:
			{
				appid: number,
				auid: number, //auid của anonymous user
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
			res.json({success: true});
			var data = req.body;
			var collection = "meotrics_"+data.appid;
			var uid = data.user.uid;

			async.waterfall([
				function(callback){
					delete data.user.uid;
					db.collection(collection).findOneAndUpdate({type: "user", uid: uid}, {$set: data.user})
						.then(function(r){
							if(r.value != null){ 
								if(r.lastErrorObject.n == 1){
									callback(null, true);
								}else{
									callback('Error');
								}
							}else{	
								callback(null, false);
							}
						}).catch(function(e){
							console.log(e);
							
						});
				}, function(isCreated, callback){
					if(isCreated){
						callback(null);
					}else{
						data.user.uid = uid;
						data.user.type = "user";
						db.collection(collection).insertOne(data.user)
							.then(function(r){
								if(results.insertedCount == 1){
									callback(null);
								}else{
									callback('Error');
								}
							}).catch(function(e){
								callback('Error');
							});
					}
				}, function(callback){
					db.collection(collection).updateMany({type: "action", uid: data.auid}, {$set: {uid: uid}})
						.then(function(r){
							// If success delete 
						}).catch(function(e){

						});
				}
			], function(err){
				// We need to do that again and again util it success
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
			var auid = uuid.v4();
			var collection = 'meotrics_'+req.body.appid;
			db.collection(collection).insertOne({uid: auid, type:'user'})
				.then(function(results){
					if(results.insertedCount == 1){
						res.json({success: true, auid: auid});
					}else{
						res.json({success: false});
					}
				}).catch(function(err){
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
		route(app, db, null);
		
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