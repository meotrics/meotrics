@extends('../layout/master')
@section('title', 'Change password')

@section('style')
  <style>
  .form-group{
    margin: 0;
    overflow: hidden;
    padding-bottom: 10px;
  }
  #user_profile{
    padding: 10px 20px;
  }
  #user_profile .avatar > *{
    vertical-align: middle;
  }
  #user_profile .avatar i{
    font-size: 40px;
  }
  #user_profile .avatar i + span{
    font-size: 20px;
    margin: 0 10px;
    line-height: 40px;
  }
  .editable-input input{
    height: auto;
  }
  .avatar .form-group{
    padding: 0;
  }
  </style>
@endsection

@section('script')
  
@endsection

@section('content')

  <div class="bs-callout bs-callout-primary" id="user_profile">
    <h4>Change password</h4>
    <form class="content" method="POST" action=""> 
      <br>
      @if (count($errors) > 0)
        <div class="text-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <br>
      <div class="form-group">
        <span class="col-lg-2 control-label">Current password</span>
        <div class="col-lg-10">
          <input type="password" class="form-control input-sm"
                 name="current_password" placeholder="Current password" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <span class="col-lg-2 control-label">New password</span>
        <div class="col-lg-10">
          <input type="password" class="form-control input-sm"
                 name="password" placeholder="New password" required>
        </div>
      </div>
      <div class="form-group">
        <span class="col-lg-2 control-label">Password confirmation</span>
        <div class="col-lg-10">
          <input type="password" class="form-control input-sm"
                 name="password_confirmation" placeholder="Re-enter password" required>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-10 col-lg-offset-2">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
      <br>
    </form>
  </div>

@endsection