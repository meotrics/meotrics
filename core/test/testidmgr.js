var IdManager = require('../utils/idmanager.js').IdManager;


function mtthrow(err) {
	if (err) {
		console.log(err);
		setTimeout(function () {
			throw err;
		});

		throw err;
	}
}


var idmgr = new IdManager();
var obj = {$namefff: 'thanh', surff: 'kieu', '_id': 4};

idmgr.toID('_iddd').then(function (ret, val) {
	console.log('ret= ' + ret);
});

idmgr.toObject(obj).then(function (ret, val) {
	console.log("toObject" + JSON.stringify(ret));

	idmgr.toOriginal(ret).then(function (rets, val) {
		console.log("toOriginal" + JSON.stringify(rets));
	}).catch(mtthrow)
}).catch(mtthrow);


var trycatch = require('trycatch');
idmgr.toIDs(['_id', 'thanh', 'surff'], function (out) {
			console.log("toIDs " + JSON.stringify(out));
		}
);

trycatch(function () {
	idmgr.toIDs(['_id', 'thanh', 'surff'], function (out) {
				throw "1";
			}
	);
}, function (err) {
	console.log("hidhi" + err);
});
