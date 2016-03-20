var MongoClient = require('mongodb').MongoClient,
    url = 'mongodb://localhost:1234/test',
    mongodb = require('mongodb'),
    converter = require('../utils/fakeidmanager.js'),
    async = require('async'),
    converter = new converter.IdManager(),
	db = null;

MongoClient.connect(url)
    .then(function(database){
        console.log('[MongoDB] connected');
        db = database;
        
        groupby('meotrics_1', '56e81a3344ae6d1f522e94da', 'gender', 'string', 'age', 'number', function(err, results){
          console.log(err);
          console.log(JSON.stringify(results));
        });

        // Listen for some events
        db.on('reconnect', function(data){
          console.log(data);
          console.log('[MongoDB] reconnect success');
        });
        db.on('error', function(err){
          console.log('[MongoDB] error', err.message);
        });
        db.on('close', function(err){
          console.log('[MongoDB] disconnected');
        });
        // return db.collection('test').find({isUser: true}).toArray();
    }).catch(function(err){
        console.error("[MongoDB]", err.message);
    }); 





function groupby(collection, segmentid, field1, type1, field2,type2, callback){
	console.log('haha');
	if(field2 == undefined){
		converter.toIDs(['_isUser', '_segments', field1], function(ids){

		    var maxfield1;
		    var minfield1;

		    var query = {};
		    var sort = {};
		    query[ids['_isUser']] = true;
		    sort[ids[field1]] = -1;
		    db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function(r){
		      maxfield1 = r[0][ids[field1]];
		      sort[ids[field1]] = 1;
		      return db.collection(collection).find(query).sort(sort).limit(1).toArray();
		    }).then(function(r){
		      	minfield1 = r[0][ids[field1]];
		      	console.log(minfield1, maxfield1);

		      	var matchClause = {"$match": {}};
		      	matchClause['$match'][ids['_isUser']] = true;
		     	// matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);
		      
		      	var projectClause = {"$project": {}};
		      	projectClause['$project']['_id'] = 0;
		      	projectClause['$project'][ids[field1]] = 1;
		      
		      	var groupClause = {"$group": {}};
		      	groupClause['$group']['_id'] = '$'+ids[field1];
		      	groupClause['$group']['count'] = {'$sum': 1};

		      	var cursor = db.collection(collection).aggregate([
		        	matchClause,
		        	projectClause,
		        	groupClause
		      	], {
		        	cursor: {batchSize: 20},
		        	allowDiskUse: true
		      	});

		      	if(field1 == 'age'){
		      		var results = [0, 0, 0, 0, 0, 0, 0];
		          	var done = false;

		          	async.whilst(
			            function(){return done == false},
			            function(callback){

			              cursor.next().then(function(r){
			                if(r){
							  if(r._id<=18){
			                    results[1]+=r.count;
			                  }else if ((r._id > 18)&&(r._id<=24)){
			                    results[2]+=r.count;
			                  }else if ((r._id > 25)&&(r._id<=34)){
			                    results[3]+=r.count;
			                  }else if ((r._id > 35)&&(r._id<=44)){
			                    results[4]+=r.count;
			                  }else if ((r._id > 44)&&(r._id<=54)){
			                    results[5]+=r.count;
			                  }else {
			                    results[6]+=r.count;
			                  }
			                }else{
			                  done = true;
			                }
			                callback(null, results);
			              }).catch(function(e){
			                callback(e);
			              });
			            }, function(err, results){
			              if(results != undefined){
			                results = [{key: {to:18}, count: results[1]}, {key: {from: 19, to:24}, count: results[2]},
			                           {key: {from: 25, to:34}, count: results[3]}, {key: {from: 35, to:44}, count: results[4]},
			                           {key: {from: 45, to:54}, count: results[5]}, {key: {from: 55}, count: results[6]}];
			              }
			              callback(err, results);
			            });
		        }else{
		        	if(type1 == 'string'){
						var results = [];
				        var done = false;
				        async.whilst(
				          function () { return done == false; },
				          function(callback){
				            cursor.next().then(function(r){
				              if(r){
				                var element = {};
				                element.key = r._id;
				                element.count = r.count;
				                results.push(element);
				              }else{
				                done = true;
				              }
				              callback(null, results);
				            }).catch(function(e){
				              callback(e);
				            });
				          }, function(err, results){
				            callback(err, results);
				          }
				        );
		        	}else{
		        		if(typeof minfield1 == 'number' && typeof maxfield1 == 'number'){
		        			var spaces = 1;
				            var distance = 0;

				            if(maxfield1 - minfield1 >= 5){
				              spaces = 5;
				              distance = Math.floor((maxfield1 - minfield1) / 5);
				            }

				            var done = false;
				       		var results = [];
				            for(var i=0;i<spaces;i++){
				              results[i] = 0;
				            }
				           
				            async.whilst(
				              function(){return done == false},
				              function(callback){
				                cursor.next().then(function(r){
				                  if(r){
				                    var value = r._id;
			                      	if((value>=minfield1)&&(value<=maxfield1)){
			                        	if(maxfield1 == minfield1){
				                          	results[0] = r.count;
				                          	done = true;
			                        	}else{
				                          	var i = Math.floor(((value-minfield1)*spaces)/(maxfield1-minfield1));
				                          	if(i == spaces){
				                            	i--;
				                          	}
			                          		results[i] += r.count;
			                        	}
			                        }
			                      }else{
				                    done = true;
				                  }
				                  callback(null, results);
				                }).catch(function(e){
				                  callback(e);
				                });
				              }, function(err, results){
				                var finalResults;
				                if(results != undefined){
				                  finalResults = [];
				                  for(var i=0;i<spaces;i++){
				                    var element = {};
				                    element.key = {
				                      from: minfield1+i*distance,
				                      to: minfield1+(i+1)*distance
				                    };
				                    if(i==spaces-1){
				                      element.key.to = maxfield1;
				                    }
				                    element.count = results[i];
				                    finalResults.push(element);
				                  }
				                }
				                callback(err, finalResults);
				              });
		        		}else{
		        			// ERROR TYPE NOT HOP LE
		        		}
		        	}
		        }
		  	});
		});
	}else{
		converter.toIDs(['_isUser', '_segments', field1, field2], function(ids){

			var maxfield1;
			var minfield1;
			var maxfield2;
			var minfield2;

			var query = {};
			var sort = {};
			query[ids['_isUser']] = true;
			sort[ids[field1]] = -1;
			db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function(r){
				maxfield1 = r[0][ids[field1]];
				sort[ids[field1]] = 1;
				return db.collection(collection).find(query).sort(sort).limit(1).toArray();
			}).then(function(r){
				minfield1 = r[0][ids[field1]];
				sort[ids[field2]] = -1;
				return db.collection(collection).find(query).sort(sort).limit(1).toArray();
			}).then(function(r){
				maxfield2 = r[0][ids[field2]];
				sort[ids[field2]] = 1;
				return db.collection(collection).find(query).sort(sort).limit(1).toArray();
			}).then(function(r){
				minfield2 = r[0][ids[field2]];

				var matchClause = {"$match": {}};
				matchClause['$match'][ids['_isUser']] = true;
				// matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

				var projectClause = {"$project": {}};
				projectClause['$project']['_id'] = 0;
				projectClause['$project'][ids[field1]] = 1;
				projectClause['$project'][ids[field2]] = 1;

				var groupClause = {"$group": {}};
		        groupClause['$group']['_id'] = '$'+ids[field1];
		        groupClause['$group']['values'] = {'$push': '$'+ids[field2]};
		        groupClause['$group']['count'] = {'$sum': 1};

				var sortClause = {"$sort": {}};
				sortClause['$sort']['_id'] = 1;

				return db.collection(collection).aggregate([
					matchClause,
					projectClause,
					groupClause,
					sortClause
					], {
						cursor: {batchSize: 20},
						allowDiskUse: true
					});
			}).then(function(cursor){

				if((typeof minfield1 == typeof maxfield1)&&(typeof maxfield1 == typeof type1)&&(type1 == 'string')){
					var results = [];
					if((typeof minfield2 == typeof maxfield2)&&(typeof maxfield2 == typeof field2)&&(typeof field2 == 'string')){
						var done = false;
						async.whilst(
							function () { return done == false; },
							function(callback){
								cursor.next().then(function(r){
									if(r){
										var element = {};
										element.key = r._id;
										element.count = r.count;
										element.detail = summingString(r.values);
										results.push(element);
									}else{
										done = true;
									}
									callback(null, results);
								}).catch(function(e){
									callback(e);
								});
							}, function(err, results){
								callback(err, results);
							}
						);
					}else if((typeof minfield2 == typeof maxfield2)&&(typeof maxfield2 == type2)&&(type2 == 'number')){
						console.log('ok');
						var results = [];
						var spaces = 1;
						var distance = 0;
						if(maxfield2 - minfield2 >= 5){
							spaces = 5;
							distance = Math.floor((maxfield2 - minfield2) / 5);
						}

						var done = false;
			            async.whilst(
							function () { return done == false; },
							function(callback){
								cursor.next().then(function(r){
									if(r){
										// console.log(r);
										var element = {};
										element.key = r._id;
										element.count = r.count;
										element.detail = summingNumber(minfield2, maxfield2, spaces, distance, r.values, field2);
										results.push(element);
									}else{
										done = true;
									}
									callback(null, results);
								}).catch(function(e){
									callback(e);
								});
							}, function(err, results){
								callback(err, results);
							}
						);
					}else{
	              	// ERROR TYPE
	              	
	              }
	          }else if((typeof minfield1 == typeof maxfield1)&&(typeof maxfield1 == typeof field1)&&(typeof field1 == 'number')){
	          	if((typeof minfield2 == typeof maxfield2)&&(typeof maxfield2 == typeof field2)&&(typeof field1 == 'string')){

	          	}else if((typeof minfield2 == typeof maxfield2)&&(typeof maxfield2 == typeof field2)&&(typeof field1 == 'number')){

	          	}else{
	              	// ERROR TYPE
	            }
	          }else{
	            	// ERROR TYPE
	            }
	        }).catch(function(err){
	        	callback(err);	
	        });
	    });
	}
}

