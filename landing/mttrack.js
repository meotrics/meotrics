var event, mt = { track:function(t,r){
	mt.rq.push(["track",t,r,new Date])
}, identify:function(t){
	mt.rq.push(["identify",t])
}, clear:function(){
	mt.rq.push(["clear"])
}, onready:function(){
	if(event) mt.excute(event)
},rq:[]
}
addEventListener("message", function(ev){
	var origin = event.origin || event.originalEvent.origin
	if (origin.split('/')[2] !== "meotrics.com") return;
	event = ev
	if(mt.excute) mt.excute(ev);
}, false)