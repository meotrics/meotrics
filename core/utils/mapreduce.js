var map = function(){
	 var key = this.actiontype + this.user._id;
	 var value;
	 if(this.actiontype != 'purchase'){
	 	value = {
                  p100: this.price >= 100? 1 : 0,
                  p50: this.price >= 50? 1 : 0,
                  view:0
     	};
	 }else{
	    value = {
                  view: 1,
                  p100: 0,
                  p50: 0
     	};
	 }
     

    emit( key, value );
}

var reduce = function(key, values){
   var reducedObject;
   if(key[0] == 'p'){
   	 reducedObject = {
		nump100: 0,
		nump50: 0
	  }
	  values.forEach(function(value){
	    reducedObject.nump100 = reducedObject.nump100 + value.p100;
	    reducedObject.nump50 = reducedObject.nump50 + value.p50;
	  });  
   }else{
     reducedObject = {
       totalview: 0
     }
     reducedObject.totalview = values.length;
   }
	
    return 	reducedObject;
}

db.actions.mapReduce(
	map,
	reduce,
	{
		out: "haha"
	}
)