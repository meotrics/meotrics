var IdManager = require('../utils/idmanager.js').IdManager;

var idmgr = new IdManager();
var obj = {$name: 'thanh', sur: 'kieu'};
idmgr.toObject(obj).then(function(ret, val){
  console.log( ret);
});
