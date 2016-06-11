@extends('layout.master')
@section('title', 'Meotrics')
@section('style')
@endsection
@section('script')
	<script src="{{asset('/js/Chart.js')}}"></script>
	<script>
		$(document).ready(function () {
			var progressbar = $('#progress_bar');
			max = progressbar.attr('max');
			time = (1000 / max) * 5;
			value = progressbar.val();

			var loading = function () {
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

			var animate = setInterval(function () {
				loading();
			}, time);
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

				legend: {display: false},
				maintainAspectRatio: false,
				responsive: true,
				height: "240",
				animation: false,
				scales: {
					yAxes: [{display: false}], xAxes: [{display: false}]
				}
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
			afterDraw: function (chart, easing) {

				if (chart == visitChart) {
					drawVisitNumber();
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
									<h6 style="margin-bottom:0; margin-top:20px;  color: gray">TRAFFIC</h6>
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
								<h6 style="margin: 0; color: gray">NEW SIGNUP</h6>
							</div>
							<div class="content text-center" style="padding-top: 0">
								<span class="big"> 37</span>
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
								<canvas id="trchart" width="400" height="400"></canvas>
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
					<h6 style="margin: 0; color: white">RETENSION RATE</h6>
				</div>
				<div class=" pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> 30%</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">AVERAGE CART SIZE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> 194 $</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
				<div class="header text-center">
					<h6 style="margin: 0; color: white">PURCHASE CONVERSION RATE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> 45%</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 pl0">
			<div class="card" style="background: #4E6CC9; color: white; height: 80px">
				<div class="header text-center">
					<h6 style="margin: 0;  color: white">ABANDONMENT RATE</h6>
				</div>
				<div class="content text-center pull-right mr">
					<span class="big" style="position: absolute; bottom: 20px;right: 42px;"> 28%</span>
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
							<h6 class="pull-right" style="margin:0; color: gray">REVENUER PER CUSTOM <span class="medium" style="color: #4e6cc9">$59.00</span></h6>

							<h6 style="margin:0; color:gray">TOTAL REVENUE <span class="medium" style="color: #4e6cc9">$46, 238.00</span></h6>

						</div>
						<div class="content">
							<div style="height: 190px">
								<canvas id="retenratechart" width="100%" height="100%"></canvas>
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
							<h6 style="position: absolute; bottom: 20px;right: 30px; text-align: right;width: 80%;margin-top: 0px;">
								Woman Fashion & Accessories</h6>
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
							<h6 style="position: absolute; bottom: 20px;right: 30px; text-align: right;width: 80%;margin-top: 0px;">
								Facebook</h6>
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
							<h6 style="position: absolute; bottom: 20px;right: 30px; text-align: right;width: 80%;margin-top: 0px;">
								Organic search</h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


@endsection
@section('additional')

@endsection
