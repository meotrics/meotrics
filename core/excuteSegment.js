
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
    conditions: ["amount", ">", 5, "and", "price", "=", "dd"],
  }]
}];

if(f == "count"), addField("eifi")
if(f == "sum"), addField("eifi")
if(f== "avg"), addFiled("eifi_1, eifi_2")

buildCondition
this. + field + opera  && condition: 1 : 0

//test: buildConditionsCode("amount", ">", 5, "and", "price", "=", "dd"])
function buildConditionsCode(conditions){
  var i =0;
  var n = conditions.length
  var code = "";

  while (i < n) {
    var nextop = i+3 < n ? conditions[i+3] : "" ;
    code += "(this." + conditions[i] + conditions[i+1] + JSON.stringify(conditions[i+2]) +")" + nextop;
    i+=4;
  }
  return code;
}

//test buildBigCondition({  f: "count",  field: "pid",  operator: ">",  value: 5,conditions: ["amount", ">", 5, "and", "price", "=", "dd"],})
function buildBigCondition(i,condition)
{
  var inlineconditions = buildConditionsCode(condition.conditions);
  var code = "";
  if(condition.f === "count" )
  code = "value.f" + i + "=(" + inlineconditions+ ")?1:0;";
  else if(condition.f === "sum" || condition.f === "avg")
  code = "value.f" + i + "=(" + inlineconditions+")?this." + condition.field +":0;";
  //else if(condition.f === "avg"){
  //  code += "if("+inlineconditions+"){value.f" + i +"_1=1;";
  //  code+="value.f" + i + "_2=this." + condition.field +";}";
  //  code+= "else{value.f"+i+"_1=value.f"+i+"_2=0};";
  //}
  return code;
}

function buildMapChunk(ind, condition){
    var code="if(this.actiontype==='"+condition.actiontype+"'){";
    var i = 0;
    var conditions = condition.conditions;
    var n = conditions.length;
    while (i < n) {
      code += buildBigCondition(ind + "_" + (i/2) ,conditions[i]);
      i+=2;
    }
    code+="}";
    return {code;
}

function buildMapFunction(query){
  var code="function(){var value={}";
}

function toMapReduce( queryobject, result)
{
  if(queryobject.length !== 1)
  {
    toMapReduce(queryobject.slice(0,-2), result);
  }

  var operator = queryobject[queryobject.length - 2];
  var query = queryobject[queryobject.length - 1];

  json =


}
function toMapReduce(queryobject)
{
  var N = queryobject.length;
  var ei = 0;
  while (ei < N) {

  }
}
