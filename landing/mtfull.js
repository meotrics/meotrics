(function () {
	var host = "meotrics.com";
	if(location.hostname == "client.meotrics.dev") host = "meotrics.dev";
	var encodeFunction = encodeURIComponent, i = 0, j = 0, isready, request_queue2 = [], doc = document;
	
	window.addEventListener("beforeunload", function (e) {
		ajax('x/' + mt.actionid);
	});

	function ajax(url, data, callback) {
		var script = doc.createElement('script');
		// script.type = 'text/javascript'; comment this because we dont need to excute the script
		script.src = '//api.' + host + "/" + mt.appid + '/' + url + (data ? '?' + serialize(data) : '');
		script.style.display = 'none';
		script.onreadystatechange = script.onload = callback;//for IE
		doc.body === undefined ? doc.head.appendChild(script) : doc.body.appendChild(script);
	}

	function serialize(obj, prefix) {
		var str = [];
		for (var p in obj)/* if (obj.hasOwnProperty(p)) */{
			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push(v instanceof Object ? serialize(v, k) : encodeFunction(k) + "=" + encodeFunction(v));
		}
		return str.join("&");
	}

	mt.info = function (data, callback, callback2, callback3) {
		if(typeof data == 'string') data = {userid: data};
		if(isready)
			ajax('info', data, callback || callback3)
		else{
			request_queue2.push(['info', data]);
			(callback || callback3)();
		}
	};

	mt.clear = function (callback, callback2, callback3, callback4) {
		if(isready)
			 ajax('clear', callback3 /*alway undefined, use callback3 for better minify*/, callback || callback4);
		else{
			request_queue2.push(['clear']);
			(callback || callback4)();
		}
	};

	mt.track = function (event, data, time, callback) {
		data._deltat = (new Date() - time) / 1000;
		data._typeid = event;
		if(isready) ajax('track', addVisitorPlatform(data), callback) 
		else
		{
			request_queue2.push(['track', event, data, new Date()]);
			callback();
		}
	};

	function addVisitorPlatform(data) {
		data._ref = doc.referrer;
		data._scr = screen.width + "x" + screen.height;
		data._url = location.href;
		data._link = mt.actionid;
		return data;
	}

	// clean request queue
	function cleanRequest() {
		// clean queue number 2 when out of element in queue number 1
		if (i >= mt.rq.length) { 
			isready = 1;
			return cleanRequest2();
		}
		var rq = mt.rq[i++];
		mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest);
	}

	// clean request queue step 2
	function cleanRequest2() {
		if (j < request_queue2.length) // clean the state when done
		{
			var rq = request_queue2[j++];
			mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest2);
		}
	}

	mt.excute = function(event){
		var data = JSON.parse(event.data);
		mt.actionid = data.actionid;
		if(data.lastactionid !== undefined)
		{
			var o1 = {
				actionid : data.actionid, 
				lastactionid: data.lastactionid
			}
			addVisitorPlatform(o1);
			ajax('fix',o1);
		}
		else
		{
			var o2 = {actionid : data.actionid};
			addVisitorPlatform(o2);
			ajax('fix', o2);
		}
		cleanRequest();// excute delayed request in queue
	}

	var ifm = doc.createElement('iframe');
	ifm.style.display="none";
	ifm.src = "//" + host + "/iframe.html?x=" + mt.appid + '-' + mt.actionid;
	if(doc.body === undefined) doc.head.appendChild(ifm);
	else doc.body.appendChild(ifm);

	mt.onready();
})();