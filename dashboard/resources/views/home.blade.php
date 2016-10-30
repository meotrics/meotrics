    @extends('layout.master')
@section('title', 'Meotrics')
@section('style')
@endsection
@section('script')
	<script src="{{asset('/js/Chart.js')}}"></script>
	<script>
		onPageLoad(function () {
			var pending_pageview = false;

			function update_pageview(){
				if(!pending_pageview){
					pending_pageview = true;
					throttle(function(){
						$.post('/app/getpageview/{{$appcode}}', function(data){
							pending_pageview = false;
							data = JSON.parse(data);
							$('.id_newv').html(data.newVisitors);
							if(data.returningVisitors >= 0) {
								$('.id_retu').html(data.returningVisitors);
							}
							drawVisitChart(data.newVisitors, data.returningVisitors);
						});
					}, 1000)();
				}
			}
			
			function update_register() {
				console.log('fc');
				throttle(function(){
					console.log('d');
					var endtime= $tp.val().split(' ')[2];
					var starttime = $tp.val().split(' ')[0];
					$.post('/app/getsignup/{{$appcode}}/' + starttime + "/" + endtime, function(data){
						data = JSON.parse(data);
						$('.id_newsignup').html(data.signup);
					})
				}, 1000)();
			}
			websock.appChange('{{$appcode}}', 'type.pageview', update_pageview);
			websock.appChange('{{$appcode}}', 'type.register', update_register);
			
			var $tp = $('#timepick');
			var tp = $tp.dateRangePicker();

			@if(isset($starttime))
			$tp.data('dateRangePicker').setDateRange('{{$starttime}}', '{{$endtime}}');
			@else
			// 30 ngay truoc do
			var today = new Date().toISOString().substr(0, 10);
			var lastyear = new Date(new Date().getTime() - 31104000000).toISOString();
			$tp.data('dateRangePicker').setDateRange(lastyear, today);
			@endif

			tp.bind('datepicker-change', function () {
				var val = $(this).val();
				$.post('/dashboard/{{$appcode}}/currenttime', {
					'endTime': val.split(' ')[2],
					'startTime': val.split(' ')[0]
				}, function () {
					location.reload();
				});
			});
		});

		var options = {
			tooltips: {enabled: false},
			legend: {display: false},
			animation: false,
			segmentShowStroke: false,
			animateRotate: false,
			maintainAspectRatio: false,
			responsive: true,
			percentageInnerCutout: 50,
			scaleShowLabels: false,
			//tooltipTemplate: {{"<%= value %> %"}},
		};

		var labels = [];
		labels = {!!   json_encode($dashboard->labels) !!};
		//
		var totalrevenues = [];
		totalrevenues = {!! json_encode($dashboard->revenues) !!};

		//
		//var usergrowthrates = [];
		//usergrowthrates = {-- !! json_encode($dashboard->usergrowth_rates) !! --};
		//
		var npurchases = [];
		npurchases = {!! json_encode($dashboard->n_purchases) !!};
		console.log(totalrevenues);
		console.log(npurchases);
		//
		var traffic24 = [];
		traffic24 = {!! json_encode($dashboard->traffic24) !!};
		var traffic24labels =[];
		traffic24labels = {!! json_encode($dashboard->traffic24labels) !!};
		var retenratechart = new Chart($("#revenuechart"), {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: "Revenue",
						yAxisID: "y-axis-0",
						lineTension: 0.5,
						borderCapStyle: 'round',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointHoverRadius: 3,
						borderWidth: 2,
						pointHoverBackgroundColor: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(50,50,50,1)",
						pointHoverBorderWidth: 2,
						pointRadius: 2,
						pointHitRadius: 10,
						fill: true,
						borderColor: "#2F5CB0",
						backgroundColor: "rgba(78,108,201,0.3)",
						pointBorderColor: "#2F5CB0",
						pointBackgroundColor: "white",
						pointBorderWidth: 1,
						data: totalrevenues
					},

					{
						label: "Number of purchase",
						yAxisID: "y-axis-1",
						lineTension: 0.5,
						borderCapStyle: 'round',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointHoverRadius: 3,
						borderWidth: 2,
						pointHoverBackgroundColor: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(50,50,50,1)",
						pointHoverBorderWidth: 2,
						pointRadius: 2,
						pointHitRadius: 10,
						fill: true,
						borderColor: "#1dc7ea",
						backgroundColor: "#9deeff",
						pointBorderColor: "#1dc7ea",
						pointBackgroundColor: "white",
						pointBorderWidth: 1,
						data: npurchases
					}
				]
			},
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {display: false},
				animation: false,
				scales: {
					yAxes: [
						{
						position: "left",
						"id": "y-axis-0"
						}, {
							position: "right",
							"id": "y-axis-1"
						}
					]
					, xAxes: [{display: true}]
				}
			}
		});


		var trChart = new Chart($("#ugchart"), {
			type: 'bar',
			data: {
				labels: traffic24labels, //labels,
				datasets: [
					{
						label: "Traffic",
						lineTension: 0.5,
						borderCapStyle: 'round',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointHoverRadius: 3,
						borderWidth: 1,
						//pointHoverBackgroundColor: "rgba(75,192,192,1)",
						//pointHoverBorderColor: "rgba(50,50,50,1)",
						//pointHoverBorderWidth: 2,
						//pointRadius: 2,
						pointHitRadius: 10,
						fill: true,
						borderColor: "#2F5CB0",
						backgroundColor: "rgba(78,108,201,0.3)",
						//pointBorderColor: "#2F5CB0",
						//pointBackgroundColor: "white",
						//pointBorderWidth: 1,
						data: traffic24// usergrowthrates
					}
				]
			},
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {display: false},
				animation: false,
				scales: {
					yAxes: [{display: true}], xAxes: [{display: true}]
				}
			}
		});

		function drawVisitNumber( newv, retv) {
			var canvas = document.getElementById("visitchart");
			var ctx = canvas.getContext("2d");
			ctx.font = "22px Roboto";
			ctx.fillStyle = "black";
			ctx.textAlign = "center";
			ctx.fillText(  parseInt(newv) + parseInt(retv) + "" , canvas.width / 2, canvas.height / 2);
			ctx.font = "14px Roboto";
			ctx.fillStyle = "black";
			ctx.textAlign = "center";
			ctx.fillText("visitors", canvas.width / 2, canvas.height / 2 + 20);
		}

		function drawVisitChart(newv, retv)
		{
			newv = parseInt(newv);
			//newv = newv == 0 ? 1 : newv;
			retv = parseInt(retv);
			var visitChart = new Chart($("#visitchart"), {
				type: 'doughnut',
				data: {
					labels: ["New visitor", "Returning visitor"],
					datasets: [
						{
							data: [(newv == 0 && retv == 0) ? 1 : newv, retv],
							backgroundColor: ["#4E6CC9", "#8C8C8C"],
							hoverBackgroundColor: ["#4E6CC9", "#8C8C8C"]
						}
					]
				},
				options: options
			});
			Chart.pluginService.register({
				afterDraw: function (chart) {
					if (chart == visitChart) {
						drawVisitNumber( newv,retv);
					}
				}
			});

			drawVisitNumber( newv,retv);
		}

		drawVisitChart({{$dashboard->n_new_visitor}},{{ $dashboard->n_returning_visitor}});
	</script>
