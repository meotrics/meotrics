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
           yAxes: [{
               display: false
           }]
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
           yAxes: [{
               display: false
           }]
       }}
       
   });
   
   var trChart = new Chart($("#trchartpc"), {
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
               data: [65, 59, 80, 54, 56, 55, 65],
           }
       ]
   },
       options: {
        maintainAspectRatio: false,
    responsive: true,
           legend: {display:false},
       animation : false,
           scales: {
           yAxes: [{
               display: false
           }]
       }
       }
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
               data: [430, 23],
               backgroundColor: [
                   "#4E6CC9",
                   "#8C8C8C",
               ],
               hoverBackgroundColor: [
                   "#4E6CC9",
                   "#8C8C8C",
               ]
           }]}, options: options
       
   });
   
</script>
@endsection
@section('content')
<div class="row">
   <div class="col-md-5" >
      <div class="card row" style="height: 275px;">
         <div class="header">
            <h4>Visits</h4>
            <p class="category pull-right">TODAY</p>
         </div>
         <div class="content">
            <div class="row">
               <div class="col-sm-5">
                  <span class="verybig" style="color: #4E6CC9">430</span> <i class="bigup fa fa-arrow-up"></i><br/>
                  <span class="verybig" style="color: #8C8C8C">23</span> <i class="bigdown fa fa-arrow-down"></i><br/>

               </div>
               <div class="col-sm-7">
                <div style="height: 158px">
                  <canvas id="visitchart" width="400" height="400" ></canvas>
                  </div>
                  <div id="visitchartlegend" class="chart-legend"></div>
               </div>
            </div>
            <div class="small mt">
            <div class="pull-right"> <i class="fa fa-circle" style="color: #8C8C8C"></i> Returning visitor </div>
             <div > <i class="fa fa-circle" style="color: #4E6CC9"></i> New visitor </div>
             
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-7">
      <div class="card " style="height: 275px;"> 
         <div class="header">
            <div class="row">
               <div class="col-sm-6">
                  This week
                   <div style="height: 152px">
                  <canvas id="trchart" width="400" height="400"> </canvas>
                  </div>
                  <h5>Total revenue</h5> 
                  <span class="big" >
                  $ 2 238
                  </span>  <i class="bigup fa fa-arrow-up"></i>
               </div>
               <div class="col-sm-6">
                  This week
                  <div style="height: 152px">
                  <canvas id="trchartpc" width="400" height="40"> </canvas>
                  </div>
                  <h5>Revenue per customer</h5>
                  <span class="big">$ 13.5 </span>  <i class="bigup fa fa-arrow-up"></i>
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
            <h4>Retension rate</h4>
              
            </div>
            <div class="content" style="padding-top: 0px">
             <div class="row">

                  
                  
                  <div class="col-sm-12">
                  <p class="muted">This week</p>
                  <div style="height: 100px">
                     <canvas id="retenratechart" width="400" height="40"> </canvas>
                    </div>
                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="card" style="height: 100px">
            <div class="header"> <h4> User growth rate</h4>
            <div class="verybig">
            <i class="bigup fa fa-arrow-up"></i> 5%
            </div></div>
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