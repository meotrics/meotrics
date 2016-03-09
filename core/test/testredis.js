var config = require('config')
var redis = require("redis")

console.log(config.get('redis.host'));
var    client = redis.createClient(  config.get('redis.port'),config.get('redis.host'))

console.time('test');
client.auth(config.get('redis.password'), function(){
  client.set("some key", "some val");
  client.get('some key',function( err,value){
  console.log(value);
  console.timeEnd('test');
  process.exit(1);
});
});
