
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');
var MongoClient = mongodb.MongoClient;
var converter = require('../utils/fakeidmanager.js');
converter = new converter.IdManager();

var UserG = require('../utils/generateUsers.js');
var ActionG = require('../utils/generateLog.js');
var Valuemgr = require('../module/valuemgr.js').ValueMgr;

var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');
var prefix = config.get('mongod.prefix');
var appid = "testsync";

function samplingData(db, done)
{
	// insert 10 user
	// insert 100 pageview for 10 user with difference os, browser
	var valuemgr = new Valuemgr(db, prefix);
	UserG.generate(appid, valuemgr, converter, url, 10, `${prefix}app${appid}`, function(){
		ActionG.generate(appid, valuemgr, converter, url, 100, 0, `${prefix}app${appid}`, "pageview", "", function()
		{
			done();
		});
	});
}

describe("Sync campaign", function(){

		beforeAll(function(done){
			console.log('434');
			done();
		});

		afterAll(function(done){
			done();
		});

	MongoClient.connect("mongodb://127.0.0.1" + ":" + config.get('mongod.port') + '/test' , function (err, db) {

		console.log(1);
	it("copy all os from pageview to user", function(done){
		db.find({_isUser: {$exists: true}}).toArray(function(err, ret){
			if(err) throw err;
			for(var i in ret) if(ret.hasOwnProperty(i)){
				var user = ret[i];
				db.find({_mtid: user._id, _typeid: "pageview"}).toArray(function(err, ret){
					if(err) throw err;
					for(var j in ret) if(ret.hasOwnProperty(j)){
						var action = ret[j];
						if( user._os.indexOf(action._os) == -1)
						{
							console.log(j);
							fail("some os not merged");
							done();
						}
					}
				});
			}
		});
console.log(2);
		expect(true).toBe(true);
		setTimeout(done, 2000);
	});
	it("", function(done){});
	it("", function(done){});
	it("", function(done){});
	it("", function(done){});
	it("", function(done){});
	it("", function(done){});

	});
});
