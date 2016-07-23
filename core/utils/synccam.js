var config = require('config');
var async = require('async');
var mongodb = require('mongodb');
var MongoClient = mongodb.MongoClient;
var converter = require('./fakeidmanager.js');
converter = new converter.IdManager();
var ActionMgr = require('../module/actionmgr');

var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');

exports.sync = function(appid, done)
{
	var prefix = config.get('mongod.prefix');
	var appid = "testsync";
	var col = `${prefix}app${appid}`;
	MongoClient.connect(url, function(err, db){
		var actionmgr = new ActionMgr.ActionMgr(db, converter, prefix, "mapping", undefined, undefined);
		if(err) throw err;
		// loop through each user in the system
		var usercursor = db.collection(col).find({_isUser: true});
		usercursor.count(function(err, usercount){
			if(usercount == 0 ) return done();
			usercursor.forEach(function(user){
				
				// loop through each pageview of the user
				var pageviewcursor = db.collection(col).find({_mtid: user._id, _typeid: "pageview"});
				pageviewcursor.count(function(err, pageviewcount){
					if(pageviewcount == 0)
					{
						usercount--;
						if(usercount == 0) return done();
					}

					pageviewcursor.forEach(function(pageview){
						converter.toIDs(['_os', '_lang','_segment', '_devicetype', '_deviceid', '_browser', '_city', '_country', '_campaign'], function (ids) {
							actionmgr.mergeInfo(pageview, user, ids);
							pageviewcount--;
							if(pageviewcount==0)
							{
								//save the user
								db.collection(col).update({_id: user._id}, {$set: user},function(err, ret){
									usercount--;
									if(usercount == 0) return done();
								});
							}
						});
					});
				});
			});
		});
	});
}
