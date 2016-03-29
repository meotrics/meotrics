@extends('../layout/landing')
@section('title', 'Forgot password')

@section('content')
<br><br><br>

<div class="container">
	<div class="row">
		<div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3">
			<form method="POST" action="{{ URL::to('/password/email') }}">
		    <div class="card">
		      <h3 class="header text-center">Reset password</h3>
		      <br>
		      <div class="content">
		      	@if (session('status'))
							<div class="alert alert-success">
								{{ session('status') }}
							</div>
						@endif
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
		          <input type="email" placeholder="Enter email" class="form-control"
		          			 name="email" value="{{ old('email') }}" required>
		        </div>

		      </div>
		      <div class="text-center">
		        <button type="submit" class="btn btn-fill btn-primary btn-wd">Send Password Reset Link</button>
		        <br><br>
		        <center>
		        	<a class="text-muted" href="{{ url('/auth/login') }}">
		        		<i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to login
	        		</a>
		        </center>
		      </div>
		    </div>
		  </form>
		</div>                    
	</div>
</div>
@endsection
