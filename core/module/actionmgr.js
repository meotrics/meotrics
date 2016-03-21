exports.ActionMgr = function (db, mongodb, async, converter, prefix, mtthrow) {
    //CLIENT------------------------------------------------------------------------

    /* Ghi nhận một action mới
     Tham số: {
     _mtid : number
     _typeid: number // type of action, eg: "purchase", "pageview"
     osid: number // os information, eg: "window", "linux",
     browserid : number // eg, "chrome", "firefox", "opera",
     locationid : number // location code, eg: Hanoi, Vietnam
     referer: string
     campaignid : number
     deviceid : number
     _ctime: date // created time
     ip: string // public ip address
     screenres: string // screen resolution of the device
     totalsec: number // total number of sec to finish the action
     url: string
     browserversion : number
     osversion : number
     userfields = {field1:10, field2: 345};
     }
     */

    this.save = function (req, res) {
        var data = req.body;
        var collection = prefix + req.params.appid;
        // Convert string to ObjectID in mongodgodb
        data._mtid = new mongodb.ObjectID(data._mtid);
        data._typeid = new mongodb.ObjectID(data._typeid);
        data._segments = [];
        // Add created time
        data._ctime = Math.round(new Date() / 1000);
        converter.toObject(data)
            .then(function (results) {
                return db.collection(collection).insertOne(results);
            }).then(function (r) {
            res.status(200).end();
        }).catch(mtthrow);
    };

    /* Phương thức này dùng để báo cho hệ thống biết một anonymous user thực ra là
     một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png

     Tham số:
     {
     cookie: string, //mtid của anonymous user
     user: {userid, name, email, age, birth, gender, ...}
     }

     Điều kiện:
     1. Toàn bộ actions thuộc anonymous user được sang tên cho user, thông tin về
     user được cập nhập
     Chú ý: nếu userid không tồn tại trong hệ thống, thì cập nhật luôn userid của anonymous
     user thành userid ở tham số.
     2. Toàn bộ thông tin về user được cập nhật mới.
     */
    this.identify = function (req, res) {
        var data = req.body;
        var collection = prefix + req.params.appid;
        var userConverted;

        async.waterfall([
            function (callback) {
                var query;
                converter.toObject({_isUser: true, uid: data.user.uid})
                    .then(function (r) {
                        query = r;
                        return converter.toObject(data.user);
                    }).then(function (r) {
                    userConverted = r;
                    return db.collection(collection).findOneAndUpdate(query, {$set: userConverted}, {projection: {_id: 1}});
                }).then(function (r) {
                    if (r.value != null) {
                        var _mtid = r.value._id;
                        res.send(_mtid);
                        callback(null, true, _mtid);
                    } else {
                        res.send(data.cookie);
                        callback(null, false, '');
                    }
                }).catch(function (err) {
                    // [ERROR]
                    callback(err);
                });
            }, function (isCreated, _mtid, callback) {
                if (isCreated) {
                    callback(null, true, _mtid);
                } else {
                    db.collection(collection).updateOne({_id: new mongodb.ObjectID(data.cookie)}, {$set: userConverted}, function (err, result) {
                        if (err) callback(err);
                        else callback(null, false, '');
                    });
                }
            }, function (needUpdate, _mtid, callback) {
                if (needUpdate) {
                    converter.toID('_mtid')
                        .then(function (id) {
                            var query = {};
                            var update = {};
                            query[id] = new mongodb.ObjectID(data.cookie);
                            update[id] = new mongodb.ObjectID(_mtid);
                            return db.collection(collection).updateMany(query, {$set: update});
                        }).then(function (r) {
                        return db.collection(collection).deleteOne({_id: new mongodb.ObjectID(data.cookie)});
                    }).then(function (r) {
                        callback(null);
                    }).catch(function (err) {
                        callback(err);
                    });
                } else {
                    callback(null);
                }
            }
        ], mtthrow);
    };

    /* Thiết lập cookie mới cho người dùng mới
     Tham số: {
     appid: number
     }
     Điều kiện:
     Một bản ghi user được tạo trong collection của appid, thông tin trống rỗng
     Đầu ra:
     mtid vừa được tạo
     */
    this.setup = function (req, res) {
        var collection = prefix + req.params.appid;
        var query = {
            _isUser: true,
            _segments: []
        }
        converter.toObject(query)
            .then(function (r) {
                return db.collection(collection).insertOne(r);
            }).then(function (results) {
            var _mtid = results.insertedId;
            res.send(_mtid);
        }).catch(mtthrow);
    };
}