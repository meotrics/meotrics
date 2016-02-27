
var testJson = [{
  actiontype: "purchase",
  conditions: [{
    f: "sum", field: "amount", operator: ">", value: 5,
    conditions: ["amount", ">", 2, "and", "price", "=", 40],
  },
  "and",
  { f: "count", field: "cid", operator: ">", value: 4,
  conditions: ["amount", "<", 10] }]
},
"and",
{
  actiontype: "pageview",
  conditions: [{
    f: "count",  field: "pid",  operator: ">",  value: 5,
    conditions: ["amount", ">", 5, "and", "price", "=", "30"],
  }]
}];

//purpose: build javascript code based on json condition
//example: buildConditionsCode(["amount", ">", 5, "and", "price", "=", "30"])
//return: string contains compiled javascript code
//param: array of conditions, [field, operator, value, joinoperator, field, operator, value, ...]
function buildConditionsCode(conditions){
  var i = 0;
  var code = "";

  while (i < conditions.length) {
    //get the next operator (if exist)
    var nextop = i + 3 < conditions.length ? conditions[i+3] : "";

    //append new compiled js code
    code += "(this." + conditions[i] + conditions[i+1] + JSON.stringify(conditions[i+2]) +")" + nextop;
    i+=4;
  }
  return code;
}

//purpose: build big condition to javascript code
//example: buildBigCondition({  f: "count",  field: "pid",  operator: ">", value: 5,conditions: ["amount", ">", 5, "and", "price", "=", "dd"],})
//return: string contains compiled javascript code
//param: i=index of condition, for iteration purpose, condition=see example
function buildBigCondition(i, condition)
{
  //get the condition code
  var inlineconditions = buildConditionsCode(condition.conditions);

  var code = "";
  //if the query is to count then just map 1, but if the query is to sum
  // and calculate average then have to map field's value
  if(condition.f === "count" )
    code = "value.f" + i + "=(" + inlineconditions + ")?1:0;";
  else if(condition.f === "sum" || condition.f === "avg")
    code = "value.f" + i + "=(" + inlineconditions + ")?this." + condition.field + ":0;";
  else throw "wrong operator";

  return code;
}

//purpose: build a piece of map function
//example: buildMapChunk({actiontype: "pageview", conditions: [{f: "count", field: "pid", operator: ">", value: 5, conditions: ["amount", ">", 5, "and", "price", "=", "dd"]})
//return: string contains compiled javascript code
//param: i=index of condition, for iteration purpose, condition=see example
function buildMapChunk(ind, condition){
  var code="if(this.actiontype==='" + condition.actiontype + "'){";
  var i = 0;
  var conditions = condition.conditions;
  var defvalcode = "";

  //just join small chunk into a big one
  while (i < conditions.length) {
    code += buildBigCondition( ind + "_" + (i/2) ,conditions[i]);
    defvalcode += "value['f"+ ind + "_" + (i/2) +"']=0;";
    i+=2;
  }
  code += "}else{" + defvalcode + "}";
  return code;
}

//purpose: build a javascript map functino from a json query
//example: buildMapFunction(testJson)
//return: string contains compiled javascript code
//param: query=see testJson
function buildMapFunction(query){
  var code="function(){var value={};";
  var i = 0;
  while (i < query.length) {
    code += buildMapChunk(i/2, query[i]);
    i+=2;
  }
  code += "}";
  return code;
}

function main(){
  console.log(buildMapFunction(testJson));
}


function(){
   Array.sum(f1,f2,..fn);
   for(i in condition)
   buildCondition()
}
