"use strict";
const http = require('http');
class LocationMgr {
    constructor(db, prefix) {
        this.db = db;
        this.prefix = prefix;
        this.collection = db.collection(prefix + "ip");
    }
    parse(ip, callback) {
        var me = this;
        // 1 find in database
        me.collection.find({ ip: ip }).limit(1).toArray(function (err, res) {
            if (err)
                throw err;
            if (res.length !== 0)
                return callback(res[0]);
            // 2 using the api
            http.get('http://ipinfo.io/' + ip, function (res) {
                callback(res);
                me.collection.insertOne(res, function (err, ret) {
                    if (err)
                        throw err;
                });
            });
        });
    }
}
exports.LocationMgr = LocationMgr;
//# sourceMappingURL=location.js.map