@extends('../layout/master', ['sidebarselect' => 'guideline'])
@section('title', 'Guideline')

<?php
  $sidebar = false;
  $navbar = false;
?>

@section('script')
  <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.js"></script>
  <script>
    var APP_ID = '2222'; // TODO: Fill app id to this line
    function copyScript(){
      var el = $('#guideline').find('textarea');
      el.select();
      document.execCommand('selectAll');
      document.execCommand('copy');
      toastr.success('', 'Copied !', {
        positionClass: 'toast-bottom-right'
      });
    }
    $.ajax({
      method: 'GET',
      url: '/mt.code',
      success: function(response){
        $('#guideline').find('textarea').html(response.replace(RegExp('<MT_APP_ID>', 'g'), APP_ID));
      },
      error: function(err){
        toastr.error(err);
      }
    })
  </script>
@endsection

@section('style')
  <link rel="stylesheet" href="{{asset('css/guideline.css')}}"/>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css" />
  <style>
    #guideline .modal-content{
      overflow: hidden;
    }
    #guideline .meotrics_script{
      width: 100%;
      height: auto;
      min-width: 50%;
      min-height: 50vh;
      position: relative;
    }
    #guideline .meotrics_script .copy-btn{
      position: absolute;
      top: 5px;
      right: 5px;
      z-index: +1;
    }
    #guideline .meotrics_script textarea{
      position: absolute;
      padding: 9px;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: none;
      border: none;
      font-family: monospace;
      resize: none;
      font-size: 12px;
      outline: none !important;
    }
  </style>
@endsection

@section('content')
  <div class="modal show" id="guideline">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">First thing you need to do, make sure to add this script to your site</h5>
        </div>
        <div class="modal-body">
          <div class="well well-sm meotrics_script">
            <button class="btn btn-success copy-btn" onclick="copyScript()">
              Copy
            </button>
            <textarea onkeydown="return false" placeholder="Loading..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn btn-success" href="{{ URL::to('/') }}">Go to Dashboard</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

@endsection
