var IdManager = require('../utils/idmanager.js').IdManager
var trycatch = require('trycatch');
var idmgr = new IdManager();
trycatch(function()
{
idmgr.toIDs("adf", function(data)
{

})
}, function(err)
{
//	console.log("hihi" + err);
});
