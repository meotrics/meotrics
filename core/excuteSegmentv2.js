"use strict";

var converter = require('./utils/idmanager.js'),
    async = require('async'),
    mongodb = require('mongodb'),
    converter = new converter.IdManager();

function handleInput(object){
  var sucback;
  var errback;
  var p = new Promise(function (resolve, reject) {
    sucback = resolve;
    errback = reject;
  });

  var counti = 0;
  var countj = 0;
  for(let i=0;i<object.length;i+=2){
    counti++;
    if(object[i].type === 'user'){
      counti--;
      if(object[i].conditions != undefined){
        for(let j=0;j<object[i].conditions.length;j+=4){
          countj++;
          converter.toID(object[i].conditions[j])
            .then(function(r){
              object[i].conditions[j] = r;
              countj--;
              if(counti == 0 && countj == 0){
                sucback(object);
              }
            }).catch(function(e){
              errback(e);
            });
        }
      }else if(counti == 0){
        sucback(object);
      }
    }else{
      converter.toID(object[i].field)
        .then(function(r){
          counti--;
          object[i].field = r;
          if(object[i].conditions != undefined){
            for(let j=0;j<object[i].conditions.length;j+=4){
              countj++;
              converter.toID(object[i].conditions[j])
                .then(function(r){
                  object[i].conditions[j] = r;
                  countj--;
                  if(counti == 0 && countj == 0){
                    sucback(object);
                  }
                }).catch(function(e){
                  errback(e);
                });
            }
          }else if(counti == 0){
            sucback(object);
          }
        }).catch(function(e){
          errback(e);
        });
    }
  }
  return p;
}

function queryFilter(object){
  var sucback;
  var errback;
  var p = new Promise(function (resolve, reject) {
    sucback = resolve;
    errback = reject;
  });

  var length = object.length;
  var query = {};

  if(length > 1){
    query['$or'] = [];
    let c = 0;
    for(let i=0;i<length;i+=2){
      c++;
      conditionToQuery(object[i])
        .then(function(r){
          query['$or'].push(r);
          c--;
          if(c == 0){
            sucback(query);
          }
        }).catch(function(e){
          errback(e);
        });
    }
  }else{
    conditionToQuery(object[0])
      .then(function(r){
        sucback(r);
      }).catch(function(e){
        errback(e);
      });
  }

  return p;
}

function conditionToQuery(element){
  var sucback;
  var errback;
  var p = new Promise(function (resolve, reject) {
    sucback = resolve;
    errback = reject;
  });

  var query = {};
  if(element.conditions != undefined){
    var conditions = element.conditions;
    var size = conditions.length;

    if(size > 3){
      var hasOr = false;
      for(var i=3;i<size;i+=4){
        if(conditions[i] == 'or'){
          hasOr = true;
          break;
        }
      }
      if(hasOr){
        query['$or'] = [];
        for(var i=0;i<size;i+=4){
          if(conditions[i+3] == 'or'){
            query['$or'].push(translateOperator(conditions, i));
          }else{
            for(var j=i+7;j<size;j+=4){
              if(conditions[j] == 'or'){
                break;
              }
            }
            var andQuery = {'$and': []};
            for(i;i<j;i+=4){
              andQuery['$and'].push(translateOperator(conditions, i));
            }
            query['$or'].push(andQuery);
          }
        }
      }else{
        query['$and'] = [];
        for(var i=0;i<size;i+=4){
          query['$and'].push(translateOperator(conditions, i));
        }
      }

    }else{
      query = translateOperator(conditions, 0);
    }

    if(element.type == 'user'){
      converter.toID('_isUser')
        .then(function(r){
          var temp = {};
          temp[r] = true;
          query = {'$and': [temp, query]};
          sucback(query);
        }).catch(function(e){
          errback(e);
        });
    }else{
      converter.toID('_typeid')
        .then(function(r){
          var temp = {};
          temp[r] = new mongodb.ObjectID(element.type);
          query = {'$and': [temp, query]};
          sucback(query);
        }).catch(function(e){
          errback(e);
        });
    }
  }else{
    converter.toID('_typeid')
      .then(function(r){
        query[r] = new mongodb.ObjectID(element.type);
        sucback(query);
      });
  }
  return p;
}

