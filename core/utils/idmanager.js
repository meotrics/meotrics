
var redis = require('then-redis');

var db = redis.createClient();

//will call callback after call done() for n times
exports.IdManager = function (n, callback) {

  var initcallbacks=[];



  this.toID = function(name){
    
  }

  this.toObject = function( object)
  {

  }

	var params = {};
	this.done = function () {
		for (var i in arguments)
			params = collect(params, arguments[i])
		n--;
		if (n == 0) callback(params);
	};
}
