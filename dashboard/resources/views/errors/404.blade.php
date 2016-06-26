<html>
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
  <style>
    body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      color: #B0BEC5;
      display: table;
      font-weight: 100;
      font-family: 'Lato';
      background: url(/img/error.png);
      background-repeat: no-repeat;
    }

    .container {
      text-align: center;
      display: table-cell;
      vertical-align: middle;
    }

    .content {
      text-align: center;
      display: inline-block;
    }

    .title {
      font-size: 72px;
      margin-bottom: 40px;
    }
    .height-button{
      padding: 12px !important;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="content " style="margin-top: 130px">
    <div class="col-md-12">
      <div class="col-md-4">
        <a href="/trend/dfsdf/create" class="button action blue button-radius height-button">
          <span class="label"><b>View demo</b></span>
        </a>
      </div>
      <div class="col-md-4">
        <a href="/trend/dfsdf/create" class="button action blue button-radius height-button" >
          <span class="label"><b>Yes I need it!</b></span>
        </a>
      </div>
      <div class="col-md-4">
        <a href="/trend/dfsdf/create" class="button action blue button-radius height-button">
          <span class="label"><b>Pre - Register</b></span>
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
