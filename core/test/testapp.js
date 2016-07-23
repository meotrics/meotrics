return;
var request = require('request');
var config = require('config');
var baseurl = 'http://127.0.0.1:' + config.get('port') + '/';
request.get(baseurl + 'app/init/100', function (error, response, body) {
	if (!error && response.statusCode == 200) {
//		console.log(body)
	}
	else
		console.log("err");
});
