//THIS FILE IS CALLED AFTER MT.CODE IS CALLED
(function () {
	var actionid = "$ACTIONID$";
	window.addEventListener("beforeunload", function (e) {
	ajax('x/' + actionid);
	});

	var encodeFunction = encodeURIComponent, i = 0, j = 0, isready, request_queue2 = [], doc = document;

	function ajax(url, data, callback) {
		var script = doc.createElement('script');
		// script.type = 'text/javascript'; comment this because we dont need to excute the script
		script.src = '//meotrics.dev/api/$APPID$/' + url + (data ? '?' + serialize(data) : '');
		// script.style.display = 'none';
		script.onreadystatechange = script.onload = callback;//for IE
		doc.body.appendChild(script);
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
		return isready ? ajax('info', data, callback || callback3) : request_queue2.push(['info', data]);
	};

	mt.clear = function (callback, callback2, callback3, callback4) {
		return isready ? ajax('clear', callback3 /*alway undefined, use callback3 for better minify*/, callback || callback4) : request_queue2.push(['clear']);
	};

	mt.track = function (event, data, time, callback) {
		data._deltat = (new Date() - time) / 1000;
		data._typeid = event;
		return isready ? ajax('track', addVisitorPlatform(data), callback) : request_queue2.push(['track', event, data, new Date()]);
	};

	function addVisitorPlatform(data) {
		data._ref = doc.referrer;
		data._scr = screen.width + "x" + screen.height;
		data._url = location.href;
		return data;
	}

	// clean request queue
	function cleanRequest() {
		// clean queue number 2 when out of element in queue number 1
		if (i + 1 >= mt.rq.length) return cleanRequest2();
		var rq = mt.rq[i++];
		mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest);
	}

	// clean request queue step 2
	function cleanRequest2() {
		if (j + 1 >= request_queue2.length) // clean the state when done
			return isready = 1;
		alert('how');
		var rq = request_queue2[j++];
		mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest2);
	}

	ajax('fix/' + actionid, addVisitorPlatform({}));//update the pageview first
	cleanRequest();// excute delayed request in queue
})();