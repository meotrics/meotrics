"use strict";

var IDMgr = require('./fakeidmanager.js'),
		async = require('async'),
		mongodb = require('mongodb'),
		converter = new IDMgr.IdManager();

//Chú ký triển khai hàm sinh match
//phải chia ra làm 2 loại, phép toán trên người và action

exports.createSegment = function (segment, callback) {
	var outcollection = "meotrics_segment" + segment._id.toString();
	getQuery(segment.query, function () {
		db.collection(collection).mapReduce(out.map, out.reduce, {
			out: outcollection,
			query: out.option,
			finalize: out.finalize,
			sort: {_mtid: 1}
		}, function (res) {
			callback(outcollection);
		});
	});
};

exports.getQuery = function getQuery(json, callback) {
	handleInput(json).then(function (r) {
		return queryFilter(r);
	}).then(function (r) {
		buildMapReduce(json, function (ret) {
			ret.option = r;
			callback(ret);
		});
	}).catch(mtthrow);
};

function mtthrow(err) {
	if (err) {
		console.log(err);
		setTimeout(function () {
			throw err;
		});
	}
}
function updateDB(actionC, reduceC, segmentID) {
	var _mtid = '';
	var _segments = '';
	converter.toID('_mtid')
			.then(function (r) {
				_mtid = r;
				return converter.toID('_segments');
			}).then(function (r) {
		_segments = r;
		return db.collection(reduceC).find({isIn: true}, {_id: 1}).batchSize(100);
	}).then(function (cursor) {
		var _mtids = [];
		async.forever(
				function (next) {
					cursor.hasNext(function (err, r) {
						if (err) {
							next(err);
						} else {
							if (r) {
								cursor.next(function (err, rs) {
									if (err) {
										next(err);
									} else {
										_mtids.push(rs._id);
										if (_mtids.length == 100) {
											var query = {};
											var update = {'$addToSet': {}};
											query[_mtid] = {'$in': _mtids};
											update['$addToSet'][_segments] = new mongodb.ObjectID(segmentID);
											db.collection(actionC).updateMany(query, update);
											db.collection(actionC).updateMany({_id: {'$in': _mtids}}, update);
											_mtids = [];
										}
										next(null);
									}
								});
							} else {
								next({code: -1});
							}
						}
					})
				}, function (e) {
					if (e.code == -1) {
						if (_mtids.length != 0) {
							var query = {};
							var update = {'$addToSet': {}};
							query[_mtid] = {'$in': _mtids};
							update['$addToSet'][_segments] = new mongodb.ObjectID(segmentID);
							db.collection(actionC).updateMany(query, update);
							db.collection(actionC).updateMany({_id: {'$in': _mtids}}, update);
						}
					} else {
						mtthrow(e);
					}
				});
	}).catch(mtthrow);
}
// ------------------------------------------------------
function revenue(collection, segID, wrap, inside, callback) {
	var maxWrap;
	var minWrap;
	var maxInside;
	var minInside;
	var ids;
	converter.toIDs(['_isUser', '_segments', wrap, inside], function (r) {
		ids = r;

		var query = {};
		var sort = {};
		query[ids['_isUser']] = true;
		sort[ids[wrap]] = -1;

		db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function (r) {
			maxWrap = r[0][ids[wrap]];
			sort[ids[wrap]] = 1;
			return db.collection(collection).find(query).sort(sort).limit(1).toArray();
		}).then(function (r) {
			minWrap = r[0][ids[wrap]];
			sort = {};
			sort[ids[inside]] = -1;
			return db.collection(collection).find(query).sort(sort).limit(1).toArray();
		}).then(function (r) {
			maxInside = r[0][ids[inside]];
			sort[ids[inside]] = 1;
			return db.collection(collection).find(query).sort(sort).limit(1).toArray();
		}).then(function (r) {
			minInside = r[0][ids[inside]];

			var matchClause = {"$match": {}};
			matchClause['$match'][ids['_isUser']] = true;
			matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segID);

			var projectClause = {"$project": {}};
			projectClause['$project']['_id'] = 0;
			projectClause['$project'][ids[wrap]] = 1;
			projectClause['$project'][ids[inside]] = 1;

			var groupClause = {"$group": {}};
			groupClause['$group']['_id'] = '$' + ids[wrap];
			groupClause['$group']['values'] = {'$push': '$' + ids[inside]};
			groupClause['$group']['count'] = {'$sum': 1};

			var cursor = db.collection(collection).aggregate([
				matchClause,
				projectClause,
				groupClause
			], {
				cursor: {batchSize: 20},
				allowDiskUse: true
			});
			var spaces = 5;
			var result = [];

			if (wrap == "gender" && inside == "age") {
				async.forever(
						function (next) {
							cursor.next()
									.then(function (r) {
										if (r) {
											var element = {};
											var array = r.values;
											element['key'] = r._id;
											element['total'] = r.count;
											array.sort();

											var values = [0, 0, 0, 0, 0, 0, 0];
											for (var i = 0; i < array.length; i++) {
												if (typeof array[i] != 'number') {

												} else if (array[i] <= 18) {
													values[1]++;
												} else if ((array[i] > 18) && (array[i] <= 24)) {
													values[2]++;
												} else if ((array[i] > 25) && (array[i] <= 34)) {
													values[3]++;
												} else if ((array[i] > 35) && (array[i] <= 44)) {
													values[4]++;
												} else if ((array[i] > 44) && (array[i] <= 54)) {
													values[5]++;
												} else {
													values[6]++;
												}
											}
											values[0] = r.count - (values[1] + values[2] + values[3] + values[4] + values[5] + values[6]);
											element['values'] = values;
											result.push(element);
											next(null);
										} else {
											callback(null, result);
											next({code: -1});
										}
									}).catch(function (e) {
								next(e);
							});
						}, function (err) {

						});
			} else {
				// if(typeof maxWrap === 'number'){
				//   if(typeof minWrap === 'number'){
				//     if(maxWrap - minWrap >= spaces){
				//       var result = [];
				//       var distance = (maxWrap - minWrap + 1) / spaces;
				//       var oldValue = minWrap;
				//       var newValue = minWrap;
				//       for(var i=0;i<spaces;i++){
				//         newValue += distance;
				//         var element = {};
				//         element.key = {
				//           min: oldValue,
				//           max: newValue
				//         };

				//         element.value = {

				//         }
				//       }
				//     }
				//   }
				// }else

				// }
			}


		}).catch(function (e) {
			callback(e);
		});

	});
}

