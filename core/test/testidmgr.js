var IdManager = require('../utils/idmanager.js').IdManager;


function mtthrow(err)
{
	if(err){
		console.log(err);
		setTimeout(function(){
			throw err;
		});

    throw err;
	}
}


var idmgr = new IdManager();
var obj = {$namefff: 'thanh', surff: 'kieu', '_id': 4};
idmgr.toObject(obj).then(function(ret, val){
  console.log( ret);

  idmgr.toOriginal(ret).then(function(rets, val){
    console.log(rets);
  }).catch(mtthrow)
}).catch(mtthrow);
