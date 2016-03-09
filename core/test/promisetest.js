var p = new Promise( function(a,b, resolve, reject){
  var r = a+ b;
  resolve(r);
});

p(4,3).then(function(r){
  console.log(r);
})