function summingString(values){
	var results = [];
	var length = values.length;
  	values.sort();

  	for(var i=0;i<length;i++){
  		var str = values[i];
  		for(var j=i;j<length;j++){
  			if(values[j] != values[i]){
  				var element = {};
  				element.key = str;
  				element.count = j-i;
  				i = j-1;
  				results.push(element);
  				break;
  			}else if((j+1) == length){
  				var element = {};
  				element.key = str;
  				element.count = j - i + 1;
  				i = j;
  				results.push(element);
  			}
  		}
  	}

  	return results;
}

function summingNumber(min, max, spaces, distance, values, field){
	console.log(min, max, distance);
	var results = [];
	var length = values.length;
	values.sort();

	if(field == 'age'){
		var tempresults = [0, 0, 0, 0, 0, 0];
		for(var i = 0; i<length;i++){
			var age = values[i];
          	var count = 0;

			for(var j=i;j<length;j++){
				if(values[j] != age){
					count = j-i;
					i = j-1;
					break;
				}else if((j+1) == length){
					count = j - i + 1;
					i = j
				}
			}

			if(age<=18){
	            tempresults[0] += count;
	        }else if ((age > 18)&&(age<=24)){
            	tempresults[1] += count;
          	}else if ((age > 24)&&(age<=34)){
            	tempresults[2] += count;
          	}else if ((age > 34)&&(age<=44)){
            	tempresults[3] += count;
          	}else if ((age > 44)&&(age<=54)){
            	tempresults[4] += count;
          	}else {
            	tempresults[5] += count;
          	}
		}

		results = [{key: {to:18}, count: tempresults[0]}, {key: {from: 19, to:24}, count: tempresults[1]},
	               {key: {from: 25, to:34}, count: tempresults[2]}, {key: {from: 35, to:44}, count: tempresults[3]},
	               {key: {from: 45, to:54}, count: tempresults[4]}, {key: {from: 55}, count: tempresults[5]}];
	}else{

		var tempresults = [];
		for(var i=0;i<spaces;i++){
			tempresults[i] = 0;
		}

		for(var i=0;i<length;i++){
			var temp = values[i];
			for(var j=i;j<length;j++){
				if(values[j] != temp){
					var index = Math.floor(((temp-min)*spaces)/(max-min));
					if(index == spaces){
						index --;
					}
					count = j-i;
					tempresults[index] += count;
					i = j-1;
					break;
				}else if((j+1) == length){
					var index = 0;
					if(max != min){
						index = Math.floor(((temp-min)*spaces)/(max-min));
						if(index == spaces){
							index --;
						}
					}
					count = j - i + 1;
					tempresults[index] += count;
					i = j;
				}
			}
		}

		for(var i=0;i<spaces;i++){
	        var element = {};
	        element.key = {
	          from: min+i*distance,
	          to: min+(i+1)*distance
	        };
	        if(i==spaces-1){
	          element.key.to = max;
	        }
	        element.count = tempresults[i];
	        results.push(element);      
		}
	}
  	return results;
}