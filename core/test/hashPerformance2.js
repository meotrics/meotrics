
var N = 6000;

console.time('hash');
var a = 0;
for (var i = 0; i < N * 120000; i++) {
	a = 434*44*i;
}
console.timeEnd('hash');
console.log("a = " + a);