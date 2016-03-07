@extends('../layout/master', ['sidebarselect' => 'segment'])

@section('script')
<script>

function loadSegment(segment)
{

}



$('.addactionlink').click(function(){
  var $segment = $('.id_segmenttem').children().clone();
  var $list = $('.id_segmentlist');
  $list.append($segment);
});

function newSegment()
{
  var $segment = $('.id_segmenttem').children().clone();
  var $list = $('.id_segmentlist');
  $list.empty();


  $segment.find('.removelink').click(function(){
    $segment.addClass('hidden');
  });

  var $segcondlist = $segment.find('.subconditionlist');
  $segment.find('.addcondlink').click(function()
  {
    var $cond = $('.id_condtemp').children().clone();

    $cond.find('.removecondlink').click(function(){
      $cond.addClass('hidden');
    });
    $segcondlist.append($cond);
  });

  $list.append($segment);
}


function segment_change(segment){

};
var $list = $('.id_segmentlist');
$list.empty();
newSegment();

</script>
@endsection



@section('content')
<div class="card row">
  <div class="header col-md-12">
    <form class="">
      <label class="mr5">SEGMENTATION SELECT</label>
      <select id="segmentselect" class="form-control mr" style="width: 250px; display:inline-block">
        <option value="56dab10c44aee0d1bd499a29">Purchase</option>
        <option value="56dab10544aee0d1bd499a27">Pageview</option>
      </select>
      <!-- <a class="id_querytrend btn btn-fill btn-success mr "><i class="fa fa-play" style="font-size: 15px; vertical-align: middle"></i> <span style="vertical-align: middle">Query</span></a> -->
      <a class="btn">  <i class="fa fa-floppy-o" style="font-size: 18px;padding-top: 4px;vertical-align: middle"></i></a>
      <a class="id_querytrend btn "><i class="fa fa-trash-o" style="font-size: 19px;vertical-align: middle"></i></a>
    </form>

  </div>
  <div class="content col-sm-12">

    <div class="id_segmentlist">

    </div>
    <a href='#' class="addactionlink">and/or</a>


    <div>
      <label>Filter by </label>
      <select class="fieldselect form-control " style="width:150px; display: inline-block">
        <option value="pid">Product ID</option>
        <option value="cid">Category ID</option>
      </select>
      <select class="fieldselect form-control " style="width:150px; display: inline-block">
        <option value="pid">Product ID</option>
        <option value="cid">Category ID</option>
      </select>
      <button>Excute</button>
      <button>Save </button>
    </div>

    <div class="hidden id_segmenttem">
      <div class="">
        <label>Has done</label> <select class="id_actionselect form-control" style="width:150px; display: inline-block">
          <option value="pageview">Pageview</option>
          <option value="purchase">Purchase</option>
        </select>
        <label> which </label>
        <div style="display: inline-block">
          <select class="id_fselect form-control" style="width:120px; display: inline-block">
            <option value="count">No. occurs</option>
            <option value="sum">Total</option>
            <option value="avg">Average</option>
          </select>
          <select class="fieldselect form-control " style="width:150px; display: inline-block">
            <option value="pid">Product ID</option>
            <option value="cid">Category ID</option>
          </select>
          <select class="operatorselect form-control" style="width:70px; display: inline-block">
            <option value="&gt;">&gt;</option>
            <option value="&gt;">&gt;</option>
            <option value="&lt;">&lt;</option>
            <option value="&#x2260;">&#x2260;</option>
          </select>
          <input type="text"  class="value form-control" style="width:150px; display: inline-block"/>
          <a href='#' class="removelink"><i class="fa fa-remove"></i> </a>
          <div class="subconditionlist">
          </div>
          <a href='#' class="addcondlink">and/or</a>
        </div>
      </div>

    </div>

    <div class="hidden id_condtemp">
      <div>
        <select class="joinselect form-control" style="width:80px; display: inline-block">
          <option value="and">and</option><option value="or">or</option>
        </select>
        <select class="subfieldselect form-control" style="width:150px; display: inline-block"></select>
        <select class="operatorselect form-control" style="width:70px; display: inline-block">
          <option value="&gt;">&gt;</option>
          <option value="&gt;">&gt;</option>
          <option value="&lt;">&lt;</option>
          <option value="&#x2260;">&#x2260;</option>
        </select>
        <input type="text"  class="value form-control" style="width:150px; display: inline-block"/>
        <a href='#' class="removecondlink"><i class="fa fa-remove"></i> </a>
      </div>
    </div>
  </div>

</div>

@endsection
