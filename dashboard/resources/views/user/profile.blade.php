@extends('../layout/master')
@section('title', 'User profile')

@section('style')
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>

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
  .editable-click{
      border-bottom: 0px !important;
  }
  </style>
@endsection

@section('script')
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

  <script>
    $(document).ready(function(){
      $('[xeditable]').editable({ 
          url: '/user/profile',
        });
        
        $('body').on('click', '.a-edit, .a-edit-username ', function(e){
            e.stopPropagation();
            var ele_edit = $(this).prev();
            ele_edit.editable('toggle');
        });
    });
  </script>
@endsection

@section('content')

  <div class="bs-callout bs-callout-primary" id="user_profile">
    <h4>Profile management</h4>
    <div class="content">
      <h3 class="avatar">
        <i class="pe-7s-user"></i>
        <a class="text-info"  href="#" xeditable data-type="text" data-name="name"
           data-mode="inline" data-showbuttons="false" data-pk="{{ Auth::user()->id }}">{{ Auth::user()->name }}</a>
        <a href="javascript:void(0)" class="a-edit-username" data-name="a-edit">
          <span class="glyphicon glyphicon-pencil"></span>
        </a>
      </h3>
      <div class="form-group">
        <span class="col-xs-2 control-label">UserID</span>
        <div class="col-xs-10">
          <b>{{ Auth::user()->id }}</b>
        </div>
      </div>
      <div class="form-group">
        <span class="col-xs-2 control-label">Email</span>
        <div class="col-xs-10">
          <b>{{ Auth::user()->email }}</b>
        </div>
      </div>
      <div class="form-group">
        <span class="col-xs-2 control-label">Phone number</span>
        <div class="col-xs-10">
          <a href="#" xeditable data-type="text" data-name="phone"
             data-mode="inline" data-showbuttons="false" data-pk="{{ Auth::user()->id }}">{{ Auth::user()->phone}}</a>
            <a href="javascript:void(0)" class="a-edit" data-name="a-edit">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
        </div>
      </div>
      <div class="form-group">
        <span class="col-xs-2 control-label">Password</span>
        <div class="col-xs-10">
          <b>************</b>
          <!--<a class="btn btn-xs btn-info" href="{{ URL::to('/user/password') }}">Change</a>-->
          <a href="{{ URL::to('/user/password') }}" class="a-edit" data-name="a-edit">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
          @if(session('success'))
            <span class="text-success">
              {{ session('success') }}
            </span>
          @endif
        </div>
      </div>
      <br>
    </div>
  </div>

@endsection