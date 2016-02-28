var express = require('express');
var config = require('config');

var app = express();


var port = config.get("port");

app.get('/', function (req, res) {
  res.send('Hello World!');
});

app.listen(port, function () {
  console.log('Example app listening on port ' + port +'!');
});