//very light script to load first
var event,
mt.track = function(t,r){
	mt.rq.push(["track",t,r,new Date])
};
mt.identify = function(t){
	mt.rq.push(["identify",t])
}
mt.clear = function(){
	mt.rq.push(["clear"])
}
mt.onready = function(){
	if(event) mt.excute(event)
}
mt.rq = []
}

(function(){
	addEventListener("message", function(ev){
		var origin = event.origin || event.originalEvent.origin
		if (origin.split('/')[2] !== "meotrics.com") return;
		event = ev
		if(mt.excute) mt.excute(ev);
	}, false);

	var s = getComputedStyle(document.getElementById('mt' + mt.appid), null);
	mt.actionid = s['font-family'].substr(1, s.content.length - 2);
})();