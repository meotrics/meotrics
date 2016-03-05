<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="icon" type="image/png" href="/favicon.ico">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>@yield('title')</title>

  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
  <meta name="viewport" content="width=device-width" />

  <!-- Bootstrap core CSS     -->
  <link href="/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/animate.min.css" rel="stylesheet"/>
  <link href="/css/light-bootstrap-dashboard.css" rel="stylesheet"/>

  <!--     Fonts and icons     -->
  <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
  <link href="/css/pe-icon-7-stroke.css" rel="stylesheet" />
  <link href="/css/app.css" rel="stylesheet" />

</head>
<body>

  <div class="wrapper">
    <div class="sidebar" data-color="black" data-image="/img/sidebar-4.jpg">
      <!--
      Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple"
      Tip 2: you can also add an image using data-image tag
    -->

    <div class="sidebar-wrapper">
      <div class="logo">
        <a href="/"> <img src="{{asset('img/logo.png')}}" width="30px"/></a>
        <span class="logo-text">meotrics</span>
      </div>

      <ul class="nav">
        <li class="active">
          <a href="dashboard.html">
            <i class="pe-7s-graph"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li>
          <a href="user.html">
            <i class="pe-7s-user"></i>
            <p>Trend</p>
          </a>
        </li>
        <li>
          <a href="user.html">
            <i class="pe-7s-user"></i>
            <p>Segmentation</p>
          </a>
        </li>

        <li class="active-pro">
          <a href="upgrade.html">
            <i class="pe-7s-rocket"></i>
            <p>Upgrade to PRO</p>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="main-panel">
    <nav class="navbar navbar-default navbar-fixed">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Dashboard</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-left">
            <li>
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-dashboard"></i>
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-globe"></i>
                <b class="caret"></b>
                <span class="notification">5</span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="#">Notification 1</a></li>
                <li><a href="#">Notification 2</a></li>
                <li><a href="#">Notification 3</a></li>
                <li><a href="#">Notification 4</a></li>
                <li><a href="#">Another notification</a></li>
              </ul>
            </li>
            <li>
              <a href="">
                <i class="fa fa-search"></i>
              </a>
            </li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="">
                Account
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                Dropdown
                <b class="caret"></b>
              </a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </li>
            <li>
              <a href="#">
                Log out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="content">
      @yield('content')
    </div>

    <footer class="footer">
      <div class="container-fluid">
        <nav class="pull-left">
          <ul>
            <li>
              <a href="#">
                Home
              </a>
            </li>
            <li>
              <a href="#">
                Company
              </a>
            </li>
            <li>
              <a href="#">
                Portfolio
              </a>
            </li>
            <li>
              <a href="#">
                Blog
              </a>
            </li>
          </ul>
        </nav>
        <p class="copyright pull-right">
          &copy; 2016 <a href="http://meotrics.com">Meotrics</a>, made with love for a better insight
        </p>
      </div>
    </footer>

  </div>


  <script src="/js/jquery-1.10.2.js" type="text/javascript"></script>
  <script src="/js/bootstrap.min.js" type="text/javascript"></script>

  <!--  Checkbox, Radio & Switch Plugins -->
  <script src="/js/bootstrap-checkbox-radio-switch.js"></script>
  <script src="/js/chartist.min.js"></script>

  <!--  Notifications Plugin    -->
  <script src="/js/bootstrap-notify.js"></script>
  <script src="/js/light-bootstrap-dashboard.js"></script>

  <script type="text/javascript">
  $(document).ready(function(){

    $.notify({
      icon: 'pe-7s-gift',
      message: "Welcome to <b>Meotrics Dashboard</b> - a beautiful dashboard for every marketer."

    },{
      type: 'info',
      timer: 4000
    });

  });
  </script>
</body>
</html>
