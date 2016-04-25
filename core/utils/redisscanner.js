"use strict";
class RedisScanner {
    constructor(rdb) {
        this.rdb = rdb;
    }
    count(pattern, callback) {
        function rdscan(cursor) {
            var c = 0;
            this.rdb.scan(cursor, 'MATCH', pattern, function (err, res) {
                if (err)
                    throw err;
                var cursor = res[0];
                if (cursor == 0)
                    if (callback)
                        return callback(c);
                    else
                        return;
                c += res[1].length;
                rdscan(cursor);
            });
        }
        rdscan(0);
    }
    scan(pattern, callback) {
        var keys = new Set();
        function rdscan(cursor) {
            this.rdb.scan(cursor, 'MATCH', pattern, function (err, res) {
                if (err)
                    throw err;
                var cursor = res[0];
                if (cursor == 0)
                    if (callback)
                        return callback(Array.from(keys));
                    else
                        return;
                var ks = res[1];
                for (var i of ks)
                    keys.add(i);
                rdscan(cursor);
            });
        }
        rdscan(0);
    }
}
exports.RedisScanner = RedisScanner;
//# sourceMappingURL=redisscanner.js.map