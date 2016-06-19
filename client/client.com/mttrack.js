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
		var origin = ev.origin || ev.originalEvent.origin
		if (origin.split('/')[2] !== "meotrics.com") return;
		mt.event = ev
		if(mt.excute) mt.excute(ev)
	}, false);

	var s = getComputedStyle(document.getElementById('meotrics29101992'), null);
	var cs = s['font-family'].substr(1, s['font-family'].length - 2).split('-');
	mt.appid = cs[0];
	mt.actionid = cs[1];
	var script = document.createElement('script');
	script.setAttribute('src','/mtfull.js');
	script.setAttribute('defer', 'defer');
	script.setAttribute('async', true);
	document.head.appendChild(script);
})()