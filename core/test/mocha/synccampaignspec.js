var assert = require('chai').assert;

var synccam = require('../../utils/synccam.js');
var config = require('config');
var async = require('async');
var mongodb = require('mongodb');
var MongoClient = mongodb.MongoClient;
var converter = require('../../utils/fakeidmanager.js');
converter = new converter.IdManager();

var UserG = require('../../utils/generateUsers.js');
var ActionG = require('../../utils/generateLog.js');
var Valuemgr = require('../../module/valuemgr.js').ValueMgr;

var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');

var prefix = config.get('mongod.prefix');
var appid = "testsync";
var col = `${prefix}app${appid}`;

function checkIfCollectionHaveData(db, callback){
	db.collection(col).count( function(err, ret){
		if(err) throw err;
		callback(ret!== 0);
	});
}

function samplingData(db, done)
{
	// insert 10 user
	// insert 100 pageview for 10 user with difference os, browser
	var valuemgr = new Valuemgr(db, prefix);
	checkIfCollectionHaveData(db, function(out){
		if(out == true)
		{
			done();
		}
		else
		{
			UserG.generate(appid, valuemgr, converter, url, 10, col, function(){
				ActionG.generate(appid, valuemgr, converter, url, 100, 100, col, "pageview", "purchase", function(){
					done();
				});
			});
		}
	});
}

describe("Test async", function(done){
	it("should end in 1 sec", function(done){
		setTimeout(function(){
			done();
		},1000);
	});
});

describe("Sync campaign",function(){
	it("should copy all os from pageview to user", function(done){
			MongoClient.connect(url , function (err, db) {
				if(err) throw err;
				samplingData(db, function(){
	synccam.sync(appid, function(){
				db.collection(col).find({_isUser: {$exists: true}}).toArray(function(err, ret){
						if(err) throw err;
						for(var i in ret) if(ret.hasOwnProperty(i)){
							var user = ret[i];
							db.collection(col).find({_mtid: user._id, _typeid: "pageview"}).toArray(function(err, ret){
								if(err) throw err;
								for(var j in ret) if(ret.hasOwnProperty(j)){
									var action = ret[j];
									if(new Set([].concat(user._os).concat(action._os)).size != [].concat(user._os).length)
									//if( user._os.indexOf(action._os) == -1)
									{
										console.log(user._os, action._os);
										assert.fail(0, 1,"some os not merged");
										return;
									}
								}
							});
						}
					});

					//expect(true).toBe(true);
					setTimeout(function(){
						assert.equal(true,true);
						done();
					}, 2000);
				});
			});
		});
	});
});
