"use strict";
exports.HttpApi = function (codepath, actionmgr, fs, ua, MD, valuemgr) {
	var code;
	function loadCode(appid, actionid, callback) {
		// cache mtcode in code for minimize disk usage, lazy load
		if (code === undefined)
			fs.readFile(codepath,'ascii', function (err, data) {
				code = data;
				replaceParam();
			});
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
		var useragent = request.headers['user-agent'];
		var r = ua.parse(useragent);
		var url = request.params._url || "";

		var md = new MD(useragent);
		var devicetype;
		if (md.tablet() !== null)
			devicetype = 'tablet';
		else if (md.phone() !== null)
			devicetype = 'phone';
		else
			devicetype = 'desktop';

		if ( url === "" || url.startsWith(request.headers.referer) === false) url = request.headers.referer;
		var res = {
			_url: url,
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
			_devicetype: devicetype
		};
		for (var i in request.params) if (request.params.hasOwnProperty(i))
			if (i.startsWith('_') === false)
				res[i] = request.params[i];

		// extract campaign
		var query = url.parse(url, true).query;
		res._utm_source = query.utm_source;
		res._utm_campaign = query.utm_campaign;
		res._utm_term = query.utm_term;
		res._utm_content = query.utm_content;
		res._utm_medium = query.utm_medium;

		return res;
	}

	function getMtid(req, appid, res, callback) {
		var mtid = getCookie(req, "mtid");
		if(mtid === undefined)
		{
			return actionmgr.setupRaw(appid, function (mtid) {
				setCookie(res, "mtid", mtid, 'api/' + appid);
				callback(mtid);
			});
		}

		// check if mtid is valid
		actionmgr.ismtidValid(appid, mtid, function(ret){
			if(ret) callback(mtid);
			else {
				eraseCookie(res, "mtid", 'api/' + appid);
				actionmgr.setupRaw(appid, function (mtid) {
					setCookie(res, "mtid", mtid, 'api/' + appid);
					callback(mtid);
				});
			}
		});
	}

	function eraseCookie(res, name, path) {
		res.setHeader('Set-Cookie', name + "=x; expires=Wed, 21 Aug 1995 11:11:11 GMT; path=/" + path);
	}

	this.clear = function (req, res) {
		// delete the cookie
		eraseCookie(res, 'mtid', 'api/' + req.appid);
		res.end();
	};

	this.track = function (req, res) {
		var appid = req.appid;
		var data = trackBasic(req);
		getMtid(req, appid, res, function (mtid) {
			data._mtid = mtid;
			actionmgr.saveRaw(appid, data, function (actionid) {
				res.setHeader('Content-Type', 'text/plain');
				res.end("//" + actionid);
			});
		});
	};

	this.info = function (req, res) {
		var appid = req.appid;
		getMtid(req, appid, res, function (mtid) {
			var data = {};
			for (var i in req.params) if (req.params.hasOwnProperty(i)) {
				if (i.startsWith('_') === false) data[i] = req.params[i];
			}

			actionmgr.identifyRaw(appid, {mtid: mtid, user: data}, function (mtid) {
				//set new mtid if need
				setCookie(res, "mtid", mtid, 'api/' + appid);
				res.setHeader('Content-Type', 'text/plain');

				res.end("//"+ mtid);
			});
		});
	};

	this.x = function(req,res){
		actionmgr.x(req, res, function(){});
	};

	function getCookie(req, name) {
		var list = {}, rc = req.headers.cookie;
		rc && rc.split(';').forEach(function (cookie) {
			var parts = cookie.split('=');
			list[parts.shift().trim()] = decodeURIComponent(parts.join('='));
		});
		return list[name];
	}

	function setCookie(res, name, value, path) {
		var tenyearlater = new Date().getYear() + 10 + 1900;
		res.setHeader('Set-Cookie', name + '=' + encodeURIComponent(value) + "; expires=Wed, 21 Aug " + tenyearlater + " 11:11:11 GMT; path=/" + path);
	}

	this.fix = function (req, res) {
		var appid = req.appid;
		var actionid = req.actionid;
		var data = trackBasic(req);
		getMtid(req, appid, res, function (mtid) {
			res.end();
			data._mtid = mtid;
			actionmgr.fixRaw(appid, actionid, data, function () {
			});
		});
	};
	
	this.suggest = function(req, res){
		valuemgr.suggest(req.appid +"", req.typeid+"", req.field+"", req.qr+"", function(results){
			res.setHeader('Content-Type', 'application/json');
			res.end(JSON.stringify(results));
		});
	};

	this.code = function (req, res) {
		var appid = req.appid;
		// record an new pageview
		var data = trackBasic(req);
		getMtid(req, appid, res, function (mtid) {
			data._mtid = mtid;
			data._typeid = 'pageview';
			
			actionmgr.saveRaw(appid, data, function (actionid) {
				// return code
				loadCode(appid, actionid, function (code) {
					res.setHeader('Content-Type', 'application/javascript');
					res.end(code);
				});
			});
		});
	};
};