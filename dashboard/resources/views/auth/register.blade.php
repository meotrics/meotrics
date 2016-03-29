@extends('../layout/landing')
@section('title', 'Register')

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

		/* Docs*/

    .docs{
      background: #f6f7fa;
    }

    .doc{
      padding: 10px;
    }

    .doc .content{
      font-size: 16px;
    }

    .doc h1,
    .doc .title{
      text-align: center;
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 30px
    }

    .doc .content h2{
      margin: 15px 0;
      text-transform: uppercase;
      font-size: 22px;
        font-weight: 500;
    }

    .doc .content h3{
      font-size: 16px;
    }
	</style>
@endsection

@section('content')

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