<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<link rel="icon" type="image/png" href="{{asset('favicon.ico')}}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>@yield('title')</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
	<meta name="viewport" content="width=device-width"/>

	<!-- Bootstrap core CSS     -->
	<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/animate.min.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/light-bootstrap-dashboard.css')}}" rel="stylesheet"/>

	<!--     Fonts and icons     -->
	<link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
	<link href="{{asset('css/gf-roboto.css')}}" rel='stylesheet' type='text/css'>
	<link href="{{asset('css/pe-icon-7-stroke.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/app.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/fg.menu.css')}}" rel="stylesheet"/>
	<link rel="stylesheet" href="{{asset('css/daterangepicker.css')}}"/>
	@yield('style')
	<script>
		function onPageLoad(fn) {
			if (window.addEventListener)
				window.addEventListener('load', fn, false);
			else if (window.attachEvent)
				window.attachEvent('onload', fn);
		}

		function throttle(fn, delay) {
			var timer = null;
			return function () {
				var context = this, args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					fn.apply(context, args);
				}, delay);
			};
		}
	</script>
</head>
<body>
<div class="wrapper">
	<div class="sidebar" data-color="green" data-image="/img/sidebar-4.jpg">
		<div class="sidebar-wrapper">
			<div class="logo">
				<a href="/"> <img src="{{asset('img/logo.png')}}" width="30px"/></a>
				<span class="logo-text">meotrics</span>
			</div>

			<ul class="nav">
				<li class="{{ !isset($sidebarselect) ||  $sidebarselect == 'home' ? 'active' : '' }}">
					<a href="/home">
						<i class="pe-7s-display2"></i>
						<p>Dashboard</p>
					</a>
				</li>
				<li class="{{isset($sidebarselect) && $sidebarselect == 'trend' ? 'active' : '' }}">
					<a href="/trend">
						<i class="pe-7s-cup"></i>
						<p>Trend</p>
					</a>
				</li>
				<li class="{{isset($sidebarselect) && $sidebarselect == 'segment' ? 'active' : '' }}">
					<a href="/segment">
						<i class="pe-7s-user"></i>
						<p>Segmentation</p>
					</a>
				</li>
				<li class="user-area">
					<ul class="media-list" style="margin-left: 10px; margin-top: 10px;">
						<li class="media">
							<div class="media-left">
								<a href="#">
									<img class="media-object" width="40px" src="/img/user.png" alt="">
								</a>
							</div>
							<div class="media-body">
								<h5 style="color:white;" class="media-heading">thanhpk</h5>
								<a class="small" href="/auth/default/view?id=4">profile</a>
								&nbsp; &nbsp;
								<a class="small" href="/auth/logout" data-method="get">logout</a>
								<a class="small" href="/actiontype" data-method="get">action type</a>
							</div>
						</li>
					</ul>

				</li>
			</ul>
		</div>
	</div>

	<div class="main-panel">
		<nav class="navbar navbar-default navbar-fixed">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse"
					        data-target="#navigation-example-2">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-left">
						@include('segment/select')
						<li>
							<a style="padding: 0px">
								<div class="input-group" style="width: 250px;">
                  <span class="input-group-addon">
	                  <i class="pe-7s-date" style="font-size:26px"></i>
                  </span>
									<input type="text" class="form-control" id="date-range">

								</div>
							</a>
						</li>
					</ul>

					<ul class="nav navbar-nav navbar-right">


					</ul>
				</div>
			</div>
		</nav>

		<div class="content">
			<div class="container-fluid">@yield('content')</div>

		</div>
	</div>
</div>
<div class="">
	@yield('footer')
</div>
<script src="{{asset('js/he.js')}}" type="text/javascript"></script>
<script src="{{asset('js/jquery-1.12.1.min.js')}}" type="text/javascript"></script>

<script src="{{asset('js/bootstrap.min.js')}}" type="text/javascript"></script>

<!--  Checkbox, Radio & Switch Plugins -->
<script src="{{asset('/js/bootstrap-checkbox-radio-switch.js')}}"></script>
<script src="{{asset('/js/chartist.min.js')}}"></script>

<!--  Notifications Plugin    -->
<script src="{{asset('/js/bootstrap-notify.js')}}"></script>
<script src="{{asset('/js/light-bootstrap-dashboard.js')}}"></script>

<script src="{{asset('js/moment.js')}}"></script>
<script src="{{asset('js/fg.menu.js')}}"></script>
<script src="{{asset('js/jquery.daterangepicker.js')}}"></script>

@yield('script')
<script>

	var config = {
		customOpenAnimation: function (cb) {
			$(this).fadeIn(300, cb);
		},
		customCloseAnimation: function (cb) {
			$(this).fadeOut(300, cb);
		}
	};
	$('#date-range').dateRangePicker(config);


</script>
</body>
</html>
