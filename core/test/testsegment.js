var seg = require('../utils/excuteSegmentv2.js');
var MongoClient = require('mongodb').MongoClient;
var config = require('config');
var testJson =
		[{
			type: "56e3a14a44ae6d70ddbf82a2", f: "avg", field: "amount", operator: ">", value: 5,
			conditions: ["amount", "gt", 2, "and", "price", "eq", 40]
		},
			"and",
			{
				type: "56e3a14a44ae6d70ddbf82a2", f: "count", field: "pid", operator: ">", value: 5,
				conditions: ["amount", "gt", 5, "or", "price", "eq", "30"]
			},
			"and",
			{
				type: 'user',
				conditions: ['age', 'eq', 'male']
			}];

function mtthrow(err) {
	if (err) {
		console.log(err);
		setTimeout(function () {
			throw err;
		});
	}
}

var testJson2 =
		[{
			type: "56e3a14a44ae6d70ddbf82a2", f: "avg", field: "quantity", operator: ">", value: 0,
			conditions: []
		}];


seg.getQuery( testJson2, function (out) {

	console.log('--TEST1');
	console.log(JSON.stringify(out));


	var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');
	var collection = process.argv[2];

	MongoClient.connect(url)
			.then(function (db) {
				db.collection(collection).mapReduce(out.map, out.reduce, {out: "meotrics_out", query: out.option, finalize: out.finalize, jsMode: true, sort:{_mtid:1}})
	
				}).catch(mtthrow);
});

