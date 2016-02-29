var testJson = [{
  type: "purchase", f: "avg", field: "amount", operator: ">", value: 5,
  conditions:  ["amount", ">", 2, "and", "price", "=", 40]
},
"and", {
  type: "pageview", f: "count", field: "pid",  operator: ">",  value: 5,
  conditions: ["amount", ">", 5, "and", "price", "=", "30"]
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

function buildReduceChunk(ind, condition)
{


}

//purpose: build a piece of map function
//example: buildMapChunk({actiontype: "pageview", conditions: [{f: "count", field: "pid", operator: ">", value: 5, conditions: ["amount", ">", 5, "and", "price", "=", "dd"]})
//return: string contains compiled javascript code
//param: i=index of condition, for iteration purpose, condition=see example
function buildChunk(ind, element){
  var reducecondcode = "";
  var reduceinitcode = "";
  var reduceaggcode = ""
  var code="if(this.actiontype==='" + element.type + "'){";
  var conditions = element.conditions;
  var defvalcode = "";
  var aggcode = "";
  //get the condition code
  if(element.conditions === undefined) console.log(element);
  var inlineconditions = buildConditionsCode(element.conditions);

  //if the query is to count then just map 1, but if the query is to sum
  // and calculate average then have to map field's value
  if(element.f === "count" )
  {
    code += "value.f" + ind + "=(" + inlineconditions + ")?1:0;";
    reduceinitcode += "returnObject.f"+ ind +"=0;";
    reduceaggcode += "returnObject.f" + ind + "+=value.f" + ind + ";";
    defvalcode += "value.f"+ ind + "=0;";
    reducecondcode += "(value.f" + ind + element.operator + JSON.stringify(element.value)+")";
  }
  else if(element.f === "sum"){
    code += "value.f" + ind + "=(" + inlineconditions + ")?this." + element.field + ":0;";
    reduceinitcode += "returnObject.f"+ ind +"=0;";
    reduceaggcode += "returnObject.f" + ind + "+=value.f" + ind + ";";
    defvalcode += "value.f"+ ind +"=0;";
    reducecondcode += "(value.f" + ind + element.operator + JSON.stringify(element.value)+")";
  }
  else if(element.f === "avg")
  {
    code += "value.f" + ind + "_1=(" + inlineconditions + ")?this." + element.field + ":0;";
    code += "value.f" + ind + "_2=(" + inlineconditions + ")?1:0;";
    reduceinitcode += "returnObject.f"+ ind +"_1=0;";
    reduceaggcode += "returnObject.f" + ind + "_1+=value.f" + ind + "_1;";
    reduceinitcode += "returnObject.f"+ ind +"_2=0;";
    reduceaggcode += "returnObject.f" + ind + "_2+=value.f" + ind + "_2;";
    defvalcode += "value.f"+ ind +"_1=0;";
    defvalcode += "value.f"+ ind +"_2=0;";
    reducecondcode += "(1.0*value.f" + ind + "_1/value.f" + ind +"_2" + element.operator + JSON.stringify(element.value)+")";
  }
  else throw "wrong operator";

  code = defvalcode + code + "};";
  return {
    mapcode: code,
    reduceinitcode:reduceinitcode,
    reducecondcode:reducecondcode,
    reduceaggcode:reduceaggcode
  };
}

//purpose: build a javascript map functino from a json query
//example: buildMapFunction(testJson)
//return: string contains compiled javascript code
//param: query=see testJson
function buildMapReduce(query){
  var mapfunccode="function(){var value={};";
  var reducecondcode = "";
  var reduceinitcode = "";
  var reduceaggcode = "";


  var i = 0;
  while (i < query.length) {
    if(i%2==0)
    {
      console.log(i/2)
      var tmpcode = buildChunk(i/2, query[i]);
      mapfunccode += tmpcode.mapcode;
      reduceinitcode += tmpcode.reduceinitcode;
      reducecondcode += tmpcode.reducecondcode;
      reduceaggcode += tmpcode.reduceaggcode;
      console.log(reduceaggcode);
    }
    else{
      reducecondcode += query[i];
    }
    i++;
  }
  mapfunccode += "}";

  var reducefunccode = "function(key,values){var returnObject={};" + reduceinitcode  +"for(var i in values){var value=values[i];"+reduceaggcode+"};return "+reducecondcode+"?key:null;}";
  return {map:mapfunccode, reduce:reducefunccode };
}
