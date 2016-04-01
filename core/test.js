var domain = require('domain');
var d = domain.create();


function trycatch(fnP, fnT){
    // Domain emits 'error' when it's given an unhandled error
    d.on('error', function(err) {
        console.log(1);
        fnT(err);
    });

// Enter this domain
    d.run(function() {
       fnP();
    });
}

trycatch(function(){
    setTimeout(function(){
        throw new Error('test');
    });
}, function(err){
    console.log(2);
    console.log(err.stack);
});