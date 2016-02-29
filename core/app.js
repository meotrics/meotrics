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


//APP------------------------------------------------------------------------
//set up an new app
app.post('/app', function(req,res){

});

//ACTION TYPE-----------------------------------------------------------------
//update or create an action type
app.post('/actiontype', function(req,res)
{
	var sampleActionType = {
		name: "purchase",

	}
});

//get actiontype by id or list
app.get('/actiontype', function(req,res)
{
	var sampleActionType = {
		name: "purchase",

	}
});

//delete an actiontype
app.delete('/actiontype', function(req, res)
{

});


//TREND-------------------------------------------------------------------------

//get a trend
app.get('/trend', function(req,res){

});




//CLIENT------------------------------------------------------------------------
//record and event
app.post('/r', function recordAnEvent(req, res)
{
	//NOTE: use array instead of json for faster network throughput
});

//identify an user
app.post('/i', function recordAnEvent(req, res)
{
	//NOTE: use array instead of json for faster network throughput

});

//set up new cookie for new visitor
app.post('/s', function recordAnEvent(req, res)
{
	//NOTE: use array instead of json for faster network throughput
});


//SEGMENTATION--------------------------------------------------------------