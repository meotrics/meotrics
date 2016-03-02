var mmh3 = require('murmurhash3');

var N = 100000;

console.time('hash');
var i2 = 0;
for (var i = 0; i < N; i++) {
	mmh3.murmur32(i + "", function (err, hashbalue) {
		i2++;
		if (i2 == N)
			console.timeEnd('hash');
		if (err) throw err;
	});

}

//total time: 1.63 s <--- TOO BAD