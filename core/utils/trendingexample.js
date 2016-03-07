var MongoClient = require('mongodb').MongoClient,
	url = 'mongodb://localhost:1234/test',
	db = null,
	collection = "actions";

function connectMongoDB(){
	MongoClient.connect(url)
	  	.then(function(database){
		    console.log('[MongoDB] connected');

		    db = database;	  
		    getTrending({event: "purchase", object: 'pid', order: 1, operation: 'sum', limit: 10, param: 'amount'});

		    // Listen for some events
		    db.on('reconnect', function(data){
		      console.log(data);
		      console.log('[MongoDB] reconnect success');
		    });
		    db.on('error', function(err){
		      console.log('[MongoDB] error', err.message);
		    });
		    db.on('close', function(err){
		      console.log('[MongoDB] disconnected');
		    });
	  	}).catch(function(err){
	    	console.error("[MongoDB]", err.message);
	    	setTimeout(connectMongoDB, 2000);
	  	});	
}

function getTrending(object){
	db.collection(collection).aggregate(getQueryTrending(object)).toArray()
		.then(function(results){
			console.log(results);
		}).catch(function(err){
			console.log(err);
			db.close();
		});
}

function getQueryTrending(object){
	console.log(object);
	var query = [];


	query.push({$match: {actiontype: object.event}});
	if(object.operation == 'count'){
		query.push({$group: {
			_id: '$'+object.object,
			count: {'$sum': 1}
		}});
		query.push({$sort: {count: object.order}});
	}else if(object.operation == 'avg'){
		query.push({$group: {
			_id: '$'+object.object,
			result: {$avg: '$'+object.param}
		}});
		query.push({$sort: {result: object.order}});
	}else if(object.operation == 'sum'){
		query.push({$group: {
			_id: '$'+object.object,
			result: {$sum: '$'+object.param}
		}});
		query.push({$sort: {result: object.order}});
	}
	query.push({$limit: object.limit});
	return query;
}

// connectMongoDB();
var filter = {
	a: [1, 2, 3, 4, 5]
};
console.log(filter);