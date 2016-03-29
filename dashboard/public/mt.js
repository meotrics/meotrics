//THIS FILE IS CALLED AFTER MT.CODE IS CALLED
var mt = mt || {};
mt.__callbackQ = mt.__callbackQ || [];

function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
	return undefined;
}

mt.mtid = getCookie("mtid");

mt.getmtid = function (callback) {
	if (Meotrics.__visitorid !== undefined) callback(Meotrics.__visitorid);
	else {
		//set up new user cookie
		$.post(host + '/s', function () {
			//the cookie must be setted now
			callback(Meotrics.__visitorid);
		});
	}
};

mt.__sendrequest(url, data) {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'http://meotrics.dev/track?_mtid=55';
	script.style.display = 'none';
	document.appendChild(script)
}

mt.identify = function(data)
{
	if (mt.__isReady != true) {
		mt._requestQueue.push({action: 'identify', data:data, time: new Date()});
		return;
	}

	mt.__sendrequest(mt.host + '/identify/' + mt.appid, data)

};

mt.track = function (event, data) {
	if (mt.__isReady != true) {
		mt.__requestQueue.push({action: 'track', event: event, data: data, time: new Date()});
		return;
	}

	data._referrer = document.referrer;
	data._type = event;
	data._screenres = navigator.availWidth + "x" + navigator.availHeight;
	mt.__sendrequest(mt.host + '/track/' + mt.appid, data);
};

//ENTRY POINT
if (mt.mtid === undefined) {
	sendrequest(mt.host + '/setup/ + mt.appid', undefined, function () {
	});
}

//finish the queue
for (var rq in mt.__requestQueue) {
	if (rq.action == 'track')
		mt.track(rq.event, rq.data);
	else if (rq.action == 'identify')
		mt.identify(rq.data);
}
delete mt.__requestQueue;

//release memory
delete mt.__callbackQueue;