// ----------------------------------------------------
function handleInput(object) {
	var sucback;
	var errback;
	var p = new Promise(function (resolve, reject) {
		sucback = resolve;
		errback = reject;
	});

	var counti = 0;
	var countj = 0;
	for (let i = 0; i < object.length; i += 2) {
		counti++;
		console.log(object)
		if (object[i].type === 'user') {
			counti--;
			if (object[i].conditions != undefined) {
				for (let j = 0; j < object[i].conditions.length; j += 4) {
					countj++;
					converter.toID(object[i].conditions[j])
							.then(function (r) {
								object[i].conditions[j] = r;
								countj--;
								if (counti == 0 && countj == 0) {
									sucback(object);
								}
							}).catch(function (e) {
						errback(e);
					});
				}
				if (object[i].conditions.length == 0) {
					sucback(object);
				}
			} else if (counti == 0) {
				sucback(object);
			}
		} else {
			converter.toID(object[i].field).then(function (r) {
				counti--;
				object[i].field = r;
				if (object[i].conditions != undefined) {

					for (let j = 0; j < object[i].conditions.length; j += 4) {
						countj++;
						converter.toID(object[i].conditions[j]).then(function (r) {
							object[i].conditions[j] = r;
							countj--;
							if (counti == 0 && countj == 0) {
								sucback(object);
							}
						}).catch(function (e) {
							errback(e);
						});
					}
					if (object[i].conditions.length == 0) {
						sucback(object);
					}
				} else if (counti == 0) {
					sucback(object);
				}
			}).catch(function (e) {
				errback(e);
			});
		}
	}
	return p;
}

