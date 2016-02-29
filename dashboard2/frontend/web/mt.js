var Meotrics = Meotrics || {};
var host = "//dev.meotrics.com/";
function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}
Meotrics.__visitorid = getCookie("meovisitorid");
Meotrics.getVisitorId = function () {
	return Meotrics.__visitorid;
};

Meotrics.EVIEWPAGE = 1
Meotrics.EPURCHASE = 2
Meotrics.CHURN = 3


Meotrics.set = function (data, done, fail) {
	$.post(host + 'set', data, function () {
		if (done !== undefined) done();
	}).fail(function () {
		if (fail !== undefined) fail()
	});
};

Meotrics.getBrowserId = function () {
	// Opera 8.0+
	var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
	// Firefox 1.0+
	var isFirefox = typeof InstallTrigger !== 'undefined';
	// At least Safari 3+: "[object HTMLElementConstructor]"
	var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
	// Internet Explorer 6-11
	var isIE = /*@cc_on!@*/false || !!document.documentMode;
	// Edge 20+
	var isEdge = !isIE && !!window.StyleMedia;
	// Chrome 1+
	var isChrome = !!window.chrome && !!window.chrome.webstore;
	// Blink engine detection
	//var isBlink = (isChrome || isOpera) && !!window.CSS;

	if (isOpera) return 1;
	if (isFirefox) return 2;
	if (isSafari) return 3;
	if (isIE) return 4;
	if (isEdge) return 5;
	if (isChrome) return 6;
	return -1; //unknow
};

Meotrics.browserversion = function () {
	var ua = navigator.userAgent;
	var tem;
	var M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
	if (/trident/i.test(M[1])) {
		tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
		return parseInt(tem[1] || -1);
	}
	if (M[1] === 'Chrome') {
		tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
		if (tem != null)
			return parseInt(tem[2]);
	}
	M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
	if ((tem = ua.match(/version\/(\d+)/i)) != null) M.splice(1, 1, tem[1]);
	return parseInt(M[2]);
};


Meotrics.record = function (eventname, n1, n2, s1, s2, totalsec, done, fail) {
	$.post(host + 'record', {
		event: eventname,
		visitorid: Meotrics.getVisitorId(),
		eventid: eventname,
		appid: Meotrics.getAppId(),
		osid: Meotrics.getOsId(),
		browserid: Meotrics.getBrowserId(),
		locationid: Meotrics.getLocationId(),
		referer: document.referrer,
		campaignid: -1,
		deviceid: -1,
		screenres: navigator.availWidth + "x" + navigator.availHeight,
		url: window.location.toString(),
		n1: n1,
		n2: n2,
		s1: s1,
		s2: s2,
		lang: navigator.language,
		browserversion: Meotrics.browserversion(),
		osversion: -1,
		devicetype: Meotrics.deviceType()
	}, function () {
		if (done !== undefined) done();
	}).fail(function () {
		if (fail !== undefined) fail();
	});
};


if (Meotrics.__visitorid === undefined) {
	$.post(host + 'new', function () {

	})
}

Meotrics.deviceType = function () {

	var check = false;
	(function (a) {
		if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))check = true
	})(navigator.userAgent || navigator.vendor || window.opera);

	if (check === true)//MOBILE
		return 2;
	else return 1;


}

Meotrics.getOsId = function () {

	var unknown = '-';
	// browser
	var nAgt = navigator.userAgent;

	// mobile version
	var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

	// system
	var os = unknown;
	var clientStrings = [
		{id: 1, s: 'Windows 10', r: /(Windows 10.0|Windows NT 10.0)/},
		{id: 1, s: 'Windows 8.1', r: /(Windows 8.1|Windows NT 6.3)/},
		{id: 1, s: 'Windows 8', r: /(Windows 8|Windows NT 6.2)/},
		{id: 1, s: 'Windows 7', r: /(Windows 7|Windows NT 6.1)/},
		{id: 1, s: 'Windows Vista', r: /Windows NT 6.0/},
		{id: 1, s: 'Windows Server 2003', r: /Windows NT 5.2/},
		{id: 1, s: 'Windows XP', r: /(Windows NT 5.1|Windows XP)/},
		{id: 1, s: 'Windows 2000', r: /(Windows NT 5.0|Windows 2000)/},
		{id: 1, s: 'Windows ME', r: /(Win 9x 4.90|Windows ME)/},
		{id: 1, s: 'Windows 98', r: /(Windows 98|Win98)/},
		{id: 1, s: 'Windows 95', r: /(Windows 95|Win95|Windows_95)/},
		{id: 1, s: 'Windows NT 4.0', r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
		{id: 1, s: 'Windows CE', r: /Windows CE/},
		{id: 1, s: 'Windows 3.11', r: /Win16/},
		{id: 2, s: 'Android', r: /Android/},
		{id: 3, s: 'Open BSD', r: /OpenBSD/},
		{id: 4, s: 'Sun OS', r: /SunOS/},
		{id: 5, s: 'Linux', r: /(Linux|X11)/},
		{id: 6, s: 'iOS', r: /(iPhone|iPad|iPod)/},
		{id: 7, s: 'Mac OS X', r: /Mac OS X/},
		{id: 8, s: 'Mac OS', r: /(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
		{id: 9, s: 'QNX', r: /QNX/},
		{id: 10, s: 'UNIX', r: /UNIX/},
		{id: 11, s: 'BeOS', r: /BeOS/},
		{id: 12, s: 'OS/2', r: /OS\/2/},
		{id: 13, s: 'Search Bot', r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
	];

	var osid;
	for (var id in clientStrings) {
		var cs = clientStrings[id];
		if (cs.r.test(nAgt)) {
			os = cs.s;
			osid = cs.id;
			break;
		}
	}

	var osVersion = unknown;

	if (/Windows/.test(os)) {
		osVersion = /Windows (.*)/.exec(os)[1];
		os = 'Windows';
	}

	switch (os) {
		case 'Mac OS X':
			osVersion = /Mac OS X (10[\.\_\d]+)/.exec(nAgt)[1];
			break;

		case 'Android':
			osVersion = /Android ([\.\_\d]+)/.exec(nAgt)[1];
			break;

		case 'iOS':
			osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
			osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
			break;
	}

	return osid;
};