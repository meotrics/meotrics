(function () {
	'use strict';
	exports.ValueMgr = function (db, prefix) {
		var me = this;
		this.cineObject = function (appid, typeid, obj) {
			for (var i in obj) if (obj.hasOwnProperty(i)) {
				if (i === '_id' || i === "_mtid" || i === '_isUser' || i === 'ctime' || i === '_segments' || i === '_typeid')
					continue;
				this.cineValue(appid, typeid, i, obj[i]);
			}
		};

		// create if not exist (cine)
		// value can be array or string, if value is an array, this method do not
		// guarrenty the order of value
		this.cineValue = function (appid, typeid, field, value) {
			// if value is an array then return array of converted value
			if (value instanceof Array)
				for (var i in value) if (value.hasOwnProperty(i))
					this.cineValue(appid, typeid, field, value[i]);

			var record = {
				appid: appid,
				typeid: typeid,
				field: field,
				value: value
			};

			db.collection(prefix + 'valuedomain').find(record).toArray(function (err, ret) {
				if (err) throw err;
					if (ret.length === 0) {
						db.collection(prefix + 'valuedomain').insertOne(record, function (err, ret) {
							if (err) throw err;
						});
					}

			});
		};
	};
})();
