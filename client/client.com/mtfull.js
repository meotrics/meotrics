// (function () {
// 	var encodeFunction = encodeURIComponent, i = 0, j = 0, isready, request_queue2 = [], doc = document;
	
// 	window.addEventListener("beforeunload", function (e) {
// 		//ajax('x/' + mt.actionid);
// 	});

// 	function ajax(url, data, callback) {
// 		var script = doc.createElement('script');
// 		// script.type = 'text/javascript'; comment this because we dont need to excute the script
// 		script.src = '//127.0.0.1:1711/' + mt.appid + '/' + url + (data ? '?' + serialize(data) : '');
// 		script.style.display = 'none';
// 		script.onreadystatechange = script.onload = callback;//for IE
// 		doc.body.appendChild(script);
// 	}

// 	function serialize(obj, prefix) {
// 		var str = [];
// 		for (var p in obj)/* if (obj.hasOwnProperty(p)) */{
// 			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
// 			str.push(v instanceof Object ? serialize(v, k) : encodeFunction(k) + "=" + encodeFunction(v));
// 		}
// 		return str.join("&");
// 	}

// 	mt.info = function (data, callback, callback2, callback3) {
// 		if(typeof data == 'string') data = {userid: data};
// 		return isready ? ajax('info', data, callback || callback3) : request_queue2.push(['info', data]);
// 	};

// 	mt.clear = function (callback, callback2, callback3, callback4) {
// 		return isready ? ajax('clear', callback3 /*alway undefined, use callback3 for better minify*/, callback || callback4) : request_queue2.push(['clear']);
// 	};

// 	mt.track = function (event, data, time, callback) {
// 		data._deltat = (new Date() - time) / 1000;
// 		data._typeid = event;
// 		return isready ? ajax('track', addVisitorPlatform(data), callback) : request_queue2.push(['track', event, data, new Date()]);
// 	};

// 	function addVisitorPlatform(data) {
// 		data._ref = doc.referrer;
// 		data._scr = screen.width + "x" + screen.height;
// 		data._url = location.href;
// 		data._link = mt.actionid;
// 		return data;
// 	}

// 	// clean request queue
// 	function cleanRequest() {
// 		// clean queue number 2 when out of element in queue number 1
// 		if (i + 1 >= mt.rq.length) return cleanRequest2();
// 		var rq = mt.rq[i++];
// 		mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest);
// 	}

// 	// clean request queue step 2
// 	function cleanRequest2() {
// 		if (j + 1 >= request_queue2.length) // clean the state when done
// 			return isready = 1;
// 		var rq = request_queue2[j++];
// 		mt[rq[0]](rq[1], rq[2], rq[3], cleanRequest2);
// 	}

// 	mt.excute = function(event){
// 		var data = JSON.parse(event.data);
// 		mt.actionid = data.actionid;
// 		if(data.lastactionid !== undefined)
// 		{
// 			var o1 = {
// 				actionid : data.actionid, 
// 				lastactionid: data.lastactionid
// 			}
// 			addVisitorPlatform(o1);
// 			ajax('fix',o1);
// 		}
// 		else
// 		{
// 			var o2 = {actionid : data.actionid};
// 			addVisitorPlatform(o2);
// 			ajax('fix', o2);
// 		}
// 		cleanRequest();// excute delayed request in queue
// 	}

// 	var ifm = doc.createElement('iframe');
// 	ifm.style.display="none";
// 	ifm.src = "//meotrics.com/iframe.html?x=" + mt.appid + '-' + mt.actionid;
// 	doc.body.appendChild(ifm);

// 	mt.onready();
// })();
(function () {
	
	var host = "meotrics.com";
	if(location.hostname == "client.meotrics.dev") host = "meotrics.dev";
	var encodeFunction = encodeURIComponent, i = 0, j = 0, isready, request_queue2 = [], doc = document;
	
	var T = 2;
	var SESSIONGAP = 1200;
	var STARTANDLEAVE;
	var ISBLUR, lastcycle,totaltime,totalidle;
	var lefttime;
	var canstartnewsession = false;
	function init()
	{	
		totaltime = 0;
		totalidle = 0;
		STARTANDLEAVE = ISBLUR = lastcycle = lefttime = undefined;
	}

	function attach(event, fn)
	{
		if (window.addEventListener)
			window.addEventListener(event, fn, false);
		else if (window.attachEvent)
			window.attachEvent('on' + event, fn);
	}

	function interact(){
		ISBLUR = false;
		totalidle = 0;
		if(canstartnewsession) startsession();
		canstartnewsession = false;
	}

	attach("beforeunload", function (e) {
		var delta = Math.round(new Date().getTime() / 1000) - lastcycle;
		stopsession(totaltime+delta);
	});

	attach('keydown', interact);
	attach('mousemove', interact);
	attach('mousedown', interact);
	attach('scroll', interact);
	attach('resize', interact);
	attach('pagehide', interact);
	attach('pageshow', interact);

	attach('blur', function(){
		ISBLUR = true;
		lefttime = Math.round(new Date().getTime() / 1000);
	})

	
	attach('focus', function(){
		if(ISBLUR === undefined){
			startsession()
		}
		if(ISBLUR === false) return;
		if(ISBLUR === true)
		{
			var delta = Math.round(new Date().getTime() / 1000) - lefttime;
			//the user has left for too long
			if(delta > SESSIONGAP)
			{
				stopsession(totaltime);
				startsession();
				return;
			}
			else
			{}
		}
		ISBLUR = false;
	});


	function stopsession(sessiontime)
	{
		ISBLUR = undefined;
		('x/' + ajaxmt.actionid, {sessiontime: sessiontime});
	}

	function addIframeLink(){
		var ifm = doc.createElement('iframe');
		ifm.style.display="none";
		ifm.src = "//" + host + "/iframe.html?x=" + mt.appid + '-' + mt.actionid;
		if(doc.body === undefined) doc.head.appendChild(ifm);
		else doc.body.appendChild(ifm);
	}

	var startsession = mt.startsession = function ()
	{
		//start a new pageview
		mt.track('pageview', {_link: null, _callback : true},0, function(){
			init();
			addIframeLink();
		});
	}

	function backgroundtimer()
	{
		if(ISBLUR === false)
		{
			totalidle+=T;
			totaltime+=T;
			if(totalidle > SESSIONGAP){
				stopsession(Math.max(totaltime, SESSIONGAP));
				canstartnewsession = true;
			}
		}
		lastcycle = Math.round(new Date().getTime() / 1000);
		return setTimeout(backgroundtimer, T*1000)
	}

	function ajax(url, data, callback) {
		var script = doc.createElement('script');
		// script.type = 'text/javascript'; comment this because we dont need to excute the script
		script.src = '//' + host + ":1711/" + mt.appid + '/' + url + (data ? '?' + serialize(data) : '');
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
		if(data._link === undefined) data._link = mt.actionid;
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
		console.log(data);
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
	init();
	backgroundtimer();
	addIframeLink();
	mt.onready();
})();