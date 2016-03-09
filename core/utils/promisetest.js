var p = new Promise(function(resolve, reject){
  var r = 5+4;
  resolve(r);
});

p.then(function(r){
  console.log(r);
})
