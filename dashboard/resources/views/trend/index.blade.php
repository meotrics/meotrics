@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')

@section('script')
<script src="{{asset('js/select2.min.js')}}"></script>
<script>
//$('#actionselect').select2();

function updateQuery()
{

  $.get('queryTrend', {
    typeid: $('#actionselect').val(),
    operation: $('#opertorselect').val(),
    object : $('#fieldselect').val(),
    param: $('#paramselect').val(),
  }, function(data){
    $body = $('.tbbody');
    $head = $('.tbhead');
    $body.empty();
    var head;
    data = JSON.parse(data);
    var stt = 0;
    for(var i in data)
    {
      stt++;
      var row = data[i];
      var rowstr = "<td>"+stt+"</td>";
      head = "<th>#</th>";
      for(var j in row) if(row.hasOwnProperty(j))
      {
        head += '<th> '+he.encode(j) +' </th>';
        rowstr += '<td>' + he.encode(row[j]) + '</td>';

      }
      rowstr = '<tr>' + rowstr + '</tr>';
      head = '<tr>' + head + '</tr>';
      $body.append(rowstr);
    }

    $head.empty();
    $head.append(head);
  });

}
$('#actionselect, #fieldselect, #opertorselect, #paramselect').change(function(){
  updateQuery();
});

updateQuery();

</script>
@endsection

@section('style')
<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')

<div class="card row">
  <div class="header col-md-12">
    <form class="">
      <label class="mr5">TREND SELECT</label>
      <select id="trendselect" class="form-control mr" style="width: 250px; display:inline-block">
        <option value="56dab10c44aee0d1bd499a29">Purchase</option>
        <option value="56dab10544aee0d1bd499a27">Pageview</option>
      </select>
      <!-- <a class="id_querytrend btn btn-fill btn-success mr "><i class="fa fa-play" style="font-size: 15px; vertical-align: middle"></i> <span style="vertical-align: middle">Query</span></a> -->
      <a class="btn">  <i class="fa fa-floppy-o" style="font-size: 18px;padding-top: 4px;vertical-align: middle"></i></a>
      <a class="id_querytrend btn "><i class="fa fa-trash-o" style="font-size: 19px;vertical-align: middle"></i></a>
    </form>
  </div>


  <div class="content col-md-12">
    <form class="">
      <label>IN ACTION</label>
      <select id="actionselect" class="form-control" style="width: 150px; display:inline-block">
        <option value="56dab10c44aee0d1bd499a29">Purchase</option>
        <option value="56dab10544aee0d1bd499a27">Pageview</option>
      </select>


      <label>LIST TOP</label>
      <select id="fieldselect" class="form-control" style="width: 150px; display:inline-block">
        <option value="pid">Product ID</option>
        <option value="cid">Category ID</option>
        <option value="amount">Amount</option>
        <option value="price">Price</option>
      </select>

      <label>ORDER BY </label>
      <select id="opertorselect" class="form-control" style="width: 150px; display:inline-block">
        <option value="sum">Sum</option>
        <option value="count">Number of occurs</option>

        <option value="avg">Avg</option>
      </select>
      <select id="paramselect" class="form-control" style="width: 150px; display:inline-block">
        <option value="pid">Product ID</option>
        <option value="cid">Category ID</option>
        <option value="amount">Amount</option>
        <option value="price">Price</option>
      </select>
    </form>

    <div class="row">
      <div class=" col-sm-12">
        <table class="table table-hover table-striped">
          <thead class="tbhead"></thead>
          <tbody class="tbbody">

          </tbody>
        </table>

      </div>
    </div>

  </div>
</div>
@endsection
