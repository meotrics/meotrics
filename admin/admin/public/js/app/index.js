var urllistapp = '/api/listapp';
var urllistcount = 'api/countuser/';
var meotrics = new Vue({
    el: '#tabelapp',
        data: {
            listApp: []
        },
        created: function(){
            this.getListApp();
        },
        updated: function(){
            this.addCountUsertoList(0);
        },
        methods:{
            getListApp: function(){
                _self = this;
                this.$http.get(urllistapp).then((response) => {
                    // success callback
                        for(var i in response.body){
                            response.body[i]["countuser"] = 0;
                        }
                        _self.listApp = response.body;
            }, (response) => {
        // error callback
            })
            },
            addCountUsertoList : function(i){
                _self = this;
                var appid = _self.listApp[i].code;
                _self.getCountUserinMonth(appid,function(countuser){
                    _self.listApp[i]["countuser"] = countuser;
                    i++;
                    if(i < _self.listApp.length){
                        _self.addCountUsertoList(i);
                    }
                })

            },
            getCountUserinMonth: function(appid,callback){
                var now = new Date();
                var today = parseInt(now.getTime()/1000);
                var month = now.getMonth();
                var year = now.getYear();
                var firstMonth = parseInt(new Date(year.toString(),month.toString(),1)/1000);
                var url = urllistcount+appid+"/"+firstMonth+"/"+today;
                this.$http.get(url).then((response) => {
                    // success callback
                        callback(response.body);
                    }, (response) => {
                        callback(0);
                    // error callback
                })
            },
        }
})