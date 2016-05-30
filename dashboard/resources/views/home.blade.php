@extends('layout.master')
@section('title', 'Meotrics')
@section('style')
@endsection
@section('script')
<script src="{{asset('/js/Chart.js')}}"></script>
<script>
   $(document).ready(function() {
   var progressbar = $('#progress_bar');
   max = progressbar.attr('max');
   time = (1000 / max) * 5;
   value = progressbar.val();

   var loading = function() {
   value += 1;
   addValue = progressbar.val(value);

   $('.progress-value').html(value + '%');
   var $ppc = $('.progress-pie-chart'),
   deg = 360 * value / 100;
   if (value > 50) {
   $ppc.addClass('gt-50');
   }

   $('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
   $('.ppc-percents span').html(value + '%');

   if (value == max) {
   clearInterval(animate);
   }
   };

   var animate = setInterval(function() {
   loading();
   }, time);
   });

   var options = {
       legend: {display:false},
       animation : false,
       segmentShowStroke: false,
       animateRotate: false,
        maintainAspectRatio: false,
    responsive: true,
       percentageInnerCutout: 50,
       scaleShowLabels: false,
       //tooltipTemplate: {{"<%= value %> %"}},
   };

   var retenratechart = new Chart($("#retenratechart"), {
       type: 'line',
       data: {
       labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
       datasets: [
           {
               label: "My First dataset",
               fill: false,
               lineTension: 0.5,

               borderCapStyle: 'round',

               borderDash: [],
               borderDashOffset: 0.0,
               borderJoinStyle: 'miter',

               pointBorderWidth: 2,
               pointHoverRadius: 1,
               borderWidth: 2,
               pointHoverBackgroundColor: "rgba(75,192,192,1)",
               pointHoverBorderColor: "rgba(220,220,220,1)",
               pointHoverBorderWidth: 2,
               pointRadius: 2,
               pointHitRadius: 10,
                fill: true,
                borderColor: "#2F5CB0",
                backgroundColor: "rgba(78,108,201,0.3)",
                pointBorderColor: "#2F5CB0",
                pointBackgroundColor: "white",
                pointBorderWidth: 1,

               data: [65, 59, 80, 81, 32, 12, 40],
           }
       ]
   },
       options: {

           legend: {display:false},
           maintainAspectRatio: false,
    responsive: true,
    height: "240",
       animation : false,
       scales: {
          yAxes: [{display: false}], xAxes: [{display:false}]
       }}
   });


   var trChart = new Chart($("#trchart"), {
       type: 'line',
       data: {
       labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
       datasets: [
           {
               label: "My First dataset",
               fill: false,
               lineTension: 0.5,

               borderCapStyle: 'round',

               borderDash: [],
               borderDashOffset: 0.0,
               borderJoinStyle: 'miter',

               pointBorderWidth: 2,
               pointHoverRadius: 1,
               borderWidth: 2,
               pointHoverBackgroundColor: "rgba(75,192,192,1)",
               pointHoverBorderColor: "rgba(220,220,220,1)",
               pointHoverBorderWidth: 2,
               pointRadius: 2,
               pointHitRadius: 10,
                fill: true,
                borderColor: "#2F5CB0",
                backgroundColor: "rgba(78,108,201,0.3)",
                pointBorderColor: "#2F5CB0",
                pointBackgroundColor: "white",
                pointBorderWidth: 1,
               data: [65, 59, 80, 81, 32, 12, 40],
           }
       ]
   },
       options: {
        maintainAspectRatio: false,
    responsive: true,
           legend: {display:false},
       animation : false,
           scales: {
           yAxes: [{display: false}], xAxes: [{display:true}]
       }}

   });



  var visitChart = new Chart($("#visitchart"), {
    type: 'doughnut',
    data: {
      labels: [
        "New visitor",
        "Returning visitor",
      ],
      datasets: [
        {
          data: [{{$dashboard->n_new_visitor}}, {{$dashboard->n_returning_visitor == 0 ? 1 : $dashboard->n_returning_visitor}}],
          backgroundColor: ["#4E6CC9","#8C8C8C"],
          hoverBackgroundColor: ["#4E6CC9","#8C8C8C",]
        }
      ]},
    options: options
  });
</script>
@endsection
@section('content')
<div class="row">
  <div class="col-md-4 pl0">
    <div class="card " style="height: 240px;">
      <div class="header">
      <h4>General metrics</h4>
      </div>
      <div class="content">
        <p>Number of new Sign Up: 345</p>
        <p>Average card size: $34.30</p>
        <p>Most revenue category: Book</p>
        <p>User growth rate: 5.2</p>
      </div>
    </div>
  </div>
  <div class="col-md-4 pl0">
    <div class="card " style="height: 240px;">
      <div class="header">
          <h4> Revenue</h4>
      </div>
      <div class="content">
          <div style="height: 130px">
            <canvas id="trchart" width="400" height="400"> </canvas>
          </div>
          <p class="text-center" style="margin-top: 5px">Total revenue <b> $ 2 238</b> ($20 per customer) </p>

      </div>
    </div>
  </div>

  <div class="col-md-4 pl0">
    <div class="card" style="height: 240px;">
      <div class="header">
        <div class="small pull-right" style="margin-top: 10px">
          <span style="margin-right: 5px"> <i class="fa fa-circle" style="color: #8C8C8C"></i> Returning visitor </span>
          <span><i class="fa fa-circle" style="color: #4E6CC9"></i> New visitor</span>
        </div>
        <h4>Visitors</h4>
      </div>
      <div class="content">
        <div class="row">
          <div class="col-sm-5 mt">
            <span class="verybig" style="color: #4E6CC9">{{$dashboard->n_new_visitor}}</span> <i class="bigup fa fa-arrow-up"></i><br/>
            <span class="verybig" style="color: #8C8C8C">{{$dashboard->n_returning_visitor}}</span> <i class="bigdown fa fa-arrow-down"></i><br/>

          </div>
          <div class="col-sm-7">
            <div style="height: 158px">
              <canvas id="visitchart" width="400" height="400" ></canvas>
            </div>
            <div id="visitchartlegend" class="chart-legend"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-4 pl0">
    <div class="card">
      <div class="header">
        <h4>Retension rate</h4>
      </div>
      <div class="content" style="padding-top: 0px">
         <div class="row">
          <div class="col-sm-12">
            <p class="text-muted">This week</p>
            <div style="height: 100px">
              <canvas id="retenratechart" width="400" height="40"> </canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 pl0">
    <div class="card" style="  height: 275px;">
      <div class="header">
        <h4>Conversion rate</h4>
      </div>
      <div class="content text-center">
        <div class="bar_container">
          <div id="main_container">
            <div id="pbar" class="progress-pie-chart" data-percent="0">
              <div class="ppc-progress">
                <div class="ppc-progress-fill"></div>
              </div>
              <div class="ppc-percents">
                <div class="pcc-percents-wrapper">
                  <span>%</span>
                </div>
              </div>
            </div>
              <progress style="display: none" id="progress_bar" value="0" max="10"></progress>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 pl0">
    <div class="row">
      <div class="col-sm-12">
        <div class="card" style="height: 140px">
          <div class="header ">
            <h4 class="" style="margin-bottom: 20px">Highesh revenue campaign</h4>
            <h5 class="big pull-right" style="margin-top: 0">1/6</h5>
            <h5 class="big">Facebook</h5>
          </div>
          <div class="content " style="padding-top: 0px">
            <div class="">
              <div class="progress" style="width: 100%;margin-bottom: 10px;margin-top: 10px; height: 12px; border-radius: 35px;">
                <div data-percentage="20%" style="width: 50%; background-color: #4E6CC9" class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card" style="background: #4E6CC9; color: white; height: 120px">
          <div class="header text-center">
            <h4>Most effective refferer</h4>
          </div>
          <div class="content text-center">
            <span class="verybig" > SOCIAL</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('additional')

@endsection
