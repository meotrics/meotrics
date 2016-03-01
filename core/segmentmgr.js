exports.SegmentMgr = function (db) {
	var me = this;
	var segdb = new SegmentDb(db)
	/*
	param:
	{
		appid: number // id of the application
		userid: number // creator user id in the application (not the mtid)
		name: string // name of the segmentation
		condition: [{
			typeid: number // event(action type) 
			f: string // the function perform on object, must be <avg, count, sum>
			field: string // field that condition take on, eg "amount"
			operator: string // the operator, eg ">"
			value: string | number // value apply in the condition
			conditions:  ["amount", ">", 2, "and", "price", "=", 40]
			}]; eg: 
			var testJson = [{
				type: "purchase", f: "avg", field: "amount", operator: ">", value: 5,
				conditions:  ["amount", ">", 2, "and", "price", "=", 40]
				},
				"and", {
				type: "pageview", f: "count", field: "pid",  operator: ">",  value: 5,
				conditions: ["amount", ">", 5, "and", "price", "=", "30"]
				}];
	}
	
	*/
	this.create = function (param, callback) {
		
		param.ctime = new Date();

		db.collection("segments").insert(param, { w: 1 }, function (err, result) {
			if (err) throw err;
			console.log(result);
			
			db.collection("segments").find({a: {$eq: 5}}, function (err, docs){
				if(err) throw err;
				console.log(docs);
				callback(docs[0].a);
				
			})
		});


	}
	
	this.update = function(param, callback){
		segdb.update(param, callback)
	}
}
