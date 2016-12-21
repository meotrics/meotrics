"use strict";

var trycatch = require('trycatch');
var qs = require('querystring');
var url = require('url');
var fs = require('fs');
var ua = require('ua-parser');
var MD = require('mobile-detect');
var ActionMgr = require('./actionmgr');
var ObjectID = require('bson-objectid');
var TrackOffline = require('./trackOffline');
exports.HttpApi = function (db, converter, prefix, codepath, ref, valuemgr) {
    var code;
    var actionmgr = new ActionMgr.ActionMgr(db, converter, prefix, "mapping", valuemgr, ref);
    var me = this;
    this.onchange = function () {
    };
    function loadCode(appid, actionid, callback) {
        // cache mtcode in code for minimize disk usage, lazy load
        if (code === undefined) {
            fs.readFile(codepath, 'ascii', function (err, data) {
                code = data;
                replaceParam();
            });
        }
        else replaceParam();

        function replaceParam() {
            callback(code.replace('$APPID$', appid).replace('$ACTIONID$', actionid));
        }
    }

    // purpose get real ip address
    function getRemoteAddress(req) {
        return req.headers['x-forwarded-for'] || req.connection.remoteAddress || req.socket.remoteAddress || req.connection.socket.remoteAddress;
    }

    // purpose: extract basic info from user agent and request parameter
    function trackBasic(request) {
        // console.log("=====trace basic");
        var useragent = request.headers['user-agent'];
        var r = ua.parse(useragent);
        var uri = request.params._url || '';
        var md = new MD(useragent);
        var devicetype = 'desktop';
        if (md.tablet() !== null)
            devicetype = 'tablet';
        else if (md.phone() !== null || md.mobile() != null)
            devicetype = 'phone';
        else
            devicetype = 'desktop';
        if (uri === "" || uri.startsWith(request.headers.referer) === false) uri = request.headers.referer || '';
        var res = {
            _url: uri,
            _callback: request.params._callback,
            _ref: request.params._ref,
            _typeid: request.params._typeid,
            _ip: getRemoteAddress(request),
            _deltat: request.params._deltat,
            _os: r.os.family,
            _browser: r.ua.family,
            _browserver: r.ua.major + '.' + r.ua.minor,
            _osver: r.os.major + '.' + r.os.minor,
            _deviceid: r.device.family,
            _scr: request.params._scr,
            _lang: request.headers["accept-language"],
            _devicetype: devicetype,
            _mtid: request.params._mtid,
            _ga : request.params._ga
        };

        for (var i in request.params)
            if (i.startsWith('_') === false) {
                res[i] = isNaN(request.params[i]) ? request.params[i] : parseFloat(request.params[i]);
            }

        // extract campaign
        var query = url.parse(uri, true).query;
        res._utm_source = query.utm_source;
        res._utm_campaign = query.utm_campaign;
        res._utm_term = query.utm_term;
        res._utm_content = query.utm_content;
        res._utm_medium = query.utm_medium;
        delete res.k;
        return res;
    }

    function getKey(request) {
        var useragent = request.headers['user-agent'];
        var r = ua.parse(useragent);
        var uri = request.params._url || '';
        var res = {};
        for (var i in request.params)
            if (i.startsWith('_') === false) {
                res[i] = isNaN(request.params[i]) ? request.params[i] : parseFloat(request.params[i]);
            }
        return res;
    }


    function track(req, res) {
        // console.log("=====track");
        var appid = req.appid;
        var data = trackBasic(req);
        var callback = (data._callback == 'true' || data._callback == true);
        delete data._callback;

        handlerMtid(data._mtid, appid, res, function (mtid) {
            data._mtid = mtid;
            actionmgr.saveRaw(appid, data, function (actionid) {
                me.onchange(appid, "type." + data._typeid);
            });
        });
    }

    // identify an user
    // if mtid not exists in the parameter ->create one
    // function info(req, res) {
    //     // console.log("=====info");
    //     var appid = req.appid;
    //     var data = trackBasic(req);
    //     handlerMtid(data._mtid, appid, res, function (mtid) {
    //         var value = {};
    //         // console.log(req.params);
    //         for (var i in req.params)
    //             if (i.startsWith('_') === false) value[i] = isNaN(req.params[i]) ? req.params[i] : parseFloat(req.params[i]);
    //
    //         actionmgr.identifyRaw(appid, {mtid: mtid, user: value}, function (mtid) {
    //             //set new mtid if need
    //             data._typeid = "login";
    //             actionmgr.saveRaw(appid, data, function (actionid) {
    //             });
    //         });
    //     });
    // }

    function info(req, res) {
        // console.log("=====info");
        var appid = req.appid;
        var data = trackBasic(req);
        // find user have mtid
        var queryuserhavemtid = {_id: new ObjectID(data._mtid)};
        var queryuserhaveemail = {email : data.email};
        // console.log(data.email);
        actionmgr.findUserByKey(appid,queryuserhavemtid,function(userold){
            if(userold != undefined){
                if(userold.email == undefined){
                    actionmgr.findUserByKey(appid, queryuserhaveemail,function(userhaveemail){
                        if(userhaveemail == "undefined"){
                            // console.log(1);
                            login();
                            // sendMtid(mtid, res);
                            // updateUser(data._mtid);
                        }else{
                            // console.log(2);
                            var idUser = userhaveemail._mtid;
                             sendMtid(idUser, res);
                            actionmgr.moveUser(userold, userhaveemail,appid);
                        }
                    })
                }else{
                      if(userold.email == data.email){
                          // console.log(3);
                          sendMtid(data._mtid, res);
                          updateUser(data._mtid);
                      }else{
                          actionmgr.findUserByKey(appid, queryuserhaveemail,function(userhaveemail){
                              // console.log("userhaveemail: ");
                              // console.log(userhaveemail);
                              if(userhaveemail == "undefined"){
                                  // console.log(4);
                                  data._mtid = undefined;
                                  handlerMtid(data._mtid, appid, res, function (mtid) {
                                      var value = {};
                                      data._mtid = mtid;
                                      // console.log(req.params);
                                      for (var i in req.params)
                                          if (i.startsWith('_') === false) value[i] = isNaN(req.params[i]) ? req.params[i] : parseFloat(req.params[i]);

                                      actionmgr.identifyRaw(appid, {mtid: mtid, user: value}, function (mtid) {
                                          //set new mtid if need
                                          data._typeid = "login";
                                          actionmgr.saveRaw(appid, data, function (actionid) {
                                          });
                                      });
                                  });
                              }else{
                                  // console.log(5);
                                  // exists user
                                  data._mtid = userhaveemail._mtid;
                                  sendMtid(userhaveemail._mtid,res);
                                  updateUser(userhaveemail._mtid);

                              }
                          })
                      }
                }
            }
        })

        function updateUser(mtid){
                var value = {};
                // console.log(req.params);
                for (var i in req.params)
                    if (i.startsWith('_') === false) value[i] = isNaN(req.params[i]) ? req.params[i] : parseFloat(req.params[i]);

                actionmgr.identifyRaw(appid, {mtid: mtid, user: value}, function (mtid) {
                    //set new mtid if need
                    data._typeid = "login";
                    actionmgr.saveRaw(appid, data, function (actionid) {
                    });
                });
        };

        function login(){
            handlerMtid(data._mtid, appid, res, function (mtid) {
                var value = {};
                // console.log(req.params);
                for (var i in req.params)
                    if (i.startsWith('_') === false) value[i] = isNaN(req.params[i]) ? req.params[i] : parseFloat(req.params[i]);

                actionmgr.identifyRaw(appid, {mtid: mtid, user: value}, function (mtid) {
                    //set new mtid if need
                    data._typeid = "login";
                    actionmgr.saveRaw(appid, data, function (actionid) {
                    });
                });
            });
        }
    }



    function registerevent(req, res) {
        // console.log("=====info");
        var me = this;
        var appid = req.appid;
        var data = trackBasic(req);
        if(data.email != undefined){
            var query = {};
            query['email'] = data.email;
            actionmgr.findUserByKey(appid, query, function (user) {
                data._mtid = user._mtid.toString();
                registerUser();
            });
        }else{
            delete data._mtid;
            registerUser();
        }
        function registerUser(){
            handlerMtid(data._mtid, appid, res, function (mtid) {
                var value = {};
                for (var i in req.params)
                    if (i.startsWith('_') === false) value[i] = isNaN(req.params[i]) ? req.params[i] : parseFloat(req.params[i]);

                actionmgr.identifyRaw(appid, {mtid: mtid, user: value}, function (mtid) {
                    //set new mtid if need
                    data._typeid = "login";
                    data._mtid = mtid;
                    actionmgr.saveRaw(appid, data, function (actionid) {
                    });
                });
            });
        }
    }

    function x(req, res) {
        actionmgr.x(req, res, function () {
        });
    }


    function suggest(req, res) {
        valuemgr.suggest(req.appid + "", req.typeid + "", req.field + "", req.qr + "", function (results) {
            res.setHeader('Content-Type', 'application/json');
            res.setHeader('Access-Control-Allow-Origin', '*');
            //res.setHeader('Access-Control-Allow-Origin', 'https://app.meotrics.com env=HTTPS');

            res.setHeader('Access-Control-Allow-Methods', 'GET');
            res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
            res.setHeader('Access-Control-Allow-Credentials', true);
            res.end(JSON.stringify(results));
        });
    }


    function pageviewtwo(req, res) {
        var appid = req.appid;
        var data = trackBasic(req);
        handlerMtid(data._mtid, appid, res, function (mtid) {
            me.onchange(appid, 'type.pageview');
            data._typeid = 'pageview';
            data._mtid = mtid;
            actionmgr.saveRaw(appid, data, function (callback) {
            });
        });
    }

    function handlerMtid(mtid, appid, res, callback) {
        if (mtid == undefined || mtid == 'undefined' || mtid.length != 24) {
            mtid = ObjectID();
            sendMtid(mtid, res);
            actionmgr.setupRaw(appid, mtid, function (id) {
                callback(id);
            });
        } else {
            sendMtid(mtid, res);
            actionmgr.ismtidValid(appid, mtid, function (ret) {
                if (!ret) {
                    actionmgr.setupRaw(appid, mtid, function (id) {
                        callback(mtid);
                    });
                } else {
                    callback(mtid);
                }
            });
        }
    }

    function sendMtid(mtid, res) {
        var id = {};
        id._mtid = mtid;
        var json = JSON.stringify(id);
        res.setHeader('Content-Type', 'application/json');
        res.setHeader('Access-Control-Allow-Origin', '*');
        res.setHeader('Access-Control-Allow-Methods', 'GET');
        res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
        res.setHeader('Access-Control-Allow-Credentials', true);
        res.setHeader('Content-Type', 'application/json');
        res.write(json);
        res.end();
    }


    this.route = function (req, res) {
        var me = this;
        trycatch(function () {
            var url_parts = url.parse(req.url, true);
            if (req.method === 'POST') {
                var body = '';
                req.on('data', function (data) {
                    body += data;
                });
                req.on('end', function () {
                    req['params'] = qs.parse(body);
                    handle(req, res, url_parts.pathname);
                });
            }
            else if (req.method === 'GET') {
                req['params'] = url_parts.query;
                handle(req, res, url_parts.pathname);
            }

            function checkOffline(req, res) {
                var appid = req.appid
                res.setHeader('Content-Type', 'application/json');
                res.setHeader('Access-Control-Allow-Origin', '*');
                res.setHeader('Access-Control-Allow-Methods', 'GET');
                res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
                res.setHeader('Access-Control-Allow-Credentials', true);
                res.setHeader('Content-Type', 'application/json');
                var trackOffline = new TrackOffline.trackOffline(appid, actionmgr);
                trackOffline.purchaseOffline(req);
                var json = {};
                json.status = "success";
                res.statusCode = 200;
                res.end(JSON.stringify(json));
            }

            function handle(req, res, path) {
                var data = getKey(req);
                if(data.k =="4ec0f81c5a3ddb192ab9ee9641758c52" && data._browser != "Googlebot"){
                    var parts = path.split('/');
                    res.statusCode = 200;
                    req['appid'] = parts[1];
                    var action = parts[2];
                    if (action === 'track') track(req, res);
                    // else if (action === '' || action === undefined) pageviewtwo(req, res);
                    else if (action === 'pageviewtwo') pageviewtwo(req, res);
                    else if (action === 'registerevent') registerevent(req, res);
                    else if (action === 'info') info(req, res);
                    else if (action === 'x') {
                        req['actionid'] = parts[3];

                        x(req, res);
                    }
                    else if (action === 'suggest') {
                        req['typeid'] = parts[3];
                        req['field'] = parts[4];
                        req['qr'] = parts[5];
                        suggest(req, res);
                    } else if (action == 'offPurchase') checkOffline(req, res);
                    else {
                        res.statusCode = 404;
                        res.end('action "' + action + '" not found, action must be one of [x, clear, info, fix, track]');
                    }
                }else{
                    res.statusCode = 500;
                    res.end();
                }
            }
        }, function (err) {
            res.statusCode = 500;
            res.end();
            console.log(err, err.stack);
        });
    };
};
