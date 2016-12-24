var funnelApp = new Vue({
    el: '#managerfunnel',
    data:
    {
        data_segment:[],
        list_choice:[]
    },
    methods: {
        addSegment: function(){
            var _id = $('#selectsegment').val();
            var segment = this.getSegment(_id);
            if(this.checklist(_id))
                this.list_choice.push(segment);
            this.getChartSegment();
        },
        getSegment: function(_id){
            var _self = this;
            for(var i in _self.data_segment)
                if(_self.data_segment[i]._id == _id)
                    return _self.data_segment[i];
            return 0;
        },
        checklist: function(_id){
            for(var i in this.list_choice)
                if(this.list_choice[i]._id == _id)
                    return false;
            return true;
        },
        removeSegment: function(item){
            var index = this.list_choice.indexOf(item);
            this.list_choice.splice(index,1);
            this.getChartSegment();
        },
        getChartSegment: function(){
            var _self = this;
            //var ctx = document.getElementById("myChart");
            $("#canvas-chart").html('');
            $("#canvas-chart").append('<canvas id="segment_chart" width="400" height="100"></canvas>');

            var ctx = $("#segment_chart").get(0).getContext("2d");
            var options = {
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        stacked: true,
                        barPercentage: 0.4
                    }]
                },
                legend: {
                    display: false
                },
                stacked: true,
                gridLines: {
                    display: false
                }
            };
            var data_chart = {};
            data_chart.datasets = [];
            data_chart.labels = [];
            var data = [];
            $.each(_self.list_choice,function(index,item){
                data_chart.labels.push(item.name);
                data.push(item.count);
            });
            var obj = {
                data: data,
                backgroundColor: "#5884FF",
                hoverBackgroundColor:  "#5884FF"
            };
            data_chart.datasets.push(obj);
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: data_chart,
                options: options
            });
        }
    }
})