@endsection
@section('content')
	<div class="row">
		<div class="col-sm-4 ">
			<div class="row">
				<div class="col-md-12 pl0">
					<div class="card border-top-1" style="height: 140px;">
						<div style="padding-left: 12px">
							<div class="row ">

								<div class="col-sm-6" style="font-size: 12px">
									<h6 style="margin-bottom:0; margin-top:12px;  color:#8492af;font-size: 18px">TODAY VISITOR</h6>
									<div style="margin-left: 10px;margin-top:10px;">
										<div class="" style="font-size: 9px"><i class="fa fa-circle" style="color: #4E6CC9"></i> New visitor</div>
										<div class="medium text-center id_newv" style="color: #4E6CC9">{{$dashboard->n_new_visitor}}</div>
										<div style="margin-top: 6px;font-size: 9px"><i class="fa fa-circle " style="color: #8C8C8C"></i> Returning visitor
										</div>
										<div class="medium text-center id_retu" style="color: #25396e;">{{$dashboard->n_returning_visitor}}</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div style="height: 110px; width: 105px; margin-top: 20px">
										<canvas id="visitchart" width="400" height="400"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div>
			<div class="row">
				<div class="vl-md-6" style="margin-right: 9px">
					<div class="card border-top-1" style="height: 130px">
						<div class="" style="">
							<div class="header text-center">
								<h6 style="margin: 0; color: #0e1a35">NEW SIGNUP</h6>
							</div>
							<div class="content text-center" style="padding-top: 0">
								<span class="big id_newsignup" style="color: #4164c2;">{{$dashboard->n_new_signup}}</span>
							</div>
						</div>
					</div>
				</div>
				<div class="vl-md-6">
					<div class="card border-top-1" style="height: 130px">
						<div class="" style="">
							<div class="header text-center">
								<h6 style="margin: 0; color: #0e1a35">USER GROWTH RATE</h6>
							</div>
							<div class="content text-center" style="padding-top: 0">
								<span class="big id_newsignup" style="color: #4164c2;">{{$dashboard->usergrowth_rate}}%</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-8 pl0">
			<div class="row">
				<div class="col-md-12">
					<div class="card border-top-1" style="height: 285px;">
						<div class="header">
							<h6 style="margin:0; color: gray">TRAFFIC</h6>
						</div>
						<div class="content">
							<div style="height: 175px">
								<canvas id="ugchart" width="400" height="400"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3 pl0">
			<div class="card vl-table-4 bg-table-1">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">TODAY RETENSION RATE</h6>
				</div>
				<div class=" pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> {{floor($dashboard->retention_rate)}} %</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card vl-table-4 bg-table-2">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">AVERAGE CART SIZE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;">${{floor($dashboard->n_avgcartsize)}}</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card vl-table-4 bg-table-3">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">PURCHASE CONVERSION RATE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> {{floor($dashboard->conversion_rate)}}
						%</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card vl-table-4 bg-table-4">
				<div class="header text-center">
					<h6 style="margin: 0;  color: white">ABANDONMENT RATE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> 0%</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9 pl0">
			<div class="row">
				<div class="col-md-12">
					<div class="card border-top-2" style="height: 360px;">
						<div class="header">
							<div class="row">
								<div class="col-md-3" style="margin-top:13px">
									<h6 style="margin:0; color:#0e1a35;font-size: 18px;">
										TOTAL REVENUE
									</h6>
								</div>
								<div class="row col-md-2">
									<h6 style="margin:0; color:#0e1a35">
										REVENUE
										</h6>
									<h6 style="margin-left:0px; ">
										<span class="medium" style="color: #4164c2">$ {{$dashboard->total_revenue}}</span>
									</h6>
								</div>
								<div class="row col-md-3">
									<h6 style="margin:0; color: #0e1a35">
										REVENUE PER CUSTOM
										</h6>
									<h6 style="margin-left:0px;">
										<span class="medium" style="color: #4164c2">$ {{floor($dashboard->revenue_per_customer)}}</span>
									</h6>
								</div>
							</div>
						</div>
						<div class="content">
							<div style="height: 190px">
								<canvas id="revenuechart" width="100%" height="100%"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-3 pl0">
			<div class="row">
				<div class="col-sm-12 ">
					<div class="card border-right-1" style=" height: 109px">
						<div class="header text-center">
							<h5 style="margin: 0; text-align: right;color: #0e1a35;font-size: 14px;">MOST POPULAR CATEGORY</h5>
						</div>
						<div class="content text-center pull-right mr">
							<h6 style="position: absolute; right: 30px; text-align: right;width: 80%;margin-top: 0;color:#5584ff;">
							{{$dashboard->most_popular_category or "N/A"}}
							</h6>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 ">
					<div class="card border-right-2" style=" height: 109px">
						<div class="header text-center">
							<h5 style="margin: 0; text-align: right;color: #0e1a35;font-size: 14px;">HIGHEST REVENUE CAMPAIGN</h5>
						</div>
						<div class="content text-center pull-right mr">
							<h6 style="position: absolute;right: 30px; text-align: right;width: 80%;margin-top: 0;color:#4164c2">
								{{$dashboard->highest_revenue_campaign or "N/A" }}
							</h6>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 ">
					<div class="card border-right-3" style=" height: 109px">
						<div class="header text-center">
							<h5 style=" margin: 0; text-align: right;color: #0e1a35;font-size: 14px;">MOST EFFECTIVE REFERAL</h5>
						</div>
						<div class="content text-center pull-right mr">
							<h6 style="position: absolute;right: 30px; text-align: right;width: 80%;margin-top: 0;color:#25396e">
								{{$dashboard->most_effective_ref or "None"}}
							</h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('additional')

@endsection

@section('action')
	<li>
		<input style="width: 192px;margin-top: 11px;" class="form-control mr" id="timepick">
	</li>
@endsection

