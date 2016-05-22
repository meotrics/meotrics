@extends('../layout/landing')
@section('title', 'Register')
@section('header')
	<meta name="google-signin-scope" content="profile email">
	<meta name="google-signin-client_id"
	      content="102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
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
								<li class="active"><a href="">Register</a></li>
								<li><a href="{{ URL::to('/auth/login') }}">Login</a></li>
							</ul>
						</div>
						<div class="login-box-body">
							<div class="tab-content row">
								<div id="register" class="tab-pane active text-center col-md-12">
									<h1 class="login-msg">Hey Buddy, ready to take your adventure?</h1>

									<div>
										<div class=" g-signin2" data-onsuccess="onSignIn"></div>
									</div>


									<form role="form" class="col-sm-8 col-sm-offset-2" method="POST" action="{{ url('/auth/register') }}">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										@if (count($errors) > 0)
											<div class="text-danger">
												<ul>
													@foreach ($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@endif
										<div class="form-group">
											<input type="text" placeholder="Display name" class="minput username"
											       name="name" value="{{ old('name') }}" required>
										</div>

										<div class="form-group">
											<input type="email" class="minput email" placeholder="Email"
											       name="email" value="{{ old('email') }}" required>
										</div>

										<div class="form-group">
											<input type="password" class="minput psw" placeholder="Password"
											       name="password" required>
										</div>

										<div class="form-group">
											<input type="password" class="minput psw" placeholder="Password confirmation"
											       class="form-control" name="password_confirmation" required>
										</div>

										<div class="form-group">
											<div class="checkbox">
												<label>
													<input type="checkbox" value="check" required>
													<strong class="text-left">
														By click on this box you agree with Meotrics&rsquo;s
														<a href="" data-toggle="modal" data-target="#privacy_dialog">Policy</a>
														and
														<a href="" data-toggle="modal" data-target="#terms_dialog">Terms</a>
													</strong>
												</label>
											</div>
										</div>
										<button class="mbtn mbtn-primary" type="submit">
											<span>I am ready</span>
										</button>
										<i class="fa fa-chevron-right" style="font-size:"></i>
										<br><br>
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