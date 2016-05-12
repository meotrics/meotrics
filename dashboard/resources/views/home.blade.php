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
       
       percentageInnerCutout: 50,
       scaleShowLabels: false,
       tooltipTemplate: "<%= svalue %>%",
   }
   
   
   
   var retenratechart = new Chart($("#retenratechart"), {
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
               data: [65, 59, 80, 81, 32, 12, 40],
           }
       ]
   },
       options: {
           legend: {display:false},
       animation : false,
           xAxes: [{
               display: false
           }]
       }
   });
   
   
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
               data: [65, 59, 80, 81, 32, 12, 40],
           }
       ]
   },
       options: {
           legend: {display:false},
       animation : false,
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
               data: [65, 59, 80, 54, 56, 55, 65],
           }
       ]
   },
       options: {
           legend: {display:false},
       animation : false,
           xAxes: [{
               display: true
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
<div class="row">
   <div class="col-md-4">
      <div class="row">
         <div class="card">
            <div class="header">
               <div class="row">
                  Retension rate
                  <
                  <div class="col-sm-12">
                     <canvas id="retenratechart" width="400" height="400"> </canvas>
                     Total revenue
                     $ 2 238
                  </div>
               </div>
            </div>
            <div class="content">
            </div>
         </div>
      </div>
      <div class="row">
         <div class="card">
            <div class="header"> User growth rate</div>
            <div class="header"> 5%</div>
         </div>
      </div>
   </div>
   <div class="col-md-4">
      <div class="card">
         <div class="header">
            Purchase conversion rate
         </div>
         <div class="content">
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
                  <progress style="display: none" id="progress_bar" value="0" max="100"></progress>
               </div>
            </div>
         </div>
         LOW 
      </div>
   </div>
   <div class="col-md-4">
      <div class="row">
         <div class="card">
            <div class="header">
               Highesh revenue campaign
            </div>
            <div class="content">
               <div class="col-md-11">
                  <div class="progress">
                     <div data-percentage="20%" style="width: 50%;" class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="card">
            <div class="header">
               Most effective refferer
            </div>
            <div class="content">
               SOCIAL
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