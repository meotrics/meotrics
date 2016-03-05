var MongoClient = require('mongodb').MongoClient,
    url = 'mongodb://localhost:1234/local',
    n = 100000,
    collection = 'meotrics_1',
    users = null,
    numberUsers = 0,
    db = null;

    var mongodb = require('mongodb');

function getUsers(){
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
            return db.collection('meotrics_1').find({isUser: true}).toArray();
        }).then(function(results){
            users = results;
            numberUsers = users.length;
            generateDB();
        }).catch(function(err){
            console.error("[MongoDB]", err.message);
            setTimeout(getUsers, 2000);
        }); 
}

function generatePageView(){
	var user = users[generateNumber(0, numberUsers-1)];
	return {
		typeid: new mongodb.ObjectID("56dab10544aee0d1bd499a27"),
		url: 'http://' + generateNumber(1, 1000) + '.com',
		ctime: Math.floor(new Date().getTime()/1000),
		mtid: user._id
	}
}

function generatePurchase(){
    var user = users[generateNumber(0, numberUsers-1)];
    var pid = generateNumber(1, 1000);
    return {
        typeid: new mongodb.ObjectID("56dab10c44aee0d1bd499a29"),
        ctime: Math.floor(new Date().getTime()/1000),
        mtid: user._id,
        cid: pid%10,
        pid: pid,
        amount: generateNumber(1, 10),
        price: generateNumber(10, 200)
    }
}

function generateNumber(min, max){
    var range = max - min;

    var number = Math.floor(Math.random()*(range+1))+min;
    return number;
}

function generateDB(){
    var count = 0;
    for(var i=0;i<n;i++){
        db.collection(collection).insertOne(generatePurchase())
            .then(function(results){
                count++;
                console.log(count + ' records');
                if(count == n){
                    db.close();
                    console.log('Done');
                }
            }).catch(function(err){
                console.log('[MongoDB] insert err', err.message);
            });
    }
}

getUsers();