function queryFilter(object) {
	var sucback;
	var errback;
	var p = new Promise(function (resolve, reject) {
		sucback = resolve;
		errback = reject;
	});

	var length = object.length;
	var query = {};

	if (length != 0) {
		query['$or'] = [];
		let c = 0;
		for (let i = 0; i < length; i += 2) {
			c++;
			conditionToQuery(object[i])
					.then(function (r) {
						query['$or'].push(r);
						c--;
						if (c == 0) {
							var hasUser = false;
							for (var i = 0; i < length; i += 2) {
								if (object[i].type == 'user') {
									hasUser = true;
									break;
								}
							}

							if (hasUser) {
								sucback(query);
							} else {
								converter.toID('_isUser')
										.then(function (r) {
											var temp = {};
											temp[r] = true;
											query['$or'].push(temp);
											sucback(query);
										}).catch(function (e) {
									errback(e);
								});
							}
						}
					}).catch(function (e) {
				errback(e);
			});
		}
	} else {
		sucback({});
	}

	return p;
}

function conditionToQuery(element) {
	var sucback;
	var errback;
	var p = new Promise(function (resolve, reject) {
		sucback = resolve;
		errback = reject;
	});

	var query = {};

	//you can decrease indenting by putting the else in front of the if
	if (element.conditions != undefined) {
		var conditions = element.conditions;
		var size = conditions.length;
		var hasOr = false;
		// you dont have to check for <or> operator, <or> and <and> operator
		// are treated same way, just delete the loop and the if
		for (var i = 3; i < size; i += 4) {
			if (conditions[i] == 'or') {
				hasOr = true;
				break;
			}
		}
		if (hasOr) {
			query['$or'] = [];
			for (var i = 0; i < size; i += 4) {
				if ((conditions[i + 3] == 'or') || (i + 3 == size)) {
					query['$or'].push(translateOperator(conditions, i));
				} else {
					for (var j = i + 7; j < size; j += 4) {
						if (conditions[j] == 'or') {
							break;
						}
					}
					var andQuery = {'$and': []};
					for (i; i < j; i += 4) {
						andQuery['$and'].push(translateOperator(conditions, i));
					}
					query['$or'].push(andQuery);
				}
			}
		} else {
			query = {};
			for (var i = 0; i < size; i += 4) {
				var returnValue = translateOperator(conditions, i);
				var key = Object.keys(returnValue)[0];
				query[key] = returnValue[key];
			}
		}

		if (element.type == 'user') {
			converter.toID('_isUser')
					.then(function (r) {
						console.log("USER");
						if (query['$or'] != undefined) {
							var temp = {};
							temp[r] = true;
							query = {"$and": [temp, query]};
						} else {
							query[r] = true;
						}
						sucback(query);
					}).catch(function (e) {
				errback(e);
			});
		} else {
			converter.toID('_typeid')
					.then(function (r) {
						console.log("NOT USER");
						if (query['$or'] != undefined) {
							var temp = {};
							temp[r] = new mongodb.ObjectID(element.type);
							query = {'$and': [temp, query]};
						} else {
							query[r] = new mongodb.ObjectID(element.type);
						}
						sucback(query);
					}).catch(function (e) {
				errback(e);
			});
		}
	} else {
		converter.toID('_typeid')
				.then(function (r) {
					query[r] = new mongodb.ObjectID(element.type);
					sucback(query);
				});
	}
	return p;
}

function buildQuery(condition) {

}

