@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')

@section('script')
<script src="{{asset('js/select2.min.js')}}"></script>
<script>
//$('#actionselect').select2();

$('.id_querytrend').click(function(){

  $.get('queryTrend', {
    typeid: $('#actionselect').val(),
    operation: $('#opertorselect').val(),
    object : $('#fieldselect').val(),
    param: $('#paramselect').val(),
  }, function(data){
    console.log(data);
  });
});
</script>
@endsection

@section('style')
<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')
<div class="container">
  <script></script>
  <form>
    <div class="row">
      <div class="col-xs-3">
        <label>Action
          <select id="actionselect" class="form-control">
            <option value="2">Purchase</option>
            <option value="1">Pageview</option>
          </select></label>
        </div>

        <div class="col-xs-2">
          <label>By
            <select id="fieldselect" class="form-control">
              <option value="pid">Product ID</option>
              <option value="cid">Category ID</option>
              <option value="cid">Amount</option>
              <option value="cid">Price</option>
            </select>
          </label>
        </div>

        <div class="col-xs-2">
          <label> Operator
            <select id="opertorselect" class="form-control">
              <option value="count">Count all actions</option>
              <option value="sum">Sum</option>
              <option value="avg">Avg</option>
            </select></label>
          </div>

          <div class="col-xs-3">
            <label> Param
              <select id="paramselect" class="form-control">
                <option value="pid">Product ID</option>
                <option value="cid">Category ID</option>
                <option value="cid">Amount</option>
                <option value="cid">Price</option>
              </select></label>
            </div>

            <input type="button" value="Query" class="id_querytrend btn btn-default col-sm-2"/>
          </form>
          <div class="row">

            <div class="content table-responsive table-full-width col-sm-12">
              <table class="table table-hover table-striped">
                <thead>
                  <tr><th>ID</th>
                    <th>Name</th>
                    <th>Salary</th>
                    <th>Country</th>
                    <th>City</th>
                  </tr></thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>Dakota Rice</td>
                      <td>$36,738</td>
                      <td>Niger</td>
                      <td>Oud-Turnhout</td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>Minerva Hooper</td>
                      <td>$23,789</td>
                      <td>Curaçao</td>
                      <td>Sinaai-Waas</td>
                    </tr>
                    <tr>
                      <td>3</td>
                      <td>Sage Rodriguez</td>
                      <td>$56,142</td>
                      <td>Netherlands</td>
                      <td>Baileux</td>
                    </tr>
                    <tr>
                      <td>4</td>
                      <td>Philip Chaney</td>
                      <td>$38,735</td>
                      <td>Korea, South</td>
                      <td>Overland Park</td>
                    </tr>
                    <tr>
                      <td>5</td>
                      <td>Doris Greene</td>
                      <td>$63,542</td>
                      <td>Malawi</td>
                      <td>Feldkirchen in Kärnten</td>
                    </tr>
                    <tr>
                      <td>6</td>
                      <td>Mason Porter</td>
                      <td>$78,615</td>
                      <td>Chile</td>
                      <td>Gloucester</td>
                    </tr>
                  </tbody>
                </table>

              </div>
            </div>
          </div>
        </div>
        @endsection
