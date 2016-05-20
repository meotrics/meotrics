@extends('layout.main')
@section('header-script')
	<script>
		var userid = '{{$userid}}';
		var appcode = '{{$appcode}}';

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

		var _helper = {
			notification: {
				error: function (err, options) {

					options = options || {};
					options.type = 'danger';
					options.timer = 3000;
					options.placement = {
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
					options.timer = 3000;
					options.placement = {
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

	<script>
		window.odometerOptions = {
			duration: 350
		};

		function update_action_count() {
			var data = websock.data[appcode].action_count;
			if (odometer.length !== undefined) {
				for (var i in odometer) if (odometer.hasOwnProperty(i))odometer[i].innerHTML = data;
			}
			else odometer.innerHTML = parseInt(data);
		}

		//websock.appChange(appcode, 'action_count', update_action_count);
		//update_action_count();
	</script>
@endsection
@section('body')

	<div class="wrapper">
		<div class="sidebar main_sidebar" data-color="blue" data-image="/img/sidebar-4.jpg">
			<!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->
			<div class="sidebar-wrapper">
				<div class="logo">
					<a class="simple-text" href="{{ URL::to('/') }}">
						<img src="{{ asset('img/logo.png') }}" width="30px"/>
						<span class="logo-text">Meotrics</span>
					</a>
				</div>
				<ul class="nav">
					<li class="{{ Route::getCurrentRoute()->getPath() == 'home' ? 'active' : '' }}">
						<a href="/{{ $appcode }}">
							<i class="pe-7s-graph"></i>
							<p>Dashboard</p>
						</a>
					</li>
					<li class="{{ Route::getCurrentRoute()->getPath() == 'trend' ? 'active' : '' }}">
						<a href="/trend/{{$appcode}}">
							<i class="pe-7s-graph1"></i>
							<p>Trend</p>
						</a>
					</li>
					<li class="{{ Route::getCurrentRoute()->getPath() == 'segment' ? 'active' : '' }}">
						<a href="/segment/{{$appcode}}">
							<i class="pe-7s-users"></i>
							<p>Segmentation</p>
						</a>
					</li>
					<li class="{{ Route::getCurrentRoute()->getPath() == 'funnel' ? 'active' : '' }}">
						<a href="/funnel/{{$appcode}}">
							<i class="pe-7s-filter"></i>
							<p>Funnel</p>
						</a>
					</li>
					<li class="{{ Route::getCurrentRoute()->getPath() == 'revenue' ? 'active' : '' }}">
						<a href="/revenue/{{$appcode}}">
							<i class="pe-7s-cash"></i>
							<p>Revenue</p>
						</a>
					</li>
					<li class="{{ Route::getCurrentRoute()->getPath() == 'campaign' ? 'active' : '' }}">
						<a href="/campaign/{{$appcode}}">
							<i class="pe-7s-graph3"></i>
							<p>Marketing Campaign</p>
						</a>
					</li>
					<li class="{{ Route::getCurrentRoute()->getPath() == 'insight' ? 'active' : '' }}">
						<a href="/insight/{{$appcode}}">
							<i class="pe-7s-user"></i>
							<p>User Profile</p>
						</a>
					</li>

					<!-- <li class="user-area">
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
					</li> -->
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
					</div>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-left">
							@yield('action')
											<!-- <li class="dropdown">
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
						</li> -->
						</ul>

						<ul class="nav navbar-nav navbar-right">
							<li>
								<a href="#">
									<span class="vam">Action count:</span>
									<span id="odometer" class="vam id_counter odometer"></span>
								</a>

							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<span class="vam">	{{ $appname }}</span>
									<b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#" class="id_trackingcode">Get tracking code</a></li>
									<li><a href="{{ URL::to('/user/profile') }}">Profile</a></li>

									<li><a href="{{ URL::to('/actiontype/'.$appcode) }}">Action types</a></li>
									<li class="divider"></li>
									<li><a href="{{ URL::to('/auth/logout') }}">Logout</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>


			<div class="content">
				<div class="container-fluid">
					@yield('content')
				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer-script')
	<script>

		onPageLoad(function () {
			$('.id_trackingcode').click(function () {
				showCodeDialog('{{$appcode}}');
			});
		});
		var config = {
			customOpenAnimation: function (cb) {
				$(this).fadeIn(300, cb);
			},
			customCloseAnimation: function (cb) {
				$(this).fadeOut(300, cb);
			}
		};

	</script>

	@yield('additional')
	@yield('script')
	@include('partials/install_guide')
@endsection