//really, put conditions[i+2] to a variable, its much easier to read
function translateOperator(conditions, i) {
	var query = {};
	switch (conditions[i + 1]) {
		case 'gt':
			query[conditions[i]] = {
				'$gt': conditions[i + 2]
			};
			break;
		case 'lt':
			query[conditions[i]] = {
				'$lt': conditions[i + 2]
			};
			break;
		case 'eq':
			query[conditions[i]] = conditions[i + 2];
			break;
		case 'ne':
			query[conditions[i]] = {
				'$ne': conditions[i + 2]
			};
			break;
		case 'gte':
			query[conditions[i]] = {
				'$gte': conditions[i + 2]
			};
			break;
		case 'lte':
			query[conditions[i]] = {
				'$lte': conditions[i + 2]
			};
			break;
		case 'con':
			query[conditions[i]] = {
				'$regex': conditions[i + 2]
			};
			break;
		case 'ncon':
			query[conditions[i]] = {
				'$regex': "^((?!" + conditions[i + 2] + ").)*$/"
			};
			break;
		case 'sw':
			query[conditions[i]] = {
				'$regex': '^' + conditions[i + 2]
			};
			break;
		case 'ew':
			query[conditions[i]] = {
				'$regex': conditions[i + 2] + '$'
			};
			break;
	}
	return query;
}

var testJson =
		[{
			type: "56e3a14a44ae6d70ddbf82a2", f: "avg", field: "amount", operator: ">", value: 5,
			conditions: ["amount", "gt", 2, "and", "price", "eq", 40]
		},
			"and",
			{
				type: "56e3a14a44ae6d70ddbf82a2", f: "count", field: "pid", operator: ">", value: 5,
				conditions: ["amount", "gt", 5, "or", "price", "eq", "30"]
			},
			"and",
			{
				type: 'user',
				conditions: ['age', 'eq', 'male']
			}];

//handleInput(testJson)
//		.then(function (r) {
//			console.log(r);
//			return queryFilter(r);
//		}).then(function (r) {
//	console.log(JSON.stringify(r));
//});


//purpose: build a piece of map function
//example: buildMapChunk({actiontype: "pageview", conditions: [{f: "count", field: "pid", operator: ">", value: 5, conditions: ["amount", ">", 5, "and", "price", "=", "dd"]})
//return: string contains compiled javascript code
//param: i=index of condition, for iteration purpose, condition=see example
function buildChunk(ind, element, _typeid, element_field) {
	//var reducecondcode = "";
	var reduceinitcode = "";
	var reduceaggcode = "";
	var finalizecode = "";

	var code = 'if(this["' + _typeid + '"].str==="' + element.type + '"){';
	//var conditions = element.conditions;
	var defvalcode = "";
	//var aggcode = "";
	//get the condition code
	if (element.conditions === undefined) console.log(element);
	var inlineconditions = true;//buildConditionsCode(element.conditions);

	//if the query is to count then just map 1, but if the query is to sum
	// and calculate average then have to map field's value
	if (element.f === "count") {
		code += "value.f" + ind + "=(" + inlineconditions + ")?1:0;";
		reduceinitcode += "returnObject.f" + ind + "=0;";
		reduceaggcode += "if(value.f" + ind + "===undefined) value.f" + ind + "=0;returnObject.f" + ind + "+=value.f" + ind + ";";
		defvalcode += "value.f" + ind + "=0;";
		finalizecode += "(returnObject.f" + ind + element.operator + JSON.stringify(element.value) + ")";
	}
	else if (element.f === "sum") {
		code += "value.f" + ind + "=(" + inlineconditions + ")?this." + element_field + ":0;";
		reduceinitcode += "returnObject.f" + ind + "=0;";
		reduceaggcode += "if(value.f" + ind + "===undefined) value.f" + ind + "=0;returnObject.f" + ind + "+=value.f" + ind + ";";
		defvalcode += "value.f" + ind + "=0;";
		finalizecode += "(returnObject.f" + ind + element.operator + JSON.stringify(element.value) + ")";
	}
	else if (element.f === "avg") {
		code += "value.f" + ind + "_1=(" + inlineconditions + ")?this." + element_field + ":0;";
		code += "value.f" + ind + "_2=(" + inlineconditions + ")?1:0;";
		reduceinitcode += "returnObject.f" + ind + "_1=0;";
		reduceaggcode += "if(value.f" + ind + "_1===undefined) value.f" + ind + "_1=0;returnObject.f" + ind + "_1+=value.f" + ind + "_1;";
		reduceinitcode += "returnObject.f" + ind + "_2=0;";
		reduceaggcode += "if(value.f" + ind + "_2===undefined) value.f" + ind + "_2=0;returnObject.f" + ind + "_2+=value.f" + ind + "_2;";
		defvalcode += "value.f" + ind + "_1=0;";
		defvalcode += "value.f" + ind + "_2=0;";
		finalizecode += "(1.0*value.f" + ind + "_1/value.f" + ind + "_2" + element.operator + JSON.stringify(element.value) + ")";
	}
	else throw "wrong operator " + element;

	code = defvalcode + code + "};";
	return {
		mapcode: code,
		reduceinitcode: reduceinitcode,
		reduceaggcode: reduceaggcode,
		finalizecode: finalizecode
	};
}

