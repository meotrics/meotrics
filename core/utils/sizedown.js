var config = require('config');
var async = require('async');
var mongodb = require('mongodb');
var MongoClient = mongodb.MongoClient;
var converter = require('./fakeidmanager.js');
converter = new converter.IdManager();
var ActionMgr = require('../module/actionmgr');

var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');

var prefix = config.get('mongod.prefix');
var appid = "toyotacomvn";
var col = `${prefix}app${appid}`;

function doNextBunch(db, lastid, callback)
{
	//if(lastid == undefined) lastid= "5792e16a60c2039d4eaad0d2";
	var usercursor;
	if(lastid == undefined)
		usercursor = db.collection(col).find({_isUser: true}).sort({ _id :-1}).limit(100);
	else
		usercursor = db.collection(col).find({_isUser:true, _id: {$lt: new mongodb.ObjectId( lastid)}}).sort({ _id :-1}).limit(100);
	var lastidfound;
	
	usercursor.count(function(err, usercount){
		if(err) throw err;
		if(usercount == 0) callback(undefined);
		usercursor.each(function(err, user){
			if(user==null) return;
			lastidfound = user._id;

			// loop through each pageview of the user
			converter.toIDs(['_utm_source', '_utm_campaign', '_utm_content', '_utm_term', '_utm_medium', '_os', '_lang','_segment', '_devicetype', '_deviceid', '_browser', '_city', '_country', '_campaign'], function (ids) {
				user[ids._utm_source] = [... new Set(user[ids._utm_source])];
				user[ids._utm_medium] = [... new Set(user[ids._utm_medium])];
				user[ids._utm_content] = [... new Set(user[ids._utm_content])];
				user[ids._utm_term] = [... new Set(user[ids._utm_term])];
				user[ids._utm_campaign] = [... new Set(user[ids._utm_campaign])];

				db.collection(col).update({_id: user._id}, {$set: user},function(err, ret){
					usercount--;
					if(usercount == 0)
					{
						return callback(lastidfound);
					}
				});		
			});
		});
	});
}

exports.sync = function(appid, done)
{
	MongoClient.connect(url, function(err, db){
		//	var actionmgr = new ActionMgr.ActionMgr(db, converter, prefix, "mapping", undefined, undefined);
		if(err) throw err;
		// loop through each user in the system
		
		function callbackNextBunch(lastid)
		{
			console.log('next bunch');
			if(lastid == undefined) {
				console.log('done');
				process.exit();
			}
			doNextBunch(db, lastid, callbackNextBunch);
		}
		doNextBunch(db, undefined, callbackNextBunch);
	});
}

exports.sync(appid, function(){});
