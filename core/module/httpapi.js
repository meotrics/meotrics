"use strict";

var trycatch = require('trycatch');
var qs = require('querystring');
var url = require('url');
var fs = require('fs');
var ua = require('ua-parser');
var MD = require('mobile-detect');
var ActionMgr = require('./actionmgr');
var ObjectID = require('bson-objectid');
exports.HttpApi = function (db, converter, prefix, codepath, ref, valuemgr) {
	var code;
	var actionmgr = new ActionMgr.ActionMgr(db, converter, prefix, "mapping", valuemgr, ref);
	var me = this;
	this.onchange = function () { };
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
		console.log("=====trace basic");
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

		console.log(devicetype);
		if (uri === "" || uri.startsWith(request.headers.referer) === false) uri = request.headers.referer || '';
		var res = {
			_url: uri,
			_callback : request.params._callback,
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
			_mtid:  request.params._mtid
		};

		for (var i in request.params)
			// if (request.params.hasOwnProperty(i))
			if (i.startsWith('_') === false)
				res[i] = isNaN(request.params[i]) ? request.params[i] : parseFloat(request.params[i]);

		// extract campaign
		var query = url.parse(uri, true).query;
		res._utm_source = query.utm_source;
		res._utm_campaign = query.utm_campaign;
		res._utm_term = query.utm_term;
		res._utm_content = query.utm_content;
		res._utm_medium = query.utm_medium;
		return res;
	}


	function getMtid(req, appid, res, callback) {
		console.log("=====get mtid");
		var mtid = getCookie(req, "mtid");
		console.log("mtid: "+mtid);
		if (mtid === undefined || mtid.length != 24) {
			mtid = req.params._mtid;
			if (mtid === undefined) {
				return actionmgr.setupRaw(appid, function (mtid) {
					callback(mtid);
				});
			}
		}
		// check if mtid is valid
		actionmgr.ismtidValid(appid, mtid, function (ret) {
			if (ret) callback(mtid);
			else {
				eraseCookie(res, "mtid", appid);
				actionmgr.setupRaw(appid, function (mtid) {
					callback(mtid);
				});
			}
		});
	}


	function track(req, res) {
		console.log("=====track");
		var appid = req.appid;
		var data = trackBasic(req);
		var callback = (data._callback == 'true' || data._callback == true);
		delete data._callback;

		handlerMtid(data._mtid, appid, res, function (mtid) {
			data._mtid = mtid;
			actionmgr.saveRaw(appid, data, function (actionid) {
				me.onchange(appid, "type." + data._typeid);
				
				if (callback === true)
				{
					sendMtid(mtid,res);
				}
				else {
					sendMtid(mtid,res);
				}
	
			});
		});
	}

	// identify an user
	// if mtid not exists in the parameter ->create one
	function info(req, res) {
		console.log("=====info");
		var appid = req.appid;
		var data = trackBasic(req);
		handlerMtid(data._mtid, appid, res, function (mtid) {
			var data = {};
			console.log(req.params);
			for (var i in req.params)
					if (i.startsWith('_') === false) data[i] = isNaN(req.params[i]) ? req.params[i] : parseFloat(req.params[i]);

			actionmgr.identifyRaw(appid, { mtid: mtid, user: data }, function (mtid) {
				//set new mtid if need
			});
		});
	}

	function x(req, res) {
		actionmgr.x(req, res, function () {
		});
	}



	function suggest(req, res) {
		console.log("=====suggest");
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

	function pageviewtwo(req,res){
		var appid = req.appid;
		var data = trackBasic(req);
		handlerMtid(data._mtid,appid,res,function(mtid){
			me.onchange(appid, 'type.pageview');
			data._typeid = 'pageview';
			data._mtid = mtid;
			console.log("mtid: "+mtid);
			actionmgr.saveRaw(appid, data);
		});
	}

	function handlerMtid(mtid,appid,res,callback){
		if(mtid == undefined || mtid == 'undefined'){
			actionmgr.setupRaw(appid, function (_mtid) {
				console.log("new user 1");
				mtid = _mtid;
				sendMtid(mtid,res);
				callback(mtid);
			});
		}else{
			actionmgr.ismtidValid(appid, mtid, function (ret) {
				if (!ret){
					console.log("new user 2");
					actionmgr.setupRaw(appid, function (_mtid) {
						mtid = _mtid;
						sendMtid(mtid,res);
						callback(mtid);
					});
				}else{
					sendMtid(mtid,res);
					callback(mtid);
				}
			});
		}
	}

	function sendMtid(mtid,res){
		var id = {};
		id._mtid = mtid;
		var json = JSON.stringify(id);
		res.setHeader('Content-Type', 'application/json');
		res.setHeader('Access-Control-Allow-Origin', '*');
		res.setHeader('Access-Control-Allow-Methods', 'GET');
		res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
		res.setHeader('Access-Control-Allow-Credentials', true);
		res.setHeader('Content-Type', 'application/json');
		console.log("sended");
		res.end(json);
	}

	function pageview(req, res) {
		console.log("=====pageview");
		var appid = req.appid;
		// record an new pageview
		var data = trackBasic(req);
		getMtid(req, appid, res, function (mtid) {
			data._mtid = mtid;
			data._typeid = 'pageview';
			var newId = ObjectID();
			data._id = newId;
			loadCode(appid, newId.toString(), function (code) {
				res.setHeader('Content-Type', 'text/css');
				res.end(code);
			});
			actionmgr.saveRaw(appid, data, function (actionid) {
				me.onchange(appid, 'type.pageview');
			});
		});
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

			function handle(req, res, path) {
				var parts = path.split('/');
				res.statusCode = 200;
				req['appid'] = parts[1];
				var action = parts[2];
				if (action === 'track') track(req, res);
				else if(action == 'check') checkMtid(req,res);
				else if (action === '' || action === undefined) pageview(req, res);
				else if (action === 'pageviewtwo') pageviewtwo(req, res);
				else if (action === 'clear') clear(req, res);
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
				}
				else if (action === 'fix') {
					var query = url.parse(path, true).query;
					req['actionid'] = req.params.actionid;
					req['lastactionid'] = req.params.lastactionid;
						if(req.params.actionid==null) console.log('errfix: ', path);
					fix(req, res);
				} else {
					res.statusCode = 404;
					res.end('action "' + action + '" not found, action must be one of [x, clear, info, fix, track]');
				}
			}
		}, function (err) {
			res.statusCode = 500;
			res.end();
			console.log(err, err.stack);
		});
	};
};
