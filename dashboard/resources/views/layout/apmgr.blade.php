@extends('layout.main')
@section('header-script')
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
@endsection
@section('body')
	<div class="wrapper">
		<div class="">
			<nav class="navbar navbar-default navbar-fixed">
				<div class="container">
					<div class="navbar-header">
						<div class="row"><a class="" href="#"><img style="margin-top: 12px" height="25px" src="/img/meotrics-alt.png"/></a>
							</div>
					</div>

					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-left">
							@yield('action')
						</ul>

						<ul class="nav navbar-nav navbar-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<span class="vam">	{{ \Auth::user()->name}}</span>
									<b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
									<li><a href="{{ URL::to('/user/profile') }}">Profile</a></li>
									<li class="divider"></li>
									<li><a href="{{ URL::to('/auth/signout') }}">Logout</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>


			<div class="content">
				<div class="container">
					@yield('content')
				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer-script')
	@yield('additional')
	@yield('script')
	@include('partials.install_guide')
@endsection
