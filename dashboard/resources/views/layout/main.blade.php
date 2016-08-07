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
	<script type="text/javascript">var _kmq = _kmq || [];
		var _kmk = _kmk || '517e9b1f300005861a6db4b27428d66c1902ad70';
		function _kms(u){
			setTimeout(function(){
				var d = document, f = d.getElementsByTagName('script')[0],
						s = d.createElement('script');
				s.type = 'text/javascript'; s.async = true; s.src = u;
				f.parentNode.insertBefore(s, f);
			}, 1);
		}
		_kms('//i.kissmetrics.com/i.js');
		_kms('//scripts.kissmetrics.com/' + _kmk + '.2.js');
	</script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-80655428-1', 'auto');
		<?php
			if (\Auth::user() != null){
				$email = \Auth::user()->email;
				echo "ga('set', '&uid', '".$email."');";
			}
		?>
    ga('send', 'pageview');

	</script>

</head>
<body>
@if($verified == 0)
<div class="alert alert-danger id_ver" style="    position: relative; margin-bottom: 0">
	<button type="button" aria-hidden="true" class="close" style="color:red">×</button>
	<span>A confirm email has already sent into your inbox, please confirm your email address. Don't get it ?, please check your spam box or <a style="color:#164dff" onclick="$('.id_ver').addClass('hidden'); $.post('/auth/resent').fail(function(){alert('cannot resent')});" href="#">resent it</a></span>
</div>
@endif
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
