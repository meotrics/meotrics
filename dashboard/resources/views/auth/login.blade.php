@extends('layout.landing')
@section('title', 'Login')
@section('header')
	<meta name="google-signin-scope" content="profile email">
	<meta name="google-signin-client_id"
					content="102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
@endsection


@section('content')
	<section>
		<script>
			function onSignIn(googleUser) {
				var profile = googleUser.getBasicProfile();
				$.post('/auth/googlesignin', {
					id: profile.getId(),
					id_token: googleUser.getAuthResponse().id_token
				}, function (url) {
					window.location.href = url;
				});
			}
		</script>

	</section>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="login-box ">
						<div class="login-box-header">
							<a class="logo" href="{{ URL::to('/') }}">
								<img alt="Brand" src="../img/logo.png">
							</a>
							<ul class="login-navbar">
								<li><a href="{{ URL::to('/auth/register') }}">Register</a></li>
								<li class="active"><a href="">Login</a></li>
							</ul>
						</div>
						<div class="login-box-body">
							<div class="tab-content row">
								<div id="login" class="tab-pane fade in active text-center col-md-12">
									<h1 class="login-msg">Hey Buddy, welcome back!</h1>


									<form role="form" class="col-sm-8 col-sm-offset-2" method="POST"
													action="{{ URL::to('/auth/login') }}">

										<div class=" g-signin2" data-onsuccess="onSignIn"></div>
										<p> Or use your account</p>
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										@if(count($errors) > 0)
											<div class="text-danger">
												<ul>
													@foreach ($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@endif
										<div class="form-group">
											<input type="email" placeholder="Email" class="minput username"
															name="email" value="{{ old('email') }}" required>
										</div>

										<div class="form-group">
											<input type="password" class="minput psw" placeholder="Password" name="password" required>
										</div>

										<div class="form-group text-left">
											<label>
												<input type="checkbox" data-toggle="checkbox" name="remember">
												Remember me
											</label>
										</div>

										<button class="mbtn mbtn-primary" type="submit">
											<span>Login</span>
										</button>
										<i class="fa fa-chevron-right"></i>
										<p>
											Not yet got Meotrics account?
											<a class="toregister" href="{{ url('/auth/register') }}">Register</a> here
										</p>
										<p>
											<a class="" href="{{ url('/password/email') }}">Forgot Your Password?</a>
										</p>

									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
