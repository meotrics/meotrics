@extends('layout.master')
@section('title', 'Meotrics')
@section('style')

@endsection
@section('script')
<script src="{{asset('/js/Chart.js')}}"></script>
<script>
var options = {
    legend: {display:false},
    segmentShowStroke: false,
    animateRotate: false,
    animation : false,
    percentageInnerCutout: 50,
    scaleShowLabels: false,
    tooltipTemplate: "<%= svalue %>%",
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].lineColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
}

var trChart = new Chart($("#trchart"), {
    type: 'line',
    data: {
    labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    datasets: [
        {
            label: "My First dataset",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [65, 59, 80, 81, 56, 55, 40],
        }
    ]
},
    options: {
        xAxes: [{
            display: false
        }]
    }
});

var trChart = new Chart($("#trchartpc"), {
    type: 'line',
    data: {
    labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    datasets: [
        {
            label: "My First dataset",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [65, 59, 80, 81, 56, 55, 40],
        }
    ]
},
    options: {
        xAxes: [{
            display: false
        }]
    }
});

var visitChart = new Chart($("#visitchart"), {
    type: 'doughnut',
    data: {
    labels: [
        "<span class='dot'></span> New visitor",
       "<span class='dot'></span> Returning visitor",
    ],
    datasets: [
        {
            data: [300, 50, 100],
            backgroundColor: [
                "#FF6384",
                "#36A2EB",
                "#FFCE56"
            ],
            hoverBackgroundColor: [
                "#FF6384",
                "#36A2EB",
                "#FFCE56"
            ]
        }]}, options: options
    
});

$('#visitchartlegend').html( visitChart.generateLegend());
</script>
@endsection

@section('content')
	<!--THIS IS HOME {{cookie('laravel_session')}}-->
    <div class="row">
        <div class="card col-md-12">
            <div class="row">
                <div class="content col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Number of visitors</p>
                                <h3>430</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Today <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Number of new visitors</p>
                                <h3>23</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Today <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>User Growth rate</p>
                                <h3>5%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">Today <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Retention rate</p>
                                <h3>2.5%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">This week <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Purchase Conversion rate</p>
                                <h3>0.1%</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer color-red">Low</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Total revenue</p>
                                <h3>2383<i class="fa fa-dollar font-size-20"></i></h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">This week <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Revenue per customer</p>
                                <h3>15.3<i class="fa fa-dollar font-size-20"></i></h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer">This week <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Highest revenue campaign</p>
                                <h3>Facebook</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p>Most effective Referrer</p>
                                <h3>Social</h3>
                            </div>
        <!--                    <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>-->
                            <p class="small-box-footer"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="header">
                                Visits
                                <p class="category pull-right">TODAY</p>    
                            </div>                        
                            <div class="content">
                              <div class="row">
                                  <div class="col-sm-6">
                                      430 <br/>
                                      23
                                  </div>
                                  <div class="col-sm-6">
                                      <canvas id="visitchart" width="400" height="400" ></canvas>

                                      <div id="visitchartlegend" class="chart-legend"></div>
                                  </div>
                              </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="header">
                                <div class="row">
                                    <div class="col-sm-6">
                                        This week
                                    </div>
                                    <div class="col-sm-6">
                                        This week
                                    </div>
                                </div>
                            </div>                        
                            <div class="content">
                                 <div class="row">
                                    <div class="col-sm-6">
                                        <canvas id="trchart" width="400" height="400"> </canvas>
                                        Total revenue
                                        $ 2 238
                                    </div>
                                    <div class="col-sm-6">
                                        <canvas id="trchartpc" width="400" height="400"> </canvas>
                                        Revenue per customer
                                        $ 13.5
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
@endsection


@section('additional')
  @include('partials/install_guide')
@endsection