function translateOperator(conditions, i){
  var query = {};
  switch(conditions[i+1]){
    case 'gt':query[conditions[i]] = {
                '$gt': conditions[i+2]
              };
              break;
    case 'lt':query[conditions[i]] = {
                '$lt': conditions[i+2]
              };
              break;
    case 'eq':query[conditions[i]] = {
                '$eq': conditions[i+2]
              };
              break;
    case 'ne':query[conditions[i]] = {
                '$ne': conditions[i+2]
              };
              break;
    case 'gte':query[conditions[i]] = {
                '$gte': conditions[i+2]
              };
              break;
    case 'lte':query[conditions[i]] = {
                '$lte': conditions[i+2]
              };
              break;
    case 'con':query[conditions[i]] = {
                '$regex': conditions[i+2]
              }
              break; 
    case 'ncon':query[conditions[i]] = {
                  '$regex': "^((?!" + conditions[i+2] + ").)*$/"
                }
                break;
    case 'sw':query[conditions[i]] = {
                '$regex': '^'+conditions[i+2]
              }
              break;
    case 'ew':query[conditions[i]] = {
                '$regex': conditions[i+2]+'$'
              }
              break;                                                                             
  }
  return query;
}

var testJson = [{
    type: "56e3a14a44ae6d70ddbf82a2", f: "avg", field: "amount", operator: ">", value: 5,
    conditions: ["amount", "gt", 2, "and", "price", "eq", 40]
  },
  "and", 
  {
    type: "56e3a14a44ae6d70ddbf82a2", f: "count", field: "pid", operator: ">", value: 5,
    conditions: ["amount", "gt", 5, "and", "price", "eq", "30"]
  },
  "and", 
  {
    type: 'user',
    conditions: ["gender", "eq", "male"]
  }];

handleInput(testJson)
  .then(function(r){
    console.log(r);
    return queryFilter(r);
  }).then(function(r){
    console.log(JSON.stringify(r));
  });

//purpose: build javascript code based on json condition
//example: buildConditionsCode(["amount", ">", 5, "and", "price", "=", "30"])
//return: string contains compiled javascript code
//param: array of conditions, [field, operator, value, joinoperator, field, operator, value, ...]
function buildConditionsCode(conditions) {
  var i = 0;
  var code = "";
  while (i < conditions.length) {
    //get the next operator (if exist)
    var nextop = i + 3 < conditions.length ? conditions[i + 3] : "";
		if(nextop == 'and') nextop = '&&';
		else if(nextop =='or') nextop = '||';
		else if(nextop !== '') throw "wrong joining operation: " + nextop;
		
		var operator = conditions[i + 1];
    //append new compiled js code
		if (['>', '<', '>=', '<=', '!==', '=='].indexOf(operator) !== -1)
			code += "(this." + conditions[i] + operator + JSON.stringify(conditions[i + 2]) + ")" + nextop;
		else if([ 'startsWith', 'endsWith',].indexOf(operator)!== -1)
		{
			code += "(this." + conditions[i] + '.' + operator +'(' + JSON.stringify(conditions[i + 2]) + ")==true)" + nextop;
			
		} else if(operator == 'contains')
		{
			code += "(this." + conditions[i] + '.indexOf(' + JSON.stringify(conditions[i + 2]) + ")!== -1)" + nextop;
		}
		else if(operator == '!contains')
		{
			code += "(this." + conditions[i] + '.indexOf(' + JSON.stringify(conditions[i + 2]) + ")=== -1)" + nextop;
		}
		else if(operator == "!startsWith")
		{
			code += "(!this." + conditions[i] + '.startsWith(' + JSON.stringify(conditions[i + 2]) + "))" + nextop;
		}
		else if(operator == "!endsWith")
		{
			code += "(!this." + conditions[i] + '.endsWith(' + JSON.stringify(conditions[i + 2]) + "))" + nextop;
		}
		else if(operator == 'isset')
		{
			code += "(!this." + conditions[i] + ' !==undefined)' + nextop;
		}
		else if(operator == '!isset')
		{
			code += "(!this." + conditions[i] + ' ===undefined)' + nextop;
		}
		else{
			throw 'not supported operator: ' + operator;
		}
		i += 4;
  }
  return code;
}

