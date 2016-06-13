@extends('layout.master')
@section('title', 'Meotrics')
@section('style')
@endsection
@section('script')
	<script src="{{asset('/js/Chart.js')}}"></script>
	<script>
		onPageLoad(function () {
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
		var usergrowthrates = [];
		usergrowthrates = {!! json_encode($dashboard->usergrowth_rates) !!};
		//
		var retenratechart = new Chart($("#revenuechart"), {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: "Revenue",
						lineTension: 0.5,
						borderCapStyle: 'round',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
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
						data: totalrevenues
					}
				]
			},
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {display: false},
				animation: false,
				scales: {
					yAxes: [{display: false}], xAxes: [{display: true}]
				}
			}
		});

		var trChart = new Chart($("#ugchart"), {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: "User Growth Rate ",
						lineTension: 0.5,
						borderCapStyle: 'round',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
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
						data: usergrowthrates
					}
				]
			},
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {display: false},
				animation: false,
				scales: {
					yAxes: [{display: false}], xAxes: [{display: true}]
				}
			}
		});

		function drawVisitNumber() {
			var canvas = document.getElementById("visitchart");
			var ctx = canvas.getContext("2d");
			ctx.font = "22px Roboto";
			ctx.fillStyle = "black";
			ctx.textAlign = "center";
			ctx.fillText("{{$dashboard->n_new_visitor + $dashboard->n_returning_visitor}}", canvas.width / 2, canvas.height / 2);
			ctx.font = "14px Roboto";
			ctx.fillStyle = "black";
			ctx.textAlign = "center";
			ctx.fillText("visitors", canvas.width / 2, canvas.height / 2 + 20);

		}

		Chart.pluginService.register({
			afterDraw: function (chart) {
				if (chart == visitChart) {
					drawVisitNumber();
				}
			}
		});

		var visitChart = new Chart($("#visitchart"), {
			type: 'doughnut',
			data: {
				labels: ["New visitor", "Returning visitor"],
				datasets: [
					{
						data: [{{$dashboard->n_new_visitor}}, {{$dashboard->n_returning_visitor == 0 ? 1 : $dashboard->n_returning_visitor}}],
						backgroundColor: ["#4E6CC9", "#8C8C8C"],
						hoverBackgroundColor: ["#4E6CC9", "#8C8C8C"]
					}
				]
			},
			options: options
		});
		drawVisitNumber();


	</script>
@endsection
@section('content')
	<div class="row">
		<div class="col-sm-4 ">
			<div class="row">
				<div class="col-md-12 pl0">
					<div class="card" style="height: 140px;">
						<div style="padding-left: 12px">
							<div class="row">

								<div class="col-sm-6" style="font-size: 12px">
									<h6 style="margin-bottom:0; margin-top:20px;  color: gray">TODAY VISITOR</h6>
									<div class=""><i class="fa fa-circle" style="color: #4E6CC9"></i> New visitor</div>
									<div class="medium text-center" style="color: #4E6CC9">{{$dashboard->n_new_visitor}}</div>
									<div style="margin-top: 10px"><i class="fa fa-circle" style="color: #8C8C8C"></i> Returning visitor
									</div>
									<div class="medium text-center" style="color: #8C8C8C;">{{$dashboard->n_returning_visitor}}</div>

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
				<div class="col-sm-12 pl0 ">
					<div class="card" style="height: 70px">
						<div class="" style="">
							<div class="header text-center">
								<h6 style="margin: 0; color: gray">TODAY NEW SIGNUP</h6>
							</div>
							<div class="content text-center" style="padding-top: 0">
								<span class="big">{{$dashboard->n_new_signup}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-8 pl0">
			<div class="row">
				<div class="col-md-12">
					<div class="card " style="height: 225px;">
						<div class="header">
							<h6 style="margin:0; color: gray"> USER GROWTH RATE</h6>
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
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">TODAY RETENSION RATE</h6>
				</div>
				<div class=" pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> {{$dashboard->retention_rate}} %</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">AVERAGE CART SIZE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;">${{$dashboard->n_avgcartsize}}</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">PURCHASE CONVERSION RATE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> {{$dashboard->conversion_rate}}
						%</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
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
					<div class="card " style="height: 240px;">
						<div class="header">
							<h6 class="pull-right" style="margin:0; color: gray">
								REVENUER PER CUSTOM
								<span class="medium" style="color: #4e6cc9">$ {{$dashboard->revenue_per_customer}}</span>
							</h6>

							<h6 style="margin:0; color:gray">
								TOTAL REVENUE
								<span class="medium" style="color: #4e6cc9">$ {{$dashboard->total_revenue}}</span>
							</h6>
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
					<div class="card" style=" height: 70px">
						<div class="header text-center">
							<h5 style="margin: 0; text-align: right;color: #353535;font-size: 13px;">MOST POPULAR CATEGORY</h5>
						</div>
						<div class="content text-center pull-right mr">
							<h6 style="position: absolute; bottom: 20px;right: 30px; text-align: right;width: 80%;margin-top: 0;">
							{{$dashboard->most_popular_category or "N/A"}}
							</h6>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 ">
					<div class="card" style=" height: 70px">
						<div class="header text-center">
							<h5 style="margin: 0; text-align: right;color: #353535;font-size: 13px;">HIGHEST REVENUE CAMPAIGN</h5>
						</div>
						<div class="content text-center pull-right mr">
							<h6 style="position: absolute; bottom: 20px;right: 30px; text-align: right;width: 80%;margin-top: 0;">
								{{$dashboard->highest_revenue_campaign or "N/A" }}
							</h6>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 ">
					<div class="card" style=" height: 70px">
						<div class="header text-center">
							<h5 style=" margin: 0; text-align: right;color: #353535;font-size: 13px;">MOST EFFECTIVE REFERAL</h5>
						</div>
						<div class="content text-center pull-right mr">
							<h6 style="position: absolute; bottom: 20px;right: 30px; text-align: right;width: 80%;margin-top: 0;">
								{{$dashboard->most_effective_ref or "N/A"}}
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
		<input style="width: 220px;margin-top: 7px;" class="form-control mr" id="timepick">
	</li>
@endsection

