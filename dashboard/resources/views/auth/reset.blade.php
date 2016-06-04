@extends('layout.landing')
@section('title', 'Reset password')

@section('content')
	<section>
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
					<div class="login-box ">
						<div class="login-box-header">
							<a class="logo" href="/">
								<img alt="Brand" src="../img/logo.png">
							</a>
							<ul class="login-navbar">
								<li><a href="/auth/register">Register</a></li>
								<li><a href="/auth/login">Login</a></li>
							</ul>
						</div>
						<div class="login-box-body">
							<div class="tab-content row">
								<div id="login" class="tab-pane fade in active col-sm-12">

									<form role="form" class="form-inputs col-sm-8 col-sm-offset-2" method="POST" action="/auth/reset">

										@if(isset($success))
											<h1 class="login-msg">Your email has been sent.</h1>
											<p>You can now close this window and go check your inbox for more instructions.</p>
										@else

											<h1 class="login-msg">Forgot your password ?</h1>
											@if(isset($error))
												<div class="alert alert-danger">
													<button type="button" aria-hidden="true" class="close">×</button>
													<span>{{$error}}</span>
												</div>
											@endif
											<p>Enter your email address below and we will send you password reset instructions.</p>
											<div class="form-group">
												<input type="email" placeholder="Email" class="minput email" name="email" required>
											</div>
											<div class="form-group text-left" style="margin-top: 20px;">
												<button class="button action blue" style="vertical-align: top;">
													<span class="label">Reset password</span>
												</button>
											</div>
											<p class="text-muted" style="margin-bottom: 30px; margin-top:15px">Note that we only
												able to reset your password if you login to meotrics using email &amp; password. </p>
										@endif
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