function buildReduceChunk(ind, condition) {


}

//purpose: build a piece of map function
//example: buildMapChunk({actiontype: "pageview", conditions: [{f: "count", field: "pid", operator: ">", value: 5, conditions: ["amount", ">", 5, "and", "price", "=", "dd"]})
//return: string contains compiled javascript code
//param: i=index of condition, for iteration purpose, condition=see example
function buildChunk(ind, element) {
  var reducecondcode = "";
  var reduceinitcode = "";
  var reduceaggcode = ""
  var code = "if(this.actiontype==='" + element.type + "'){";
  var conditions = element.conditions;
  var defvalcode = "";
  var aggcode = "";
  //get the condition code
  if (element.conditions === undefined) console.log(element);
  var inlineconditions = buildConditionsCode(element.conditions);

  //if the query is to count then just map 1, but if the query is to sum
  // and calculate average then have to map field's value
  if (element.f === "count") {
    code += "value.f" + ind + "=(" + inlineconditions + ")?1:0;";
    reduceinitcode += "returnObject.f" + ind + "=0;";
    reduceaggcode += "returnObject.f" + ind + "+=value.f" + ind + ";";
    defvalcode += "value.f" + ind + "=0;";
    reducecondcode += "(value.f" + ind + element.operator + JSON.stringify(element.value) + ")";
  }
  else if (element.f === "sum") {
    code += "value.f" + ind + "=(" + inlineconditions + ")?this." + element.field + ":0;";
    reduceinitcode += "returnObject.f" + ind + "=0;";
    reduceaggcode += "returnObject.f" + ind + "+=value.f" + ind + ";";
    defvalcode += "value.f" + ind + "=0;";
    reducecondcode += "(value.f" + ind + element.operator + JSON.stringify(element.value) + ")";
  }
  else if (element.f === "avg") {
    code += "value.f" + ind + "_1=(" + inlineconditions + ")?this." + element.field + ":0;";
    code += "value.f" + ind + "_2=(" + inlineconditions + ")?1:0;";
    reduceinitcode += "returnObject.f" + ind + "_1=0;";
    reduceaggcode += "returnObject.f" + ind + "_1+=value.f" + ind + "_1;";
    reduceinitcode += "returnObject.f" + ind + "_2=0;";
    reduceaggcode += "returnObject.f" + ind + "_2+=value.f" + ind + "_2;";
    defvalcode += "value.f" + ind + "_1=0;";
    defvalcode += "value.f" + ind + "_2=0;";
    reducecondcode += "(1.0*value.f" + ind + "_1/value.f" + ind + "_2" + element.operator + JSON.stringify(element.value) + ")";
  }
  else throw "wrong operator";

  code = defvalcode + code + "};";
  return {
    mapcode: code,
    reduceinitcode: reduceinitcode,
    reducecondcode: reducecondcode,
    reduceaggcode: reduceaggcode
  };
}

//purpose: build a javascript map function from a json query
//example: buildMapFunction(testJson)
//return: string contains compiled javascript code
//param: query=see testJson
function buildMapReduce(query) {
  var mapfunccode = "function(){var value={};";
  var reducecondcode = "";
  var reduceinitcode = "";
  var reduceaggcode = "";


  var i = 0;
  while (i < query.length) {
    if (i % 2 == 0) {
      var tmpcode = buildChunk(i / 2, query[i]);
      mapfunccode += tmpcode.mapcode;
      reduceinitcode += tmpcode.reduceinitcode;
      reducecondcode += tmpcode.reducecondcode;
      reduceaggcode += tmpcode.reduceaggcode;
    }
    else {
			var joinop = query[i];
			if(joinop === 'and') joinop = '&&';
			else if(joinop === 'or') joinop = '||';
			else throw "wrong join operator: " + joinop;
      reducecondcode += joinop;
    }
    i++;
  }
  mapfunccode += "}";

  var reducefunccode = "function(key,values){var returnObject={};" + reduceinitcode + "for(var i in values){var value=values[i];" + reduceaggcode + "};return " + reducecondcode + "?key:null;}";
  return { map: mapfunccode, reduce: reducefunccode };
}
