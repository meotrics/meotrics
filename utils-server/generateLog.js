var MongoClient = require('mongodb').MongoClient,
    url = 'mongodb://localhost:1234/test',
    n = 200000,
    collection = 'actions',
    users = null,
    numberUsers = 0,
    db = null;

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
            return db.collection('users').find().toArray();
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
		appid: user.appid,
		actiontype: 'pageview',
		url: 'http://' + generateNumber(1, 1000) + '.com',
		ctime: new Date(new Date().getTime() + generateNumber(-1, 1)*3600000),
		user: user
	}
}

function generateNumber(min, max){
    var range = max - min;
    var temp = range.toString();
    var str = "1";
    for(var i=0;i<temp.length;i++){
        str += '0';
    }
    temp = parseInt(str);

    var number = Math.floor((Math.random()*temp)/(temp/(range+1)))+min;
    return number;
}

function generateDB(){
    var count = 0;
    for(var i=0;i<n;i++){
        db.collection(collection).insertOne(generatePageView())
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
