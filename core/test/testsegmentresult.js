var segmentresult = require('../utils/segmentresult');
var collection = config.get('mongodb.prefix') + process.argv[2];
var segmentid = process.argv[3];
console.time('sr');
segmentresult(collection, segmentid, 'age', 'number', 'gender', 'number', function(err, results){
	          console.log(err);
	          console.log(JSON.stringify(results));
	          console.timeEnd('sr');
})