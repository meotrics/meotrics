var config = require('config');
var async = require('async');
var mongodb = require('mongodb');
var prefix = config.get("mongod.prefix");

var Util = function(db, converter){
	this.db = db;
	this.converter = converter;
}

module.exports = Util;

Util.prototype.updateUserInf = function(appid, _mtid, data){
	console.log(data);
	var collection = prefix + appid;

	var keys = Object.keys(data);
	var _self = this;

	async.each(keys, function(key, callback){
		var fieldInDb;
		var query;

		_self.converter.toID(key).then(function(r){
			fieldInDb = r;
			query = {_id: new mongodb.ObjectID(_mtid)};
			var projection = {};
			projection[fieldInDb] = 1;
			return _self.db.collection(collection).find(query, projection).toArray();
		}).then(function(r){
			if(r.length == 0){
				callback(null);
			}else{
				var record = r[0];
				var temp = [];

				if(record[fieldInDb] != undefined){
					temp = record[fieldInDb];
				}
				var value = data[key];
				var length = temp.length;
				for(var i=0;i<length;i++){
					if(temp[i] == value){
						break;
					}
				}

				if(i == length){
					temp.push(value);
					temp.sort();
					var update = {};
					update[fieldInDb] = temp;

					_self.db.collection(collection).updateOne(query, {"$set": update}).then(function(r){
						callback(null);
					}).catch(function(e){
						callback(e);
					});
				}else{
					callback(null);
				}

			}
		}).catch(function(e){
			callback(e);
		});
	}, function(err){
		if(err){
			// TODO:
			console.log(err);
		}else{
			// TODO:
		}
	});
};


