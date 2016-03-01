
//concat multiple javascript object 
function collect() {
	var ret = {};
	var len = arguments.length;
	for (var i = 0; i < len; i++) {
		for (p in arguments[i]) {
			if (arguments[i].hasOwnProperty(p)) {
				ret[p] = arguments[i][p];
			}
		}
	}
	return ret;
}

exports.Barrier = function (n, callback) {
	var params = {};
	var me = this;
	this.done = function() {
		for (var i in arguments)
			params = collect(params, arguments[i])
		n--;
		if (n == 0) callback(params);
	}

}