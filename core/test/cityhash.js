var cityhash = require('node-cityhash');
var hash;
var N = 6 ;

console.time('hash');

for (var i = 0; i < N*120000; i++) {
	hash = cityhash.hash64('Hello');
}
console.timeEnd('hash');

