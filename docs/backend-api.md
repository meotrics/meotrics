
## Segmentation
1. List segmentations of an `$appid`

  ```php
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

1. List properties
  1. How to call
    
    ```
    $props = MtHttp::get('prop/' . $appid);
    ```

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
1. List action type
  1. URL
  
    ```javascript
    GET /actiontype/$appid
    ```
  2. Output
    
    ```js
    [type1, type1]
    ```
2. Delete a action type
  1. URL

    ```php
    MtHttp::delete('actiontype/' . $appid . '/' .$actiontypeid);
    ```
//get a action type
MtHttp::get('actiontype/' . $appid . '/' .$actiontypeid);
//update action type
MtHttp::put('actiontype/' . $appid . '/' .$actiontypeid);


//SEGMENT---------------------------------

type:

{ 
    "_id" : ObjectId("5703e1ac00f3fd78159e64f0"), 
    "name" : "Active user", 
    "condition" : [
        {
            "type" : "pageview", 
            "f" : "count", 
            "field" : "", 
            "operator" : ">", 
            "value" : 5, 
            "conditions" : [
                "url", 
                "eq", 
                "http://google.com"
            ]
        }, 
        "and", 
        {
            "type" : "purchase", 
            "f" : "avg", 
            "field" : "price", 
            "operator" : ">", 
            "value" : 5, 
            "conditions" : [
                "amount", 
                "gt", 
                500, 
                "and", 
                "quantity", 
                "gt", 
                5
            ]
        }, 
        "and", 
        {
            "type" : "user", 
            "conditions" : [
                "age", 
                "eq", 
                15
            ]
        }
    ], 
    "_appid" : 1, 
    "_ctime" : 1459872173
}

//create
post /segment/$appid
//list all segment
get /segment/$appid
//get segment
get /segment/$appid/$id
//update segment
put /segment/$appid/$id
//delete segment
delete /segment/$appid/$id

//CAMPAIGN----------------------------
//create
post /campaign/$appid
//list all campaign in app
get /campaign/$appid'
//get a campaign
get /campaign/$appid/$id
//edit
put /campaign/$appid/$id 
//delete
delete /campaign/$appid/$id 

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

