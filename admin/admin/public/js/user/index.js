var test = new Vue({
   el: '#tabeluser',
    data: {
        listUser: [],
        ban: {
            email: '',
            index: 0,
            status: 1,
            message: ''
        },
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
            if(_self.listUser[index]['render']){
                _self.listUser[index]['render'] = false;
            }else{
                _self.listUser[index]['render'] = true;
            }
        },
        changeban: function(email,index,status){
            this.ban["email"] = email;
            this.ban["index"] = index;
            this.ban["status"] = status;
            this.ban["message"] = "";
            $('#myModal').modal('show');
        },
        saveban: function(){
            _self = this;
            if(this.ban.email != undefined){
                if(this.ban["status"])
                    this.ban["status"]= 0;
                else
                    this.ban["status"] =1;
                this.$http.get('/api/banuser/'+this.ban["email"]+'/'+this.ban["status"]).then((response)=>{
                    // success callback
                    _self.listUser[this.ban["index"]]['banned'] = _self.ban["status"];
                    this.$http.get('/api/banreason/'+this.ban["email"]+'/'+this.ban["status"]+'/'+this.ban["message"]).then((response)=>{},(response)=>{})
                },(response)=>{})
            }
            $('#myModal').modal('hide');
        }

    }
});
