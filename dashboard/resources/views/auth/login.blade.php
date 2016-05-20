@extends('layout.landing')
@section('title', 'Login')
@section('header')
	<meta name="google-signin-scope" content="profile email">
	<meta name="google-signin-client_id" content="102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>

	@endsection
@section('style')
	<style>
		section {
		    padding-top: 40px;
		}
		/**
		 * Login Modal
		 */


		.modal-content{
			border-radius: 0;
		}

		.login-box {
			background: #f6f7fa;
			-moz-box-shadow:    0 50px 150px -40px #5584ff;
			-webkit-box-shadow: 0 50px 150px -40px #5584ff;
			box-shadow: 0 50px 150px -40px #5584ff;
		}

		.login-box .logo > img{
			height: 40px;
		}

		.login-box .login-box-header{
			padding: 15px;
			background-color: #0e1a35;
		}

		.login-box .login-navbar {
			margin-top: -15px;
			margin-right: -15px;
			float: right;
			padding-left: 0;
		}

		.login-box .login-navbar>li {
			float: left;
		    display: block;
		}

		.login-box .login-navbar>li>a {
			width: 100px;
		    display: block;
		    color: #fff;
		    text-align: center;
		    padding: 25px 15px 20px 15px;
		    border-bottom: 5px solid transparent;
		    text-decoration: none;
		}

		.login-navbar>li.active>a, .login-navbar>li.active>a:focus, .login-navbar>li.active>a:hover{
			border-bottom: 5px solid #5584ff;
			color: #5584ff;
		}

		#login > h1,
		#register > h1{
			font-weight: 300;
			padding: 10px 0;
			font-size: 28px;
			color: #8492af;
		}


		#login > form input,
		#register > form input {
			background-size: 15px;
		    text-indent: 30px;
		}

		#login > form > button,
		#register > form > button{
			width: 180px;
			margin-top: 30px;
			margin-bottom: 10px;
		}

		#login > form > i,
		#register > form > i{
			color: #fff;
			font-size: 8px;
			margin-left: -20px;
		}

		.username {
			background: url("../img/form-user-icon.png") no-repeat 10px center;
		}

		.email {
			background: url("../img/form-email-icon.png") no-repeat 10px center;
		}
		.psw {
			background: url("../img/form-password-icon.png") no-repeat 10px center;
		}
	</style>
@endsection

@section('content')
<section>

	<script>
		function onSignIn(googleUser) {

			$.post('/auth/googletoken', {id: profile.getId(), eidtoken:id_token }, function(url){
				window.location.href= url;
			});

			console.log(profile);
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



								<form role="form" class="col-sm-8 col-sm-offset-2" method="POST" action="{{ URL::to('/auth/login') }}">

									<div class=" g-signin2" data-onsuccess="onSignIn" ></div>
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
