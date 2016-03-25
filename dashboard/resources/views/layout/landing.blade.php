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
  <link href="{{asset('css/fg.menu.css')}}" rel="stylesheet"/> <!-- QUESTION: ??? -->
  <link href="{{asset('css/daterangepicker.css')}}"/>
  <link href="{{asset('css/sweetalert.css')}}"/>
  <!-- App's styles -->
  <link href="{{asset('css/app.css')}}" rel="stylesheet"/>

  @yield('style')

  <script src="{{asset('js/he.js')}}" type="text/javascript"></script>
  <script src="{{asset('js/jquery-1.12.1.min.js')}}" type="text/javascript"></script>

  <script src="{{asset('js/bootstrap.min.js')}}" type="text/javascript"></script>

  <!-- Light bootstrap dashboard theme -->
  <script src="{{asset('/js/bootstrap-notify.js')}}"></script>
  <script src="{{asset('/js/bootstrap-checkbox-radio-switch.js')}}"></script>
  <script src="{{asset('/js/chartist.min.js')}}"></script>
  <script src="{{asset('/js/light-bootstrap-dashboard.js')}}"></script>
  <!-- App's dependencies -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/4.6.1/lodash.js"></script>
  <script src="{{asset('js/moment.js')}}"></script>
  <script src="{{asset('js/fg.menu.js')}}"></script>
  <script src="{{asset('js/jquery.daterangepicker.js')}}"></script>
  <script src="{{asset('js/sweetalert.js')}}" type="text/javascript"></script> @include('Alerts::alerts')

  <script>
    var _helper = {
      notification: {
        error: function(err, options){
          $.notify({
            icon: "pe-7s-attention",
            message: err
          }, _.merge({
            timer: 3000,
            placement: {
              from: 'top',
              align: 'right'
            }
          }, _.merge(options || {}, { type: 'danger' })));
        },
        success: function(message, options){
          $.notify({
            icon: "pe-7s-check",
            message: message
          }, _.merge({
            timer: 3000,
            placement: {
              from: 'top',
              align: 'right'
            }
          }, _.merge(options || {}, { type: 'success' })));
        }
      }
    }
  </script>

  @yield('script')
  
</head>
<body>
  <div class="wrapper wrapper-full-page">
    <div class="full-page login-page" data-color="azure" data-image="/img/sidebar-4.jpg">
      <!--   you can change the color of the filter page using: data-color="blue | azure | green | orange | red | purple" -->
      <div class="content">
        <div class="container">
          @yield('content')
        </div>
      </div>
      <div class="full-page-background" style="background-image: url(/img/sidebar-4.jpg) "></div>
    </div>
  </div>
</body>
</html>
