//THIS FILE IS CALLED AFTER MT.CODE IS CALLED
var mt = mt || {};
mt.host = '//meotrics.dev';
mt.appid = "APPID";
mt.rq2 = [];
mt.rq = mt.rq || [];
mt.isready = false;

mt.ajax = function (url, data, callback) {
	function serialize(obj, prefix) {
		if (obj == undefined) return "";
		var str = [];
		for (var p in obj) if (obj.hasOwnProperty(p)) {
			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push(typeof v == "object" ? serialize(v, k) : encodeURIComponent(k) + "=" + encodeURIComponent(v));
		}
		return str.join("&");
	}

	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = mt.host  + url + "?" + serialize(data);
	script.style.display = 'none';
	if (callback) {
		script.onreadystatechange = callback;//for IE
		script.onload = callback; //for other browsers
	}
	document.body.appendChild(script);
};

mt.identify = function (data, callback) {
	if (!mt.isready) return mt.rq2.push({action: 'identify', data: data});
	mt.ajax('api/' + mt.appid + '/identify', data, callback)
};

mt.clear = function (callback) {
	if (!mt.isready) return mt.rq2.push({action: 'clear'});
	mt.ajax('api/' + mt.appid + '/clear', undefined, callback);
};

mt.track = function (event, data, time, callback) {
	if (!mt.isready) return mt.rq2.push({action: 'track', event: event, data: data, time: new Date()});
	var deltat = (new Date() - time) / 1000;
	data._referrer = document.referrer;
	data._type = event;
	data._deltat = deltat;
	data._screenres = navigator.availWidth + "x" + navigator.availHeight;
	mt.ajax('api/' + mt.appid + '/track', data, callback);
};

mt.i = 0;
extract();// excute delayed request in queue

function extract() {
	// clean queue number 2 when out of element in queue number 1
	if ((mt.i + 1) >= mt.rq.length) {
		mt.i = 0;
		cleanRequestQueue2();
	}

	var rq = mt.rq[mt.i];
	mt.i++;
	if (rq.action == 'track') mt.track(rq.event, rq.data, extract);
	else if (rq.action == 'identify') mt.identify(rq.data, extract);
	else if (rq.action == 'clear') mt.clear(extract);
}

function cleanRequestQueue2() {
	if ((mt.i + 1) >= mt.rq2.length) { // clean the state when done
		delete mt.rq;
		delete mt.rq2;
		mt.isready = true;
		return;
	}
	var rq = mt.rq2[mt.i];
	mt.i++;
	if (rq.action == 'track') mt.track(rq.event, rq.data, cleanRequestQueue2);
	else if (rq.action == 'identify') mt.identify(rq.data, cleanRequestQueue2);
	else if (rq.action == 'clear') mt.clear(cleanRequestQueue2);
}