var config = require('config'),
	express = require('express'),
	app = express(),
	bodyParse = require('body-parser'),
	mongodb = require('mongodb'),
	MongoClient = mongodb.MongoClient,
	url = 'mongodb://localhost:1234/test',
	db = null,
	collection = "users";// change collection depend on appid, this is for test only

// parse application/json
app.use(bodyParser.json());

// CRUD

app.post('/user', function(req, res){
	var data = req.body;

	db.collection(collection).insertOne(data)
        .then(function(results){
        	console.log(results);
        }).catch(function(err){
        	console.error(err.message);
            
        });
});

app.get('/user/:uid', function(req, res){
	var uid = req.params.uid;

	db.collection(collection).findOne({_id: new mongodb.ObjectID(uid)}).toArray()
		.then(function(results){
			console.log(results);
		}).catch(function(err){
			console.error(err.message);
		});
});

app.put('/user/:uid', function(req, res){
	var uid = req.params.uid;
	var data = req.body;
	db.collection(collection).updateOne({_id: new mongodb.ObjectID(uid)}, {$set: data})
		.then(function(results){

		}).catch(function(err){
			console.error(err.message);
		});
});

app.delete('/user/:uid', function(req, res){
	var uid = req.params.uid;
	db.collection(collection).remove({_id: new mongodb.ObjectID(uid)})
    	.then(function(results){

    	}).catch(function(e){
        	console.error(e.message);

    	});
});



function connectMongoDB(){
	MongoClient.connect(url)
	  	.then(function(database){
		    console.log('[MongoDB] connected');

		    db = database;	  

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

function startWebserver(){
	app.listen(config.get('port'), function(){
		console.log('Example app listening on port ' + config.get('port') +'!');
	});
}

// startWebserver();


