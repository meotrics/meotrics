@extends('../layout/landing')
@section('title', 'Register')

@section('content')

<div class="row">                   
	<div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3">
	 	<form method="POST" action="{{ url('/auth/register') }}">
	    <div class="card">
	      <div class="header text-center">Register</div>
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
	          <label>Name</label>
	          <input type="text" placeholder="Enter name" class="form-control"
	          			 name="name" value="{{ old('name') }}" required>
	        </div>
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
						<label>Confirm Password</label>
						<input type="password" placeholder="Password confirmation"
									 class="form-control" name="password_confirmation">
					</div>
	      </div>
	      <div class="footer text-center">
	        <button type="submit" class="btn btn-fill btn-info btn-wd">Register</button>
	        <center>
	        	Already have an account? <a class="" href="{{ url('/auth/login') }}">Log in now</a>
	        </center>
	      </div>
	    </div>
	  </form>
	</div>                    
</div>
@endsection
