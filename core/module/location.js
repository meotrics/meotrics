"use strict";
const request = require('request');
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
            request('http://ipinfo.io/' + ip, function (err, res, body) {
                console.log(body);
                if (!err && res.statusCode == 200) {
                    callback(body);
                    body = JSON.parse(body);
                    console.log(body);
                    me.collection.insertOne(body, function (err, ret) {
                        console.log(1003);
                        if (err)
                            throw err;
                    });
                }
            });
        });
    }
}
exports.LocationMgr = LocationMgr;
//# sourceMappingURL=location.js.map