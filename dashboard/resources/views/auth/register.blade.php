@extends('../layout/landing')
@section('title', 'Register')
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
@section('style')
	<style>
		/* Docs*/

		.docs {
			background: #f6f7fa;
		}

		.doc {
			padding: 10px;
		}

		.doc .content {
			font-size: 16px;
		}

		.doc h1,
		.doc .title {
			text-align: center;
			font-size: 32px;
			font-weight: bold;
			margin-bottom: 30px
		}

		.doc .content h2 {
			margin: 15px 0;
			text-transform: uppercase;
			font-size: 22px;
			font-weight: 500;
		}

		.doc .content h3 {
			font-size: 16px;
		}
	</style>
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
			function loading() {
				$('.signinbtn').removeClass('blue');
				$('.signinbtn').prop('type', 'button');
				$('.signinbtn').find('.label').html('Loading ...');
				$('.signinbtn').css('cursor', 'default');
			}

			function ready() {
				$('.signinbtn').addClass('blue');
				$('.signinbtn').prop('type', 'submit');
				$('.signinbtn').find('.label').html('Sign up');
				$('.signinbtn').css('cursor', 'pointer');
			}

			function error(err) {
				alert("Oh snap, Meotrics just crash, please send this 'JSON.stringify(err, undefined, 2)' to support@meotrics.com, thanks");
			}

			onPageLoad(function () {

					$('#newsite').prop('checked', true);
					$('#oldsite').removeAttr('checked');


				loading();
				gapi.load('auth2', function () {
					auth2 = gapi.auth2.init({
						client_id: '102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com',
						scope: 'profile'
					});

					auth2.then(function(){

					if (auth2.isSignedIn.get()) {
						onSignIn(auth2.currentUser.get());
						return;
					}
					else ready();

					auth2.attachClickHandler(document.getElementById('gsin'), {}, onSignIn, error);
					});
				})
			});

			function changePurpose(radio) {
				if (radio.value == "2") {
					$('.siteinfo').addClass('hidden');
					$('input[name="sitename"]').removeProp('required');
					$('input[name="siteurl"]').removeProp('required');

				}
				else {
					$('.siteinfo').removeClass('hidden');
					$('input[name="sitename"]').prop('required',true);
					$('input[name="siteurl"]').prop('required', true);
				}
			}

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
							<a class="logo" href="{{ URL::to('/') }}">
								<img alt="Brand" src="../img/logo.png">
							</a>
							<ul class="login-navbar">
								<li class="active"><a href="">Register</a></li>
								<li><a href="{{ URL::to('/auth/login') }}">Login</a></li>
							</ul>
						</div>
						<div class="login-box-body">
							<div class="tab-content row">
								<div id="register" class="tab-pane active text-center col-xs-12">
									<form role="form" class="form-inputs col-xs-8 col-xs-offset-2 " method="POST"
									      action="{{ url('/auth/register') }}" style="margin-bottom: 30px">
										<h1 class="login-msg text-left">Ready for your adventure ?</h1>
										<div class="text-left">
											<div class='form-group '>
												<input type='radio' id='newsite' name='radio' onclick="changePurpose(this)" checked value="1">
												<label for="newsite">I would like to track a new site</label>
												<br/>
												<input type='radio' id='oldsite' name='radio' onclick="changePurpose(this)" value="2">
												<label for="oldsite">I&rsquo;m joining partner&rsquo;s site</label>
											</div>
										</div>

										<div class="siteinfo">
											<div class="form-group">
												<input type="text" placeholder="Site name" class="minput username"
												       name="sitename" value="{{ old('name') }}" required>
											</div>
											<div class="form-group">
												<input type="text" placeholder="Site Address (http://example.com)" class="minput psw"
												       name="siteurl" value="{{ old('name') }}" required>
											</div>
										</div>

										<div class="form-group  text-left">
											<div class="form-group">
												<input type="email" class="minput email" placeholder="Email"
													   name="email" value="{{ old('email') }}" required>
											</div>

											<div class='form-group text-left' >
												<input type='checkbox' id='input4' required>
												<label for="input4"><strong class="text-left">
														I agree with Meotrics&rsquo;s
														<a href="" data-toggle="modal" data-target="#privacy_dialog">Policy</a>
														and
														<a href="" data-toggle="modal" data-target="#terms_dialog">Terms</a>
													</strong></label>
											</div>

											<div style="text-align: center">

												<div class="form-group " style="margin-top: 20px; margin-bottom: 0px">
													<button class="signinbtn button action blue" style="vertical-align: top;border-radius: 16px;padding-left: 14px;padding-right: 14px;margin-bottom: 10px;">
														<span class="label">Sign up</span>
													</button>
												</div>

												<p>------------------------- Or sign up with -------------------------</p>

												<button class="button action red" type="button" id="gsin" style="vertical-align: top;border-radius: 16px;padding-left: 14px;padding-right: 14px;margin-bottom: 10px;">
												<span class="label">
													{{--<i class="fa fa-google-plus"--}}
																		{{--style="vertical-align: baseline; text-shadow: none;"></i>--}}
													<span id="ggmes" >Google</span>
												</span>
													</button>

													<input type="hidden" name="_token" value="{{ csrf_token() }}">
													@if (isset($error))
														<div class="text-danger">

																	<p>{{ $error }}</p>

														</div>
												</div>
											@endif
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
	@include('partials/privacy')
	@include('partials/terms')
@endsection