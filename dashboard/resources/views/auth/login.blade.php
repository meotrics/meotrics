@extends('layout.landing')
@section('title', 'Login')
@section('header')
	<meta name="google-signin-scope" content="profile email">
	<meta name="google-signin-client_id"
	      content="102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com">
	<script src="//apis.google.com/js/platform.js" async defer></script>
	<script>
		function onPageLoad(fn) {
			if (window.addEventListener)
				window.addEventListener('load', fn, false);
			else if (window.attachEvent)
				window.attachEvent('onload', fn);
		}
	</script>
@endsection

@section('content')
	<section>
		<script>

			function loading() {
				$('.signinbtn').removeClass('blue');
				$('.signinbtn').prop('type', 'button');
				$('.signinbtn').find('.label').html('Loading ...');
				$('.signinbtn').css('cursor', 'default');
			}

			function ready() {
				$('.signinbtn').addClass('blue');
				$('.signinbtn').prop('type', 'submit');
				$('.signinbtn').find('.label').html('Sign in');
				$('.signinbtn').css('cursor', 'pointer');
			}

			function error(err){
				alert("Oh snap, Meotrics just crash, please send this 'JSON.stringify(err, undefined, 2)' to support@meotrics.com, thanks");
			}

			onPageLoad(function () {
				loading();
				gapi.load('auth2', function () {
					auth2 = gapi.auth2.init({
						client_id: '102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com',
						scope: 'profile'
					});

					if (auth2.isSignedIn.get()) {
						onSignIn(auth2.currentUser.get());
						return;
					}
					else ready();

					auth2.attachClickHandler(document.getElementById('gsin'), {}, onSignIn, error);
				});
			});

			function onSignIn(googleUser) {
				var profile = googleUser.getBasicProfile();

				document.getElementById('ggmes').innerText = 'Signing in, please wait, ' + googleUser.getBasicProfile().getName();
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
				<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
					<div class="login-box ">
						<div class="login-box-header">
							<a class="logo" href="/">
								<img alt="Brand" src="/img/logo.png">
							</a>
							<ul class="login-navbar">
								<li><a href="/auth/register">Register</a></li>
								<li class="active"><a href="">Login</a></li>
							</ul>
						</div>
						<div class="login-box-body">
							<div class="tab-content row">
								<div id="login" class="tab-pane fade in active col-sm-12">

									<form role="form" class="form-inputs col-sm-8 col-sm-offset-2" method="POST" action="/auth/login">
										<h1 class="login-msg">Hey Buddy, welcome back!</h1>
										<button class=" button " id="gsin" style="vertical-align: top; background: white">
										<span class="label"> <i class="fa fa-google-plus" style="vertical-align: baseline; color: #E00000; text-shadow: none;"></i>
											<span id="ggmes" class="ml">Sign up/Sign in with Google Account</span>
											</span>
										</button>

										<h1 class="login-msg" style="margin-bottom: 0; margin-bottom: 10px; margin-top:30px">Or, use your
											password</h1>


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

										<div class="form-group text-left" style="margin-top: 20px;">
											<button class="signinbtn button action blue" style="vertical-align: top;">
												<span class="label">Sign in</span>
											</button>
											<div class="ml" style="display: inline-block; margin-left: 20px">

													<input id="rem" type="checkbox" data-toggle="checkbox" name="remember" style="margin-top: 0">
													<label for="rem" style="margin-bottom: 0px">	Remember me</label>

												<p style="margin-bottom: 0;">
													<a class="" href="/auth/reset">Forgotten Your Password?</a>
												</p>
											</div>
										</div>
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
