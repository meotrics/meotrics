var mongodb = require('mongodb'),
    MongoClient = mongodb.MongoClient,
    url = 'mongodb://localhost:1234/test',
    db = null;

MongoClient.connect(url)
    .then(function(database){
        //console.log('[MongoDB] connected');
        db = database;
        
        db.collection('meotrics_1').mapReduce(
            map,
            reduce,
            {
                out: {replace: 'hehe'}
            }
        ).then(function(r){
            //console.log(r);
        }).catch(function(e){
            //console.log(e);
        });

        // Listen for some events
        db.on('reconnect', function(data){
          //console.log(data);
          //console.log('[MongoDB] reconnect success');
        });
        db.on('error', function(err){
          //console.log('[MongoDB] error', err.message);
        });
        db.on('close', function(err){
          //console.log('[MongoDB] disconnected');
        });
    }).catch(function(err){
        //console.error("[MongoDB]", err.message);
        setTimeout(getUsers, 2000);
    }); 

var map = function(){
  var key = this['4'];
  var value = {
    time: this['6']
  }
  emit(key, value);
};

var reduce = function(key, values){
  return {objectID: ObjectId('56e0cc778c35e0d916ff841c')};
}
