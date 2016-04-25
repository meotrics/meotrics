(function () {
	'use strict';
	var Lock = require('lock');

	exports.ValueMgr = function (db, prefix) {
		var lock = Lock();
		var me = this;
		this.cineObject = function (appid, typeid, obj) {
			for (var i in obj) if (obj.hasOwnProperty(i)) {
				if (i === '_id' || i === "_mtid" || i === '_isUser' || i === 'ctime' || i === '_segments' || i === '_typeid')
					continue;
				this.cineValue(appid, typeid, i, obj[i]);
			}
		};

		this.suggest(appid, typeid)
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
				appid: appid,
				typeid: typeid,
				field: field,
				value: value
			};

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
})();
