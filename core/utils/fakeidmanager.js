'use strict';
//will call callback after call done() for n times
exports.IdManager = function () {
	var me = this;


	this.toIDs = function (names, callback) {
		var outs = {};
		var n = names.length;
		for (var i in names) {
			(function (j) {
				me.toID(names[j]).then(function (out) {
					n--;

					outs[names[j]] = out;
					if (n == 0)
						callback(outs);
				});
			})(i);
		}
	};

	this.toID = function (name) {

		var sucback;
		var errback;
		var p = new Promise(function (resolve, reject) {
			sucback = resolve;
			errback = reject;
		});


		// escape case

		(function () {
			sucback(name)
		})();

		return p;
	};

	this.toOriginal = function (object) {
		var sucback;
		var errback;
		var p = new Promise(function (resolve, reject) {
			sucback = resolve;
			errback = reject;
		});
		(function () {
			sucback(object)
		})();

		return p;
	};

	this.toObject = function (object) {
		var sucback;
		var errback;
		var p = new Promise(function (resolve, reject) {
			sucback = resolve;
			errback = reject;
		});

		(function () {
			sucback(object)
		})();

		return p;
	}

};
