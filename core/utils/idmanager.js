'use strict';
var config = require('config');
var redis = require("redis");
var trycatch = require('trycatch');

//will call callback after call done() for n times
exports.IdManager = function () {
	var me = this;

	function zip(number) {
		return toRadix(number, 61);
	}

	function unzip(string) {
		return fromRadix(string, 61);
	}

	function fromRadix(v, radix) {
		var digits = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		function ZdigitToInt(ch) {
			for (var i = 0; i < digits.length; i++)
				if (ch == digits.charAt(i))
					return (i - 0);
			return 0;
		}

		// Parse input value
		var sign = v.substr(0, 1);
		if (sign == "+" || sign == "-")
			v = v.substr(1);
		else
			sign = "";

		// Convert the input value to a number in the input base
		var r = 0;
		while (v != "") {
			var d = v.substr(0, 1);
			d = ZdigitToInt(d);
			v = v.substr(1);
			r = r * radix + d;
		}
		v = r;

		// Convert the number to the output base
		r = "";
		do
		{
			var d = Math.round(v % 10, 0);
			v = Math.round((v - d) / 10, 0);
			r = digits.charAt(d) + r;
		} while (v > 0);
		r = sign + r;
		return r;
	}

	function toRadix(N, radix) {
		var HexN = "", Q = Math.floor(Math.abs(N)), R;
		while (true) {
			R = Q % radix;
			HexN = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz".charAt(R)
					+ HexN;
			Q = (Q - R) / radix;
			if (Q == 0) break;
		}
		return ((N < 0) ? "-" + HexN : HexN);
	}

	var db = redis.createClient(config.get('redis.port'), config.get('redis.host'));
	db.auth(config.get('redis.password'), function () {
	});

	var lastid = undefined;

	function newID(callback) {
		if (lastid == undefined) {
			db.get('lastid', function (err, value) {
				if (err) throw err;
				if (value == null) value = 1;
				if (lastid !== undefined)
					return newID(callback);
				lastid = value;
				return newID(callback);
			});
		}
		else {
			lastid++;
			db.set('lastid', lastid);
			callback(lastid);
		}
	}

	this.toIDs = function (names, callback) {
		var outs = {};
		var n = names.length;
		for (let i in names) if (names.hasOwnProperty(i)) {
			me.toIDcb(names[i], function (out) {
				n--;
				outs[names[i]] = out;
				if (n == 0)
					callback(outs);
			});
		}
	};

	this.toIDcb = function (name, callback) {
		var defname = name;
		// escape case
		if (name == '_id') {
			return callback(name);
		}
		else {
			if (name.startsWith('$'))
				name = (name.substring(1));

			db.get(' ' + name, function (err, value) {
				if (err) throw err;
				//name doesn't exist yet
				if (value == null) {
					newID(function (newid) {
						db.set(newid, name);
						db.set(' ' + name, newid);
						db.set('_' + zip(newid), defname);
						if (name.startsWith("$")) callback('$' + zip(newid));
						else callback(zip(newid));
					});
				}
				else {
					if (name.startsWith("$"))callback('$' + zip(value));
					else callback(zip(value));
				}
			});
		}
	};

	this.toID = function (name, callback) {
		if (callback !== undefined)
			return me.toIDcb(name, callback);

		var defname = name;
		var sucback;
		var errback;
		var p = new Promise(function (resolve, reject) {
			sucback = resolve;
			errback = reject;
		});


		// escape case
		if (name == '_id') {
			(function () {
				sucback(name)
			})();
		}
		else {
			if (name.startsWith('$'))
				name = (name.substring(1));

			db.get(' ' + name, function (err, value) {
				if (err) return errback(err);
				//name doesn't exist yet
				if (value == null) {
					trycatch(function () {
						newID(function (newid) {
							db.set(newid, name);
							db.set(' ' + name, newid);
							db.set('_' + zip(newid), defname);
							if (name.startsWith("$")) sucback('$' + zip(newid));
							else sucback(zip(newid));
						});
					}, function (err) {
						errback(err);
					});
				}
				else {
					if (name.startsWith("$")) sucback('$' + zip(value));
					else sucback(zip(value));
				}
			});
		}
		return p;
	};

	this.toOriginal = function (object, callback) {
		if (callback) {
			var newobj = {};
			var ci = 0;
			for (let i in object) {
				if (i == '_id') {
					(function () {
						newobj[i] = object[i];
						ci--;
						if (ci == 0) return callback(newobj);
					})();
				}
				else {
					var j = i;
					if (j.startsWith('$'))
						j = j.substring(1);
					db.get('_' + j, function (err, value) {
						if (err) return errback(err);
						if (i.startsWith('$'))
							newobj['$' + value] = object[i];
						else {
							newobj[value] = object[i];
						}
						ci--;
						if (ci == 0) return callback(newobj);
					});
				}
				ci++;
			}
			return;
		}


		var sucback;
		var errback;
		var p = new Promise(function (resolve, reject) {
			sucback = resolve;
			errback = reject;
		});

		trycatch(function () {
			var newobj = {};
			var ci = 0;
			for (let i in object) {
				if (i == '_id') {
					(function () {
						newobj[i] = object[i];
						ci--;
						if (ci == 0) return sucback(newobj);
					})();
				}
				else {
					var j = i;
					if (j.startsWith('$'))
						j = j.substring(1);
					db.get('_' + j, function (err, value) {
						if (err) return errback(err);
						if (i.startsWith('$'))
							newobj['$' + value] = object[i];
						else {
							newobj[value] = object[i];
						}
						ci--;
						if (ci == 0) return sucback(newobj);
					});
				}
				ci++;
			}
		}, function (err) {
			errback(err);
		});
		return p;
	};

	this.toObject = function (object, callback) {
		if(callback !== undefined)
		{
			var arrprop = [];
			for (var i in object) if(object.hasOwnProperty(i)) {
				arrprop.push(i);
			}

			me.toIDs(arrprop, function(newobj)
			{
				callback(newobj);
			});
			return;
		}

		var sucback;
		var errback;
		var p = new Promise(function (resolve, reject) {
			sucback = resolve;
			errback = reject;
		});
		var newobj = {};
		var ci = 0;

		trycatch(function () {
			for (let i in object) {
				ci++;
				me.toID(i).then(function (value) {
					newobj[value] = object[i];
					ci--;
					if (ci == 0) return sucback(newobj);
				}).catch(function (err) {
					errback(err);
				});
			}
		}, function (err) {
			errback(err);
		});
		return p;
	}
};
