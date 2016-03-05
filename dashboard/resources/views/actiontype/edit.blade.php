

@section('script')
<script>
$('#id_addprop').click(function(){

})

</script>
@endsection



<h1>Edit action type {{ $type->id }}</h1>

<form>
  <div class="form-group">
    <label>Name</label>
    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Name" value="{{$type->name}}">
  </div>
  <div class="form-group">
    <label >Code name</label>
    <input type="text" class="form-control" name="codename" placeholder="Password">
  </div>
  <div class="form-group">
    <label >Description</label>
    <input type="text" class="form-control" name="desc" placeholder="Description">
  </div>

  <h3>Properties</h3>
  <a class="id_addprop" href="#">+ Add Properties</a>

  <div></div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>

<div class="hidden id_proptem">
<div class="form-group ">
  <label >Properties code (should not contains any space)</label>
  <input type="text" class="form-control" name="codename" placeholder="Code">
  <label >Properties name</label>
  <input type="text" class="form-control" name="codename" placeholder="Name">
</div></div>
