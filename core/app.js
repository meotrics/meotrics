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

	function buildconnstr() {
		var host = config.get("mongo.host") || "127.0.0.1";
		var port = config.get("mongo.port") || 27017;
		var database = config.get("mongo.database") || "test";
		return "mongodb://" + host + ":" + port + "/" + database;
	}

	function route(app, db, segmgr) {
		app.get('/', function (req, res) {
			res.send('Hello World!');
		});

		var config = require('config');

		function buildconnstr() {
			var host = config.get("mongod.host");
			var port = config.get("mongod.port");
			var database = config.get("mongod.database");
			return "mongodb://" + host + ":" + port + "/" + database;
		}


		//APP------------------------------------------------------------------------
		//set up an new app
		app.post('/app', function (req, res) {

		});

		//ACTION TYPE-----------------------------------------------------------------
		//update or create an action type
		app.post('/actiontype', function (req, res) {
			var sampleActionType = {
				name: "purchase",

			}
		});

		//get actiontype by id or list
		app.get('/actiontype', function (req, res) {
			var sampleActionType = {
				name: "purchase",

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
			
		});

		/* Phương thức này dùng để báo cho hệ thống biết một anonymous user thực ra là
			một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
			
			Tham số:
			{
				appid: number,
				mtid: number, //mtid của anonymous user
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
			res.json({ uid: uuid.v4() });
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
		app.post('segment', function (req, res) {
			segmgr.create(req.params, function () {
				res.send(200);
			});
		})
	}

	//THE ENTRY POINT
	//Using connection pool. Initialize mongodb once
	MongoClient.connect(buildconnstr(), function (err, db) {
		if (err) throw err;

		var SegmentMgr = require('segment').SegmentMgr;
		var express = require('express');
		
		//create component
		var segmgr = new SegmentMgr(db);
		
		//set up new express application
		var app = express();
		route(app, db, segmgr);
		
		//run the app
		var port = config.get("port") || 2108;
		app.listen(port, function () {
			console.log('Meotrics core listening on port ' + port + '!');
		});

		app.get('/segment', function (req, res) {
			segmgr.create(req.params, function (responsetext) {
				res.send(responsetext);
				res.end()
			});
		})
	});
}