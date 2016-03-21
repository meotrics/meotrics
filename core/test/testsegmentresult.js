var segmentresult = require('../utils/segmentresult');

segmentresult('meotrics_1', '56e81a3344ae6d1f522e94da', 'age', 'number', 'age', 'number', function(err, results){
	          console.log(err);
	          console.log(JSON.stringify(results));
	        })