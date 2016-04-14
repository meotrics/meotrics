var Util = function (db, mongodb,  async, converter, prefix) {
	this.db = db;
	this.converter = converter;
	this.async = async;
	this.prefix = prefix;
};

module.exports = Util;

Util.prototype.updateUserInf = function (appid, _mtid, data) {
	console.log(data);
	var _self = this;
	var collection = _self.prefix + appid;

	var keys = Object.keys(data);

	_self.async.each(keys, function (key, callback) {
		var fieldInDb;
		var query;

		_self.converter.toID(key, function (r) {
			fieldInDb = r;
			query = {_id: new mongodb.ObjectID(_mtid)};
			var projection = {};
			projection[fieldInDb] = 1;
			_self.db.collection(collection).find(query, projection).toArray(function (err, r) {
				if (err) throw err;
				if (r.length == 0) {
					callback(null);
				} else {
					var record = r[0];
					var temp = [];

					if (record[fieldInDb] != undefined) {
						temp = record[fieldInDb];
					}
					var value = data[key];
					var length = temp.length;
					for (var i = 0; i < length; i++) {
						if (temp[i] == value) {
							break;
						}
					}

					if (i == length) {
						temp.push(value);
						temp.sort();
						var update = {};
						update[fieldInDb] = temp;

						_self.db.collection(collection).updateOne(query, {"$set": update}, function (err, r) {
							if (err) throw err;

							callback(null);
						});
					} else {
						callback(null);
					}

				}
			});
		});
	}, function (err) {
		if (err) throw err;

	});
};


