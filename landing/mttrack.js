//very light script to load first
var mt ={
track : function(t,r){
	mt.rq.push(["track",t,r,new Date])
},
info : function(t){
	mt.rq.push(["info",t])
},
clear : function(){
	mt.rq.push(["clear"])
},
onready : function(){
	if(mt.event) mt.excute(mt.event)
},
rq : []
};
(function(){
	addEventListener("message", function(ev){
		var origin = ev.origin || ev.originalEvent.origin;
		if (origin.split('/')[2] !== "meotrics.com") return;
		mt.event = ev;
		if(mt.excute) mt.excute(ev);
	}, false);

	function getStyle (el, prop) {
		return getComputedStyle === 'undefined' ? el.currentStyle[prop] : getComputedStyle(el, null).getPropertyValue(prop);
	}

	var s = getStyle(document.getElementById('meotrics29101992'), "font-family");
	if(s.charAt(0) === '"') s=s.substr(1, s.length - 2);
	s = s.split('-');
	mt.appid=s[0];
	mt.actionid=s[1];
	var script = document.createElement('script');
	script.setAttribute('src','//meotrics.com/mtfull.js');
	script.setAttribute('defer', 'defer');
	script.setAttribute('async', true);
	document.head.appendChild(script);
})();
