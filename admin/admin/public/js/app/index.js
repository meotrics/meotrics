var urllistapp = '/api/listapp';
var meotrics = new Vue({
    el: '#tabelapp',
        data: {
            listApp: []
        },
        created: function(){
            this.getListApp();
        },
        methods:{
            getListApp: function(){
                var _self = this;
                this.$http.get(urllistapp).then((response) => {
                    // success callback
                _self.listApp = response.body;
            }, (response) => {
        // error callback
        })
        }
}
})