//purpose: build a javascript map function from a json query
//example: buildMapFunction(testJson)
//return: string contains compiled javascript code
//param: query=see testJson
function buildMapReduce(query, callback) {

	var mapfunccode = "";
	var reducecondcode = "";
	var reduceinitcode = "";
	var reduceaggcode = "";
	var finalizecode = "";

	//get all string need to convert to db
	var zipfield = [];
	var i = 0;
	while (i < query.length) {
		if (i % 2 == 0) {
			zipfield.push(query[i].field);
		}
		i++;
	}
	zipfield = zipfield.concat(['_isUser', '_segments', '_id', '_mtid', '_typeid']);

	i = 0;
	converter.toIDs(zipfield, function (ids) {
		while (i < query.length) {
			if (i % 2 == 0) {
				//ignore user type
				if (query[i].type == 'user') {
					i++;
					continue;
				}

				var tmpcode = buildChunk(i / 2, query[i], ids._typeid, ids[query[i].field]);
				mapfunccode += tmpcode.mapcode;
				reduceinitcode += tmpcode.reduceinitcode;
				reducecondcode += tmpcode.reducecondcode;
				reduceaggcode += tmpcode.reduceaggcode;
				finalizecode += tmpcode.finalizecode;
			}
			else {
				var joinop = query[i];
				if (joinop === 'and') joinop = '&&';
				else if (joinop === 'or') joinop = '||';
				else throw "wrong join operator: " + joinop;
				reducecondcode += joinop;
			}
			i++;
		}
		var mapinitcode = 'function(){var value={};var userid=-1;if(this["' + ids._isUser + '"]==true){userid=this["' + ids._mtid + '"];value._hasUser=true;}else{userid=this["' + ids._mtid + '"];';
		mapfunccode = mapinitcode + mapfunccode + "}emit(userid,value);}";
		var reducefunccode = "function(key,values){var returnObject={};" + reduceinitcode + "var hasUser=false;for(var i in values){var value=values[i];if(value._hasUser!==undefined)returnObject._hasUser=value._hasUser;" + reduceaggcode + "};return returnObject;}";
		finalizecode = 'function(key, value){return' + finalizecode + '&&value._hasUser?1:0}';
		if (callback !== undefined)
			callback({map: mapfunccode, reduce: reducefunccode, finalize: finalizecode});
	});
}

//function () {
//	var value = {};
//	var userid = -1;
//	if (this["_isUser"] == true) {
//		if (this["_segments"].indexOf(ObjectId("adc43c")) == -1)return;
//		userid = this["_id"];
//		value._hasUser = true;
//	} else {
//		if (this["_segments"].indexOf(ObjectId("adc43c")) == -1) return;
//		userid = this["_mtid"];
//		value.f0_1 = 0;
//		value.f0_2 = 0;
//		if (this["undefined"] === ObjectId("56e3a14a44ae6d70ddbf82a2")) {
//			value.f0_1 = (true) ? this.amount : 0;
//			value.f0_2 = (true) ? 1 : 0;
//		}
//		;
//		value.f1 = 0;
//		if (this["undefined"] === ObjectId("56e3a14a44ae6d70ddbf82a2")) {
//			value.f1 = (true) ? 1 : 0;
//		}
//		;
//	}
//	emit(userid, value);
//}

