@extends('../layout/landing')
@section('title', 'Register')
@section('header')
	<meta name="google-signin-scope" content="profile email">
	<meta name="google-signin-client_id"
	      content="102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com">
@endsection
@section('content')
	<section>
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
					<div class="login-box" id="register">
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

										<button class=" button action red" type="button" id="gsin" style="vertical-align: top;width: 100%;border-radius: 16px;">
										<span class="label">
											<span id="ggmes" class="ml">Sign up with Google</span>
											</span>
										</button>
										{{--<button class="button action red" type="button" id="gsin" style="vertical-align: top;border-radius: 16px;padding-left: 14px;padding-right: 14px;margin-bottom: 10px;">--}}
												{{--<span class="label">--}}
													{{--<i class="fa fa-google-plus"--}}
													{{--style="vertical-align: baseline; text-shadow: none;"></i>--}}
													{{--<span id="ggmes" >Google</span>--}}
												{{--</span>--}}
										{{--</button>--}}
										<p style="margin-top: 20px">------------------------- Or sign up with -------------------------</p>
										<div class="siteinfo">
											<div class="form-group">
												<input type="email" class="minput email" placeholder="Email"
													   name="email" v-model="info.email">
											</div>
											<div class="form-group " >
												<div class="input-group">
													<span class="input-group-addon ">
														<select v-model="codephone" :value="codephone" name="codephone"  style="width: 17px">
															<option v-for="item in listcode" :value="item.code">@{{item.name}}</option>
														</select>
														@{{codephone}}
													</span>
													<input type="number" placeholder="Phone" class="minput username"
														   name="phone" v-model="info.phone">
												</div>
											</div>
											<div class="form-group">
												<input type="text" placeholder="Site Address (http://example.com)" class="minput psw"
												       name="siteurl" v-model="info.website">
											</div>
											<div class="form-group " >
												{{--<div class="form-group  text-left">--}}
													{{--<p>I am: </p>--}}
												{{--</div>--}}
												<div>
													<select class=" input-sm minput" style="background: #f6f7fa;" name="job" v-model="info.job" >
														<option>CMO</option>
														<option>Marketing Executive</option>
														<option>Sale</option>
														<option>Seo</option>
														<option>Developer</option>
														<option>Other</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group  text-left">
											<div class='form-group text-left' >
												<input type='checkbox' id='input4' name="radio"  v-model="info.agree" required>
												<label for="input4"><strong class="text-left">
														I agree with Meotrics&rsquo;s
														<a href="" data-toggle="modal" data-target="#privacy_dialog">Policy</a>
														and
														<a href="" data-toggle="modal" data-target="#terms_dialog">Terms</a>
													</strong></label>
											</div>
											<div>
												<label style="color: red" v-if="errors">@{{message}}</label>
											</div>

											<div style="text-align: center">

												<div class="form-group" style="margin-top: 20px; margin-bottom: 0px">
													<button class="signinbtn button action blue" :disabled="errors"  style="vertical-align: top;width:100%; border-radius: 16px;padding-left: 14px;padding-right: 14px;margin-bottom: 10px;">
														<span class="label">Sign up</span>
													</button>
												</div>

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
		<script src="//apis.google.com/js/platform.js" async defer></script>
		<script src="/js/vendor.js"></script>
		<script src="/js/register/phonecode.js"></script>
		<script src="/js/register/index.js"></script>
	</section>
	@include('partials/privacy')
	@include('partials/terms')
@endsection
