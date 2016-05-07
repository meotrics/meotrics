"use strict";
const mongodb = require('mongodb');
const location = require('./location');
class ActionMgr {
    constructor(db, converter, prefix, mapping, valuemgr, referer) {
        this.db = db;
        this.converter = converter;
        this.prefix = prefix;
        this.mapping = mapping;
        this.valuemgr = valuemgr;
        this.referer = referer;
        this.location = new location.LocationMgr(db, prefix);
    }
    // purpose: check if an mtid is valid
    // a mtid is valid if there is one user record based on mtid
    // if a mtid is ano-mtid, convert it to iden-mtid
    ismtidValid(appid, mtids, callback) {
        let collection = this.prefix + "app" + appid;
        let collectionmapping = this.prefix + this.mapping;
        let mtid = new mongodb.ObjectID(mtids);
        var me = this;
        me.converter.toID('_isUser', function (isUser) {
            me.db.collection(collectionmapping).find({ anomtid: mtid }).limit(1).toArray(function (err, r) {
                if (r.length !== 0)
                    mtid = r[0].idemtid;
                // check if user existed
                var query = { _id: mtid };
                query[isUser] = true;
                me.db.collection(collection).find(query).limit(1).toArray(function (err, ret) {
                    if (ret.length === 0)
                        callback(false);
                    else
                        callback(true);
                });
            });
        });
    }
    ;
    // purpose: record an action rawly
    // param:
    // + _deltat: number of delayed second before request sent
    // + _mtid : number
    // + _typeid: string // type of action, eg: "purchase", "pageview"
    // + _osid: number // os information, eg: "window", "linux",
    // + _browser : number // eg, "chrome", "firefox", "opera",
    // + _location : number // location code, eg: Hanoi, Vietnam
    // + _ref: string
    // + _device : number
    // + _ip: string // public ip address
    // + _scrres: string // screen resolution of the device
    // + _totalsec: number // total number of sec to finish the action
    // + _url: string
    // + _browserversion : number
    // + _osversion : number
    // + data1
    // + data2
    // + data3
    saveRaw(appid, data, callback) {
        let me = this;
        var collection = this.prefix + "app" + appid;
        var collectionmapping = this.prefix + this.mapping;
        var mtid = new mongodb.ObjectID(data._mtid);
        data._mtid = mtid;
        var utm_campaign = data._utm_campaign;
        data._segments = [];
        // correct timming
        data._ctime = Math.round(new Date().getTime() / 1000) - (parseInt(data._deltat) ? parseInt(data._deltat) : 0);
        delete data._deltat;
        if (data._link !== undefined) {
            me.db.collection(collection).find({ _id: new mongodb.ObjectID(data._link) }).limit(1).toArray(function (err, ret) {
                if (err)
                    throw err;
                if (ret.length == 0)
                    throw "link not found: " + data._link + ", in app: " + appid;
                let link = ret[0];
                me.converter.toIDs(['_utm_source', '_utm_campaign', '_utm_term', '_utm_content', '_utm_medium'], function (ids) {
                    if (data._utm_source == undefined)
                        data._utm_source = link[ids._utm_source];
                    if (data._utm_campaign == undefined)
                        data._utm_campaign = link[ids._utm_campaign];
                    if (data._utm_term == undefined)
                        data._utm_term = link[ids._utm_term];
                    if (data._utm_content == undefined)
                        data._utm_content = link[ids._utm_content];
                    if (data._utm_medium == undefined)
                        data._utm_medium = link[ids._utm_meidum];
                    lastsave();
                });
            });
        }
        else
            lastsave();
        function lastsave() {
            // retrive real mtid because user can still use old mtid
            me.db.collection(collectionmapping).find({ anomtid: mtid }).limit(1).toArray(function (err, r) {
                if (err)
                    throw err;
                if (r.length !== 0)
                    mtid = r[0].idemtid;
                me.valuemgr.cineObject(appid, data._typeid, data);
                // set referal type
                data._reftype = me.referer.getRefType(data._url, data._ref);
                me.converter.toObject(data, function (datax) {
                    me.db.collection(collection).insertOne(datax, function (err, r) {
                        if (err)
                            throw err;
                        // update location
                        me.location.parse(data._ip, function (res) {
                            var loc = { _city: res.city, _country: res.country };
                            me.converter.toObject(loc, function (datax) {
                                me.db.collection(collection).updateOne({ _id: r.insertedId }, { "$set": loc }, function (err, r) {
                                    if (err)
                                        throw err;
                                });
                                me.db.collection(collection).updateOne({ _id: data._mtid }, { $set: loc }, function (err, r) {
                                    if (err)
                                        throw err;
                                });
                            });
                        });
                        callback(r.insertedId);
                    });
                    //get user infomation
                    me.db.collection(collection).find({ _id: mtid }).limit(1).toArray(function (err, ret) {
                        if (err)
                            throw err;
                        var user = ret[0];
                        if (user === undefined)
                            throw "mtid " + mtid + " did not match any user";
                        var typeid = data._typeid;
                        me.converter.toIDs(['_revenue', '_firstcampaign', '_lastcampaign', '_campaign', '_ctime', '_mtid', '_reftype',
                            '_segments', '_url', '_typeid', '_referer', '_totalsec', 'registed'], function (ids) {
                            // increase revenue
                            var simpleprop = {};
                            if (typeid === 'purchase') {
                                if (user[ids._revenue] === undefined)
                                    user[ids._revenue] = 0;
                                simpleprop[ids._revenue] = user[ids._revenue] + data.amount;
                            }
                            if (typeid === 'pageview') {
                                // record campaign
                                if (utm_campaign) {
                                    if (user[ids._firstcampaign] === undefined) {
                                        simpleprop[ids._firstcampaign] = utm_campaign;
                                    }
                                    simpleprop[ids._lastcampaign] = utm_campaign;
                                    datax[ids._campaign] = utm_campaign;
                                }
                            }
                            if (typeid === 'signup' || typeid === 'login') {
                                simpleprop[ids.registed] = true;
                            }
                            // update user
                            if (Object.keys(simpleprop).length !== 0)
                                me.db.collection(collection).updateOne({ _id: mtid }, { $set: simpleprop }, function (err, r) {
                                    if (err)
                                        throw err;
                                });
                            // filter out unneeded array prop
                            var arrayprop = {};
                            for (var p in datax)
                                if (datax.hasOwnProperty(p))
                                    if (p.startsWith('_'))
                                        arrayprop[p] = datax[p];
                            delete arrayprop[ids._mtid];
                            delete arrayprop[ids._ctime];
                            delete arrayprop[ids._segments];
                            delete arrayprop[ids._url];
                            delete arrayprop[ids._typeid];
                            delete arrayprop[ids._referer];
                            delete arrayprop[ids._totalsec];
                            delete arrayprop[ids._revenue];
                            delete arrayprop[ids._firstcampaign];
                            delete arrayprop[ids._lastcampaign];
                            delete arrayprop[ids._totalsec];
                            if (Object.keys(arrayprop).length !== 0)
                                updateArrayBasedUserInfo(collection, mtid, user, arrayprop);
                        });
                    });
                });
            });
        }
        // purpose: add new data to arrays in user
        // param:
        // + collection: collection to query user information
        // + mtid: mongodb.ObjectID mtid of user
        // + data: data to be append to user
        function updateArrayBasedUserInfo(collection, mtid, user, data) {
            me.converter.toObject(data, function (datax) {
                // append new element to the array or create one
                var arr = [];
                for (var p in datax)
                    if (datax.hasOwnProperty(p)) {
                        if (user[p] !== undefined) {
                            arr = user[p];
                        }
                        if (arr instanceof Array === false)
                            arr = [arr];
                        arr = arr.concat(datax[p]).sort();
                    }
                me.db.collection(collection).updateOne({ _id: mtid }, { "$set": user }, function (err, r) {
                    if (err)
                        throw err;
                });
            });
        }
    }
    // purpose: record an action
    // url: {appid}/r
    save(req, res, callback) {
        var me = this;
        var data = req.body;
        var appid = req.params.appid;
        me.saveRaw(appid, data, function (actionid) {
            res.writeHead(200, { 'Content-Type': 'text/plain' });
            res.end(actionid);
            callback(actionid);
        });
    }
    // purpose: fix an existing action
    // param:
    // + appid: id of the app
    // + actionid: ObjectID, id of action
    // + data: action data
    fixRaw(appid, actionids, lastactionidstr, data, callback) {
        let me = this;
        let actionid = new mongodb.ObjectID(actionids);
        if (data._mtid)
            data._mtid = new mongodb.ObjectID(data._mtid);
        var collection = me.prefix + "app" + appid;
        //make sure dont change typeid
        delete data._typeid;
        if (lastactionidstr !== null && lastactionidstr !== undefined && lastactionidstr !== '') {
            let lastactionid = new mongodb.ObjectID(lastactionidstr);
            me.db.collection(collection).find({ _id: lastactionid }).limit(1).toArray(function (err, r) {
                if (err)
                    throw err;
                if (r.length == 0)
                    throw "wrong last action id: " + lastactionidstr;
                let lastaction = r[0];
                data._link = lastactionid;
                me.converter.toIDs(['_utm_source', '_utm_campaign', '_utm_term', '_utm_content', '_utm_medium'], function (ids) {
                    if (data._utm_source == undefined)
                        data._utm_source = lastaction[ids._utm_source];
                    if (data._utm_campaign == undefined)
                        data._utm_campaign = lastaction[ids._utm_campaign];
                    if (data._utm_term == undefined)
                        data._utm_term = lastaction[ids._utm_term];
                    if (data._utm_content == undefined)
                        data._utm_content = lastaction[ids._utm_content];
                    if (data._utm_medium == undefined)
                        data._utm_medium = lastaction[ids._utm_meidum];
                    return store();
                });
            });
        }
        return store();
        function store() {
            me.updateChainCampaign(appid, actionids, data);
            me.valuemgr.cineObject(appid, "pageview", data);
            // set referal type
            data._reftype = me.referer.getRefType(data._url, data._ref);
            me.converter.toObject(data, function (datax) {
                me.db.collection(collection).updateOne({ _id: actionid }, { $set: datax }, function (err, r) {
                    if (err)
                        throw err;
                    callback();
                });
            });
        }
    }
    x(req, res, callback) {
        var me = this;
        var data = req.params;
        var collection = me.prefix + "app" + req.appid;
        var actionid = new mongodb.ObjectID(req.actionid);
        me.converter.toIDs(['_ctime', 'totalsec'], function (ids) {
            var projection = {};
            projection[ids._ctime] = 1;
            me.db.collection(collection).find({ _id: actionid }, projection).limit(1).toArray(function (err, r) {
                if (err)
                    throw err;
                if (r.length === 0)
                    throw "not found pageview to close, actionid: " + actionid;
                var newaction = {};
                newaction[ids.totalsec] = Math.round(new Date().getTime() / 1000) - (parseInt(data._deltat) ? parseInt(data._deltat) : 0) - r[0][ids._ctime];
                me.db.collection(collection).updateOne({ _id: actionid }, { $set: newaction }, function (err, r) {
                    if (err)
                        throw err;
                    res.writeHead(200);
                    res.end();
                    callback();
                });
            });
        });
    }
    // purpose: identify or update info of an visitor
    // param:
    //  data.mtid: string, //mtid của anonymous user
    //  data.user: {[userid], name, email, age, birth, gender, ...}
    // condition:
    // + case 1 : userid exist, iden-mtid is equal mtid
    //         client want to update info of an existing user
    //         just update the info based on mtid.
    // + case 2: user.userid exist, iden-mtid (mtid found by user.userid) is not equals to mtid
    //         mtid is now an ano-mtid (mtid for an anonymous visitor)
    //         add a mapping beetwen ano-mtid and ide-mtid, after this
    //         all action done by ano-mtid is converted to ide-mtid
    //         update info, delete ano-mtid user record if existed
    // + case 3: user.userid doesn't exist
    //         client want identify ano-mtid into registed user
    //         in this case, create new user with ide-mtid equal ano-mtid.
    //         just simply add userid field to old ano-mtid record, and
    //         udpate new info
    // + case 4: user.userid does not present
    //         client want to update info of an user
    //         do exactly as case 1.
    // output: return mtid of identified visitor
    identifyRaw(appid, data, callback) {
        let me = this;
        var collection = me.prefix + "app" + appid;
        var collectionmapping = me.prefix + me.mapping;
        var user = data.user;
        var userid = user.userid;
        // protect system properties, allow working only on user-based props
        // user-based prop is not started with an underscore '_' character
        var userex = {};
        for (var p in user)
            if (user.hasOwnProperty(p))
                if (p.startsWith('_') === false)
                    userex[p] = user[p];
        user = userex;
        var themtid = new mongodb.ObjectID(data.mtid);
        me.converter.toIDs(['_isUser', 'userid', '_mtid'], function (ids) {
            me.valuemgr.cineObject(appid, 'user', user);
            me.converter.toObject(user, function (userx) {
                // check for case 4
                if (userid === undefined)
                    return updateUserInfo(me.db, themtid, userx, callback);
                var query = {};
                query[ids._isUser] = true;
                query[ids.userid] = userid;
                me.db.collection(collection).findOneAndUpdate(query, { $set: userx }, { projection: { _id: 1 } }, function (err, r) {
                    if (err)
                        throw err;
                    // case 3 : user doesn't exist
                    if (r.value === null)
                        return updateUserInfo(me.db, themtid, userx, callback);
                    // user exist
                    var ide_mtid = r.value._id;
                    // check for case 1
                    if (themtid.toHexString() === ide_mtid.toHexString())
                        return updateUserInfo(me.db, themtid, userx, callback);
                    // case 2
                    // add to mapping collection
                    me.db.collection(collectionmapping).insertOne({
                        anomtid: themtid,
                        idemtid: ide_mtid,
                        ctime: new Date()
                    }, function (err) {
                        if (err)
                            throw err;
                    });
                    //convert all ano-mtid to ide-mtid
                    var query = {};
                    var update = {};
                    query[ids._mtid] = themtid;
                    update[ids._mtid] = ide_mtid;
                    me.db.collection(collection).updateMany(query, { $set: update }, function (err) {
                        if (err)
                            throw err;
                    });
                    // delete ano-mtid record IF EXISTED
                    me.db.collection(collection).deleteOne({ _id: themtid }, function () {
                    });
                    return updateUserInfo(me.db, ide_mtid, userx, callback);
                });
            });
        });
        // purpose: update info which mtid is mtid
        function updateUserInfo(db, mtid, userx, callback) {
            callback(mtid);
            if (Object.keys(userx).length !== 0)
                db.collection(collection).updateOne({ _id: mtid }, { $set: userx }, function (err, result) {
                    if (err)
                        throw err;
                });
        }
    }
    // purposer: phương thức này dùng để báo cho hệ thống biết một anonymous
    // user thực ra là một user đã tồn tại. Xem thêm ở http://pasteboard.co/1WAK4HYz.png
    // url: {appid}
    // param:
    //  mtid: string, //mtid của anonymous user
    //  user: {[userid], name, email, age, birth, gender, ...}
    identify(req, res, callback) {
        let me = this;
        me.identifyRaw(req.params.appid, req.body, function (mtid) {
            res.writeHead(200, { 'Content-Type': 'text/plain' });
            res.end(mtid);
            callback(mtid);
        });
    }
    // purpose: set up new record for anonymous user
    // param: appid: id of the app
    setupRaw(appid, callback) {
        var me = this;
        var collection = me.prefix + "app" + appid;
        var user = {
            _isUser: true,
            _segments: [],
            _ctime: Math.round(new Date().getTime() / 1000),
            _mtid: 2910
        };
        me.converter.toObject(user, function (user) {
            me.db.collection(collection).insertOne(user, function (err, results) {
                if (err)
                    throw err;
                var mtid = results.insertedId;
                callback(mtid);
                // update mtid equal id
                for (var p in user)
                    if (user.hasOwnProperty(p))
                        if (user[p] === 2910) {
                            user[p] = mtid;
                            break;
                        }
                        else
                            delete user[p];
                me.db.collection(collection).updateOne({ _id: mtid }, { $set: user }, function (err, r) {
                    if (err)
                        throw err;
                });
            });
        });
    }
    // purpose: set up new record for anonymous user
    // url /{appid}/?deltatime=20
    // param:
    // + appid: id of the app
    // output: new mtid
    setup(req, res, callback) {
        this.setupRaw(req.params.appid, function (mtid) {
            res.writeHead(200, { 'Content-Type': 'text/plain' });
            res.end(mtid);
        });
    }
    updateChainCampaign(appid, actionid, data) {
        var me = this;
        var collection = me.prefix + "app" + appid;
        me.converter.toIDs(['_utm_source', '_utm_campaign', '_utm_term', '_utm_content', '_utm_medium', '_link'], function (ids) {
            var match = {};
            match[ids._link] = new mongodb.ObjectID(actionid);
            me.db.collection(collection).find(match).toArray(function (err, res) {
                if (err)
                    throw err;
                if (res.length == 0)
                    return;
                for (let act of res) {
                    if (act[ids._utm_source] === undefined)
                        act[ids._utm_source] = data[ids._utm_source];
                    if (act[ids._utm_campaign] === undefined)
                        act[ids._utm_campaign] = data[ids.utm_campaign];
                    if (act[ids._utm_term] === undefined)
                        act[ids._utm_term] = data[ids.utm_term];
                    if (act[ids._utm_content] === undefined)
                        act[ids._utm_content] = data[ids.utm_content];
                    if (act[ids._utm_medium] === undefined)
                        act[ids._utm_medium] = data[ids.utm_medium];
                    me.updateChainCampaign(appid, act._id, act);
                }
            });
        });
    }
}
exports.ActionMgr = ActionMgr;
//# sourceMappingURL=actionmgr.js.map