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
	<!-- Light bootstrap dashboard theme -->
	<link href="{{asset('css/animate.min.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/light-bootstrap-dashboard.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/pe-icon-7-stroke.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/gf-roboto.css')}}" rel='stylesheet' type='text/css'>
	<!-- Fonts and icons -->
	<link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
	<link href="{{ asset('/css/style.css') }}" rel="stylesheet">
	<link href="{{asset('css/fg.menu.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/daterangepicker.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/sweetalert.css')}}"/>
	<link rel="stylesheet" href="{{asset('css/odometer-theme-minimal.css')}}"/>
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
	@yield('style')
	<link href="{{asset('css/app.css')}}" rel="stylesheet"/>
	@yield('header-script')
</head>
<body>
@yield('body')

<script src="{{asset('js/he.js')}}" type="text/javascript"></script>
<script src="{{asset('js/jquery-1.12.1.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/bootstrap.min.js')}}" type="text/javascript"></script>

<!-- Light bootstrap dashboard theme -->
<script src="{{asset('/js/bootstrap-notify.js')}}"></script>
<script src="{{asset('/js/bootstrap-checkbox-radio-switch.js')}}"></script>
<script src="{{asset('/js/chartist.min.js')}}"></script>
<script src="{{asset('/js/light-bootstrap-dashboard.js')}}"></script>

<!-- App's dependencies -->
<script src="{{asset('js/moment.js')}}"></script>
<script src="{{asset('js/fg.menu.js')}}"></script>
<script src="{{asset('js/jquery.daterangepicker.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}" type="text/javascript"></script> @include('Alerts::alerts')
<script src="{{asset('js/odometer.min.js')}}"></script>
<script src="{{asset('js/meotricsws.js')}}" type="text/javascript"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script src="{{asset('js/app.js')}}"></script>
@yield('footer-script')
</body>
</html>
