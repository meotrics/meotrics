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

									<form role="form" class="form-inputs col-sm-8 col-sm-offset-2" method="POST" action="/auth/newpw">
										<h1 class="login-msg">Choose your password </h1>

										<p>Please enter your new password below</p>
										<div class="form-group">
											<input type="password" placeholder="New password" class="minput psw"
											       name="password" required>

										</div>
										<div class="form-group"><input type="password" placeholder="Confirm" class="minput psw"
										                               name="pwconf" value="{{ old('email') }}" required>
										</div>
										<div class="form-group text-left" style="margin-top: 20px;margin-bottom: 30px;">
											<button class="button action blue" style="vertical-align: top;">
												<span class="label">Save change</span>
											</button>
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
