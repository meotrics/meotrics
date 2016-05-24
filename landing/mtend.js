(function () {
	var encodeFunction = encodeURIComponent, i = 0, j = 0, isready, request_queue2 = [], doc = document;
	
	window.addEventListener("beforeunload", function (e) {
		//ajax('x/' + mt.actionid);
	});

	function ajax(url, data, callback) {
		var script = doc.createElement('script');
		// script.type = 'text/javascript'; comment this because we dont need to excute the script
		script.src = '//api.meotrics.com/' + mt.appid + '/' + url + (data ? '?' + serialize(data) : '');
		script.style.display = 'none';
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
		data._link = mt.actionid;
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
		var rq = request_queue2[j++];
		mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest2);
	}

	mt.excute = function(event){
		var data = JSON.parse(event.data);
		mt.actionid = data.actionid;
		if(data.lastactionid !== undefined)
			ajax('fix', {
				actionid : data.actionid, 
				lastactionid: data.lastactionid, 
				data: addVisitorPlatform({})
			});
		else
			ajax('fix', {
				actionid : data.actionid, 
				data: addVisitorPlatform({})
			});
		cleanRequest();// excute delayed request in queue
	}

	var ifm = document.createElement('iframe');
	ifm.style.display="none";
	ifm.src = "//meotrics.com/iframe.html?x=" + mt.appid + '-' + mt.actionid;
	document.body.appendChild(ifm);

	mt.onready();
})();