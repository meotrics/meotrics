"use strict";
var Lock = require('lock');
const regesc = require('escape-string-regexp');

exports.ValueMgr = function (db, prefix) {
	var lock = Lock();
	this.cineObject = function (appid, typeid, obj) {
		for (var i in obj) if (obj.hasOwnProperty(i)) {
			if (i === '_id' || i === "_mtid" || i === '_isUser' || i === '_ctime' || i === '_segments' || i === '_typeid')
				continue;
			this.cineValue(appid, typeid, i, obj[i]);
		}
	};

	this.suggest = function (appid, typeid, field, query, callback) {
			db.collection(prefix + 'valuedomain').find({
				appid: (appid + "").toLowerCase(),
				typeid: (typeid + "").toLowerCase(),
				field: (field + "").toLowerCase(),
				value: new RegExp(regesc(query), "i")
			}, {value: 1, _id: 0}).limit(50).toArray(function (err, ret) {
				if (err) throw err;

				// delete invalid element
				var ret2 = []
				for (var i in ret) if (ret.hasOwnProperty(i)) {
					if (ret[i].value == '' || ret[i].value == null || ret[i].value == undefined) continue;
					ret2.push(ret[i]);
				}
				callback(ret2);
			});
	};

	// create if not exist (cine)
	// value can be array or string, if value is an array, this method do not
	// guarrenty the order of value
	this.cineValue = function (appid, typeid, field, value) {
		// if value is an array then return array of converted value
		if (value instanceof Array) {
			for (var i in value) if (value.hasOwnProperty(i))
				this.cineValue(appid, typeid, field, value[i]);
			return;
		}

		var record = {
			appid: (appid + "").toLowerCase(),
			typeid: (typeid + "").toLowerCase(),
			field: (field + "").toLowerCase(),
			value: (value + "").toLowerCase()
		};
		console.log("record");
		console.log(record);

		var lockstr = appid + ":" + typeid + ":" + field + ":" + value;
		lock(lockstr, function (release) {

			// only run on a node (not sharded)
			db.collection(prefix + 'valuedomain').count(record, {limit: 1}, function (err, ret) {
				if (err) throw err;
				if (ret === 0) {
					db.collection(prefix + 'valuedomain').insertOne(record, function (err, ret) {
						if (err) throw err;
						release();
					});
				}
			});
		});
	};
};
