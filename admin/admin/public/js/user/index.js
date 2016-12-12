var test = new Vue({
   el: '#tabeluser',
    data: {
        listUser: []
    },
    created: function(){
        this.getListUser();
    },
    methods:{
        getListUser: function(){
            var _self = this;
            this.$http.get('/api/listuser').then((response) => {
                // success callback
                var listrs = response.body;
                var list = [];
                for(var i in listrs){
                    var item = listrs[i];
                    item["listemail"] = [];
                    item["render"] = false;
                    list.push(item);
                }
                _self.listUser = list;
            }, (response) => {
                // error callback
            })
        },
        showApp: function(email,index){
            var _self = this;
            if( _self.listUser[index]['listemail'].length ==0){
                this.$http.get('/api/listappbyemail/'+email).then((response)=>{
                    // success callback
                    _self.listUser[index]['listemail'] = response.body;
                },(response)=>{
                    // error callback
                })
            }
            console.log(_self.listUser[index]['render']);
            if(_self.listUser[index]['render']){
                _self.listUser[index]['render'] = false;
            }else{
                _self.listUser[index]['render'] = true;
            }
        }
    }
});
