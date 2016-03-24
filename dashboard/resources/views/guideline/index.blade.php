@extends('../layout/master', ['sidebarselect' => 'guideline'])
@section('title', 'Guideline')

<?php
  $sidebar = false;
  $navbar = false;
?>

@section('script')
  <script>
    function copyScript(){
      var el = $('#guideline').find('textarea');
      el.select();
      document.execCommand('selectAll');
      document.execCommand('copy');
    }
  </script>
@endsection

@section('style')
  <link rel="stylesheet" href="{{asset('css/guideline.css')}}"/>
  <style>
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
  <div id="guideline">
    <div id="step_1">
      <h5>
        Make sure to add this script to your site
      </h5>
      <div class="well well-sm meotrics_script">
        <button class="btn btn-success copy-btn" onclick="copyScript()">
          Copy
        </button>
        <textarea onkeydown="return false">
          //define meotrics for queue
          var Meotrics = Meotrics || {};

          Meotrics.appid = "1234";

          Meotrics.__requestQueue= Meotrics.__requesQueue || [];

          //TODO: problem when clone callback function, we dont want clone callback function.
          //2 level deep clone object
          Meotrics.__clone = function (obj)
          {
            if (null == obj || "object" != typeof obj) return obj;
            var copy = obj.constructor();
              for (var attr in obj) if (obj.hasOwnProperty(attr)) copy[attr] = Meotrics.__shallowclone(obj[attr]);
              return copy;
          }

          Meotrics.__shallowclone = function (obj) {
            if (null == obj || "object" != typeof obj) return obj;
            var copy = obj.constructor();
              for (var attr in obj) if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
              return copy;
          }

          if(Meotrics.record === undefined) {
            Meotrics.record = function(){
              //clone arguments
              var newarguments = [];
              for(var i in arguments)
                newarguments.push(Meotrics.__clone (arguments[i]));
              Meotrics.__requestQueue.push({arguments: newarguments, time: new Date()});
            }
          }

          Meotrics.__callbackQueue = Meotrics.__callbackQueue || [];
          Meotrics.getMtId  = function(callback){
            Meotrics.__callbackQueue.push(callback);
          }

          var script = document.createElement("script");
          script.type = "text/javascript";
          script.src = "meotrics.dev/mt.js";
          //script.onreadystatechange = callbacl;
          //script.onload = callback;
          document.body.appendChild(js);
        </textarea>
      </div>
    </div>
  </div>

@endsection
