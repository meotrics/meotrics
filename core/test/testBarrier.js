var Barrier = require('../utils/barrier.js').Barrier;

function simpletest() {
	var b = new Barrier(2, function (param) {
		console.log(param);
	});
	
	function t1(){
		setTimeout(function()
		{
			b.done({s1:1},{t1: 4});
		},400);
	}
	
	function t2(){
		setTimeout( function()
		{
			b.done({s2:6, t2: 8});
		},200);
	}
	
	t1();
	t2();
}

simpletest();