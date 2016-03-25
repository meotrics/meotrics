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
  
  <style>
    /* Additional Light bootstrap dashboard styles (PRO features) */

    .wrapper.wrapper-full-page {
      height: auto;
      min-height: 100vh;
    }

    .full-page:after, .full-page:before {
      display: block;
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 2;
    }
    .full-page:before {
      opacity: .33;
      background: #000000;
    }
    .full-page:after {
      background: #787878;
      background: -moz-linear-gradient(top, #787878 0%, rgba(52, 52, 52, 0.4) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #787878), color-stop(100%, rgba(52, 52, 52, 0.4)));
      background: -webkit-linear-gradient(top, #787878 0%, rgba(52, 52, 52, 0.4) 100%);
      background: -o-linear-gradient(top, #787878 0%, rgba(52, 52, 52, 0.4) 100%);
      background: -ms-linear-gradient(top, #787878 0%, rgba(52, 52, 52, 0.4) 100%);
      background: linear-gradient(to bottom, #787878 0%, rgba(52, 52, 52, 0.4) 100%);
      background-size: 150% 150%;
      z-index: 3;
      opacity: 1;
    }
    .full-page > .content,
    .full-page > .footer {
      position: relative;
      z-index: 4;
    }
    .full-page > .content {
      min-height: calc(100vh - 70px);
    }
    .full-page .full-page-background {
      position: absolute;
      z-index: 1;
      height: 100%;
      width: 100%;
      display: block;
      top: 0;
      left: 0;
      background-size: cover;
      background-position: center center;
    }
    .full-page[data-image]:after, .full-page.has-image:after {
      opacity: .9;
    }
    .full-page[data-color="blue"]:after {
      background: #447DF7;
      background: -moz-linear-gradient(top, #447DF7 0%, rgba(83, 60, 225, 0.6) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #447DF7), color-stop(100%, rgba(83, 60, 225, 0.6)));
      background: -webkit-linear-gradient(top, #447DF7 0%, rgba(83, 60, 225, 0.6) 100%);
      background: -o-linear-gradient(top, #447DF7 0%, rgba(83, 60, 225, 0.6) 100%);
      background: -ms-linear-gradient(top, #447DF7 0%, rgba(83, 60, 225, 0.6) 100%);
      background: linear-gradient(to bottom, #447DF7 0%, rgba(83, 60, 225, 0.6) 100%);
      background-size: 150% 150%;
    }
    .full-page[data-color="azure"]:after {
      background: #23CCEF;
      background: -moz-linear-gradient(top, #23CCEF 0%, rgba(64, 145, 255, 0.6) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #23CCEF), color-stop(100%, rgba(64, 145, 255, 0.6)));
      background: -webkit-linear-gradient(top, #23CCEF 0%, rgba(64, 145, 255, 0.6) 100%);
      background: -o-linear-gradient(top, #23CCEF 0%, rgba(64, 145, 255, 0.6) 100%);
      background: -ms-linear-gradient(top, #23CCEF 0%, rgba(64, 145, 255, 0.6) 100%);
      background: linear-gradient(to bottom, #23CCEF 0%, rgba(64, 145, 255, 0.6) 100%);
      background-size: 150% 150%;
    }
    .full-page[data-color="green"]:after {
      background: #87CB16;
      background: -moz-linear-gradient(top, #87CB16 0%, rgba(109, 192, 48, 0.6) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #87CB16), color-stop(100%, rgba(109, 192, 48, 0.6)));
      background: -webkit-linear-gradient(top, #87CB16 0%, rgba(109, 192, 48, 0.6) 100%);
      background: -o-linear-gradient(top, #87CB16 0%, rgba(109, 192, 48, 0.6) 100%);
      background: -ms-linear-gradient(top, #87CB16 0%, rgba(109, 192, 48, 0.6) 100%);
      background: linear-gradient(to bottom, #87CB16 0%, rgba(109, 192, 48, 0.6) 100%);
      background-size: 150% 150%;
    }
    .full-page[data-color="orange"]:after {
      background: #FFA534;
      background: -moz-linear-gradient(top, #FFA534 0%, rgba(236, 22, 87, 0.6) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #FFA534), color-stop(100%, rgba(236, 22, 87, 0.6)));
      background: -webkit-linear-gradient(top, #FFA534 0%, rgba(236, 22, 87, 0.6) 100%);
      background: -o-linear-gradient(top, #FFA534 0%, rgba(236, 22, 87, 0.6) 100%);
      background: -ms-linear-gradient(top, #FFA534 0%, rgba(236, 22, 87, 0.6) 100%);
      background: linear-gradient(to bottom, #FFA534 0%, rgba(236, 22, 87, 0.6) 100%);
      background-size: 150% 150%;
    }
    .full-page[data-color="red"]:after {
      background: #FB404B;
      background: -moz-linear-gradient(top, #FB404B 0%, rgba(187, 5, 2, 0.6) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #FB404B), color-stop(100%, rgba(187, 5, 2, 0.6)));
      background: -webkit-linear-gradient(top, #FB404B 0%, rgba(187, 5, 2, 0.6) 100%);
      background: -o-linear-gradient(top, #FB404B 0%, rgba(187, 5, 2, 0.6) 100%);
      background: -ms-linear-gradient(top, #FB404B 0%, rgba(187, 5, 2, 0.6) 100%);
      background: linear-gradient(to bottom, #FB404B 0%, rgba(187, 5, 2, 0.6) 100%);
      background-size: 150% 150%;
    }
    .full-page[data-color="purple"]:after {
      background: #9368E9;
      background: -moz-linear-gradient(top, #9368E9 0%, rgba(148, 59, 234, 0.6) 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #9368E9), color-stop(100%, rgba(148, 59, 234, 0.6)));
      background: -webkit-linear-gradient(top, #9368E9 0%, rgba(148, 59, 234, 0.6) 100%);
      background: -o-linear-gradient(top, #9368E9 0%, rgba(148, 59, 234, 0.6) 100%);
      background: -ms-linear-gradient(top, #9368E9 0%, rgba(148, 59, 234, 0.6) 100%);
      background: linear-gradient(to bottom, #9368E9 0%, rgba(148, 59, 234, 0.6) 100%);
      background-size: 150% 150%;
    }
    .full-page .footer nav > ul a:not(.btn),
    .full-page .footer .copyright,
    .full-page .footer .copyright a {
      color: #FFFFFF;
      font-size: 14px;
    }

    .login-page > .content,
    .lock-page > .content {
      padding-top: 22vh;
    }

    .login-page .card {
      box-shadow: 0 25px 30px -13px rgba(40, 40, 40, 0.4);
      border-radius: 10px;
      padding-top: 30px;
      padding-bottom: 30px;
      -webkit-transform: translate3d(0, 0, 0);
      -moz-transform: translate3d(0, 0, 0);
      -o-transform: translate3d(0, 0, 0);
      -ms-transform: translate3d(0, 0, 0);
      transform: translate3d(0, 0, 0);
    }
    .login-page .card.card-hidden {
      opacity: 0;
      -webkit-transform: translate3d(0, -60px, 0);
      -moz-transform: translate3d(0, -60px, 0);
      -o-transform: translate3d(0, -60px, 0);
      -ms-transform: translate3d(0, -60px, 0);
      transform: translate3d(0, -60px, 0);
    }
    .login-page .card .header {
      padding-bottom: 10px;
    }
    .login-page .card .btn-wd {
      min-width: 180px;
    }

    .card .header{
      padding: 15px 15px 0px;
      color: #333333;
      font-weight: 300;
      font-size: 22px;
    }
  </style>

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
