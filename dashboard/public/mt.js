//THIS FILE IS CALLED AFTER MT.CODE IS CALLED
var mt = mt || {};
mt.base = '//meotrics.dev/api/$APPID$/';
mt.rq2 = [];
mt.rq = mt.rq || [];
mt.ir = false; // is ready flag

mt.ajax = function (url, data, callback) {
	function serialize(obj, prefix) {
		var encodeFunction = encodeURIComponent;
		var str = [];
		for (var p in obj) if (obj.hasOwnProperty(p)) {
			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push(typeof v == "object" ? serialize(v, k) : encodeFunction(k) + "=" + encodeFunction(v));
		}
		return str.join("&");
	}

	var script = document.createElement('script');
	// script.type = 'text/javascript'; comment this because we dont need to excute the script
	script.src = mt.base + url + (data ? '?' + serialize(data) : '');
	// script.style.display = 'none';
	if (callback) script.onreadystatechange = script.onload = callback;//for IE
	document.body.appendChild(script);
};

mt.identify = function (data, callback) {
	return mt.ir ? mt.ajax('identify', data, callback) : mt.rq2.push(['i', data]);
};

mt.clear = function (callback) {
	return mt.ir ? mt.ajax('clear', undefined, callback) : mt.rq2.push(['c']);
};

mt.track = function (event, data, time, callback) {
	if (!mt.ir) return mt.rq2.push(['t', event, data, new Date()]);
	var deltat = (new Date() - time) / 1000;
	data._referrer = document.referrer;
	data._typeid = event;
	data._deltat = deltat;
	data._screenres = navigator.availWidth + "x" + navigator.availHeight;

	mt.ajax('track', data, callback);
};

// clean request queue
mt.crq = function () {
	// clean queue number 2 when out of element in queue number 1
	if (mt.i + 1 >= mt.rq.length) return mt.crq2();

	var rq = mt.rq[mt.i];
	mt.i++;
	var action = rq.action;
	if (action == 't') mt.track(rq[1], rq[2], rq[3], mt.crq);
	if (action == 'i') mt.identify(rq[1], mt.crq);
	if (action == 'c') mt.clear(mt.crq);
};

// clean request queue step 2
mt.crq2 = function () {
	if (mt.j + 1 >= mt.rq2.length) // clean the state when done
		return mt.ir = true;

	var rq = mt.rq2[mt.j];
	mt.j++;
	var action = rq.action;
	if (action == 't') mt.track(rq[1], rq[2], rq[3], mt.crq2);
	if (action == 'i') mt.identify(rq[1], mt.crq2);
	if (action == 'c') mt.clear(mt.crq2);
};

mt.i = mt.j = 0;
mt.crq();// excute delayed request in queue