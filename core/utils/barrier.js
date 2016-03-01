
//concat multiple javascript object 
function collect() {
	var ret = {};
	for (var i in arguments) {
		for (var p in arguments[i]) if (arguments[i].hasOwnProperty(p)) {
			ret[p] = arguments[i][p];
		}
	}
	return ret;
}

//will call callback after call done() for n times
exports.Barrier = function (n, callback) {
	var params = {};
	this.done = function () {
		for (var i in arguments)
			params = collect(params, arguments[i])
		n--;
		if (n == 0) callback(params);
	};
}