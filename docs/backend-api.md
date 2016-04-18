
## Segmentation
1. Type definition
  ```json
  { 
    "_id" : ObjectId("5703e1ac00f3fd78159e64f0"), 
    "name" : "Active user", 
    "condition" : [
        {
            "type" : "pageview",  "f" : "count",  "field" : "", "operator" : ">", "value" : 5, 
            "conditions" : [
                "url", "eq",  "http://google.com"
            ]
        }, 
        "and", 
        {
            "type" : "purchase",  "f" : "avg", "field" : "price",  "operator" : ">",  "value" : 5, 
            "conditions" : [
                "amount", "gt", 500, "and", "quantity", "gt", 5
            ]
        }, 
        "and", 
        {
            "type" : "user", 
            "conditions" : [
                "age",  "eq",  15
            ]
        }
    ], 
    "_appid" : 1, 
    "_ctime" : 1459872173
  }
  ```
  
2. Supported actions

|Action|Method|URL|Param Json|Output|
|--------|--------|-----|------------|--------|
| Create a segment|POST| http://localhost:2108/segment/:appid |An action type|number, id of created segmentation|
| List all segmentation in app|GET| http://localhost:2108/segment/:appid |-| JSON array of segmentations |
| Get a segmentation|GET| http://localhost:2108/segment/:appid/:segmentid |-|an segmentation|
| Update a segmentation|PUT| http://localhost:2108/segment/:appid/:segmentid |-|-|
| Delete a segmentation|DELETE| http://localhost:2108/segment/:appid/:segmentid |-|-|
| Excute an segmentation | GET | http://locaolhost:2108/segment/query/:appid/:segmentid|-|TODO|

3. Example

  ```php
  // List segmentations of an `$appid`
  $segments = MtHttp::get('segment/' . $appid);
  ```

## Properties
1. Type definition

  ```json
  {
    name : "age",
    dpname: "Age"
  }
  ```

2. Supported actions

|Action|Method|URL|Param Json|Output|
|------|-----|---|----|----|
|Create a prop|POST| http://localhost:2108/prop/:appid |An action type|number, id of created prop|
|List all prop in app|GET| http://localhost:2108/prop/:appid |-| Json array of properties |
|Get a property|GET| http://localhost:2108/prop/:appid/:propid |-|an property|
|Update a property|PUT| http://localhost:2108/prop/:appid/:propidid |-|-|
|Delete a property|DELETE| http://localhost:2108/prop/:appid/:propid |-|-|


## Action Type
1. Type definition

  ```json
  {
      "_id": "56fa9b1bc23d0a0c0b1b7bea",
      "codename": "click",
      "name": "Click",
      "desc": "User click on a object",
      "fields": [
         {
            "pname": "Object ID",
            "pcode": "oid"
         },
         {
            "pname": "Object Type",
            "pcode": "type"
         }
      ]
   }
  ```
  
2. Supported actions

|Action|Method|URL|Param Json|Output|
|---|---|---|---|---|
|Create an action type|POST|http://localhost:2108/actiontype/:appid |An action type|number, id of created action type|
|List all action type in app|GET|http://localhost:2108/actiontype/:appid |-| Array of Action type |
|Get an action type|GET|http://localhost:2108/actiontype/:appid/:actiontypeid |-|an action type|
|Update an action type|PUT|http://localhost:2108/actiontype/:appid/:actiontypeid |-|-|
|Delete an action type|DELETE|http://localhost:2108/actiontype/:appid/:actiontypeid |-|-|



//TREND

{
  typeid: "purchase",
  opration: "sum"
  object: "pid", //code of properties
  param: "price"
  order: 1 // small to large
}
//create a trend
post /trend/$appid
//list all trend in app
get /trend/$appid
//get a trend
get /trend/$appid/$id
//update
put /trend/$appid/$id
//delete
delete('/trend/$appid/$id
// Query trend
get /trend/query/$appid/$id
output:
[  
   {  
      "_id":473,
      "result":12,
      "temp":{  
         "_id":"5703d6205c6d1624129dfea2",
         "_typeid":"purchase",
         "_ctime":1459869216,
         "_mtid":"5703d6165c6d1624129dcf61",
         "_segments":[  

         ],
         "cid":3,
         "pid":473,
         "quantity":3,
         "amount":18,
         "price":12,
         "paymentype":1
      }
   },
   {  
      "_id":250,
      "result":13,
      "temp":{  
         "_id":"5703d6205c6d1624129dfaa6",
         "_typeid":"purchase",
         "_ctime":1459869216,
         "_mtid":"5703d6165c6d1624129dd205",
         "_segments":[  

         ],
         "cid":0,
         "pid":250,
         "quantity":5,
         "amount":11,
         "price":13,
         "paymentype":2
      }
   },
   {  
      "_id":834,
      "result":17,
      "temp":{  
         "_id":"5703d6215c6d1624129e0abe",
         "_typeid":"purchase",
         "_ctime":1459869217,
         "_mtid":"5703d6165c6d1624129dd0db",
         "_segments":[  

         ],
         "cid":4,
         "pid":834,
         "quantity":9,
         "amount":7,
         "price":17,
         "paymentype":1
      }
   }, ...
]
