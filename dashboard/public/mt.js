//THIS FILE IS CALLED AFTER MT.CODE IS CALLED
if (mt == undefined) var mt = {};
mt.__requestQueue = mt.__requestQueue || [];
mt.appid = mt.appid || -8;

mt.__getCookie = function (name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
	return undefined;
};

mt.__serialize = function (obj, prefix) {
	var str = [];
	for (var p in obj) {
		if (obj.hasOwnProperty(p)) {
			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push(typeof v == "object" ?
					serialize(v, k) :
			encodeURIComponent(k) + "=" + encodeURIComponent(v));
		}
	}
	return str.join("&");
};

mt.__ajax = function (url, data, callback) {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = mt.host + "/" + url + "?" + mt.__serialize(data);
	script.style.display = 'none';
	script.onreadystatechange = callback;//for IE
	script.onload = callback; //for other browsers
	document.body.appendChild(script)
};

mt.identify = function (data) {
	if (mt.__isready != true) {
		mt.__requestQueue.push({action: 'identify', data: data});
		return;
	}
	mt.__ajax('identify/' + mt.appid, data)
};

mt.track = function (event, data, time) {
	if (mt.__isready != true) {
		mt.__requestQueue.push({action: 'track', event: event, data: data, time: new Date()});
		return;
	}

	var deltat = (new Date() - time) / 1000;
	data._referrer = document.referrer;
	data._type = event;
	data._deltat = deltat;
	data._screenres = navigator.availWidth + "x" + navigator.availHeight;
	mt.__ajax('track/' + mt.appid, data);
};

//ENTRY POINT

mt.mtid = mt.__getCookie("mtid");
mt.__isready = false;
if (mt.mtid === undefined) {
	mt.__ajax('setup/' + mt.appid, undefined, function () {
		mt.mtid = mt.__getCookie("mtid");
		mt.__isready = true;
		mt.__finishqueue();
	}); //what about if we failed
}
else mt.__finishqueue();

mt.__finishqueue = function () {
	//finish the queue
	for (var rq in mt.__requestQueue) if (mt.__requestQueue.hasOwnProperty(rq)) {
		if (rq.action == 'track')
			mt.track(rq.event, rq.data);
		else if (rq.action == 'identify')
			mt.identify(rq.data);
	}
	delete mt.__requestQueue;
};



