/**
 * Created by vietle on 11/22/16.
 */
declare function require(name:string);
var ObjectID = require('bson-objectid');
export class trackOffline {
    keyUser = ['phone'];
    appid : string;
    actionmgr : any;
    constructor(appid,actionmgr){
        this.appid = appid;
        this.actionmgr = actionmgr;
    }
    public purchaseOffline(req) {
        let data = this.getInfo(req);
        data._ip = '127.0.0.1';
        data._typeid = 'purchase';
        if(req.params['_ctime'] != undefined)
            data._ctime = req.params['_ctime'];
        var _self = this;
        this.checkKey(data,function(mtid){
            if(mtid != 0 && mtid.length == 24){
                data._mtid = mtid;
                _self.insertHaveMtid(data);
            }
            // else{
            //     mtid = ObjectID();
            //     data._mtid = mtid;
            //     _self.actionmgr.setupRaw(_self.appid, mtid, function (id) {
            //         _self.actionmgr.identifyRaw(_self.appid, {mtid: mtid, user: data}, function (id) {
            //             _self.insertHaveMtid(data);
            //         });
            //         console.log("create new user");
            //     });
            // }
        })
    }

    public newUser(data : any, mtid : any) : any{
        this.actionmgr.setupRaw(this.appid, mtid, function (id) {
            data._mtid = mtid;
            return data;
        });
    }


    public getInfo(request) : any {
        let res = {};
        for (var i in request.params)
            if (i.startsWith('_') === false)
                res[i] = isNaN(request.params[i]) ? request.params[i] : parseFloat(request.params[i]);
        return res;
    }

    public checkKey(req,callback){
        // return mtid
        var _self = this;
        for (let key in this.keyUser) {
            (function(key){
                var value = _self.keyUser[key];
                // console.log(value);
                // console.log(req[value]);
                // console.log(req);
                if(req[value] != undefined){
                    var data = {};
                    data[value] = req[value];
                    // console.log(data);
                    _self.actionmgr.findUserByKey(_self.appid,data,function(mtid){
                        if(mtid != 0 && mtid != undefined){
                            callback(mtid);
                        }else{
                            if(key == _self.keyUser.length -1){
                                callback(0);
                            }
                        }
                    });
                }
            })(key);
        }
    }

    public insertHaveMtid(req) {
        this.actionmgr.saveRaw(this.appid, req,function(callback){})
    }

}
