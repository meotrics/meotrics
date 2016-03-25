@extends('../layout/landing')
@section('title', 'Login')

@section('content')

<div class="row">                   
	<div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3">
		<form method="POST" action="{{ URL::to('/auth/login') }}">
	    <div class="card">
	      <div class="header text-center">Login</div>
	      <div class="content">
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
	          <label>Email address</label>
	          <input type="email" placeholder="Enter email" class="form-control"
	          			 name="email" value="{{ old('email') }}" required>
	        </div>
	        <div class="form-group">
	          <label>Password</label>
	          <input type="password" placeholder="Password" class="form-control"
	          			 name="password" required>
	        </div>                                    
	        <div class="form-group">
	          <label class="checkbox">
	            <span class="icons">
	            	<span class="first-icon fa fa-square-o"></span>
	            	<span class="second-icon fa fa-check-square-o"></span>
	          	</span>
	          	<input type="checkbox" data-toggle="checkbox" name="remember">
	            Remember me
	          </label>    
	        </div>
	      </div>
	      <div class="footer text-center">
	        <button type="submit" class="btn btn-fill btn-info btn-wd">Login</button>
	        <center>
	        	Don't have an account yet? <a class="" href="{{ url('/auth/register') }}">Register</a>
	        </center>
	        <center>
	        	<a class="" href="{{ url('/password/email') }}">Forgot Your Password?</a>
	        </center>
	      </div>
	    </div>
	  </form>
	</div>                    
</div>
@endsection
