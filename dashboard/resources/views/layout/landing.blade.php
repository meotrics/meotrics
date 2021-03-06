<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<link rel="icon" type="image/png" href="{{asset('favicon.ico')}}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>@yield('title')</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
	<meta name="viewport" content="width=device-width"/>

	@yield('header')
	<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/gf-roboto.css')}}" rel='stylesheet' type='text/css'>
	<link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
	<link href="{{asset('css/landing.css')}}" rel="stylesheet"/>
	<link href="{{asset('css/css3-buttons.css')}}" rel="stylesheet"/>
	<link href='/css/bootstrap-inputs-min.css' rel='stylesheet'>

	<style>

		/**
		 * Login Modal
		 */
		.ml {
			margin-left: 10px;
		}

		.modal-content {
			border-radius: 0;
		}

		.login-box {
			background: #f6f7fa;
			-moz-box-shadow: 0 50px 150px -40px #5584ff;
			-webkit-box-shadow: 0 50px 150px -40px #5584ff;
			box-shadow: 0 50px 150px -40px #5584ff;
		}

		.login-box .logo > img {
			height: 40px;
		}

		.login-box .login-box-header {
			padding: 15px;
			background-color: #0e1a35;
		}

		.login-box .login-navbar {
			margin-top: -15px;
			margin-right: -15px;
			float: right;
			padding-left: 0;
		}

		.login-box .login-navbar > li {
			float: left;
			display: block;
		}

		.login-box .login-navbar > li > a {
			width: 100px;
			display: block;
			color: #fff;
			text-align: center;
			padding: 25px 15px 20px 15px;
			border-bottom: 5px solid transparent;
			text-decoration: none;
		}

		.login-navbar > li.active > a, .login-navbar > li.active > a:focus, .login-navbar > li.active > a:hover {
			border-bottom: 5px solid #5584ff;
			color: #5584ff;
		}

		h1 {
			font-weight: 300;
			padding: 10px 0;
			font-size: 28px;
			color: #8492af;
		}
		#reset > form input,
		#login > form input,
		#register > form input {
			background-size: 15px;
			text-indent: 30px;
		}


		#login > form > i,
		#register > form > i {
			color: #fff;
			font-size: 8px;
			margin-left: -20px;
		}

		.username {
			background: url("/img/form-user-icon.png") no-repeat 10px center;
		}

		.email {
			background: url("/img/form-email-icon.png") no-repeat 10px center;
		}

		.psw {
			background: url("/img/form-password-icon.png") no-repeat 10px center;
		}
	</style>
	@yield('style')

</head>
<body>
@yield('content')


<script src="{{asset('js/he.js')}}" type="text/javascript"></script>
<script src="{{asset('js/jquery-1.12.1.min.js')}}" type="text/javascript"></script>

<script src="{{asset('js/bootstrap.min.js')}}" type="text/javascript"></script>

<!-- App's dependencies -->
<script src="{{asset('js/moment.js')}}"></script>
<script src="{{asset('js/fg.menu.js')}}"></script>
<script src="{{asset('js/jquery.daterangepicker.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}" type="text/javascript"></script> @include('Alerts::alerts')
<script src='/js/inputs.js'></script>
<script>
	var _helper = {
		notification: {
			error: function (err, options) {
				options = options || {};
				options.type = 'danger';
				option.timer = 3000;
				option.placement = {
					from: 'top',
					align: 'right'
				};

				$.notify({
					icon: "pe-7s-attention",
					message: err
				}, options);
			},
			success: function (message, options) {
				options = options || {};
				options.type = 'success';
				option.timer = 3000;
				option.placement = {
					from: 'top',
					align: 'right'
				};

				$.notify({
					icon: "pe-7s-check",
					message: message
				}, options);
			}
		}
	}
</script>
<script src="/js/app.js"></script>
@yield('script')
</body>
</html>
