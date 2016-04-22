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
  <link href="{{asset('css/gf-roboto.css')}}" rel='stylesheet' type='text/css'>
  <!-- Fonts and icons -->
  <link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
  <link href="{{asset('css/fg.menu.css')}}" rel="stylesheet"/> <!-- QUESTION: ??? -->
  <link href="{{asset('css/daterangepicker.css')}}"/>
  <link href="{{asset('css/sweetalert.css')}}"/>
  <!-- App's styles -->
  <link href="{{asset('css/landing.css')}}" rel="stylesheet"/>

  @yield('style')

  <script src="{{asset('js/he.js')}}" type="text/javascript"></script>
  <script src="{{asset('js/jquery-1.12.1.min.js')}}" type="text/javascript"></script>

  <script src="{{asset('js/bootstrap.min.js')}}" type="text/javascript"></script>

  <!-- App's dependencies -->
  <script src="{{asset('js/moment.js')}}"></script>
  <script src="{{asset('js/fg.menu.js')}}"></script>
  <script src="{{asset('js/jquery.daterangepicker.js')}}"></script>
  <script src="{{asset('js/sweetalert.js')}}" type="text/javascript"></script> @include('Alerts::alerts')

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

  @yield('script')
  
</head>
<body>
  @yield('content')
</body>
</html>
