

@section('script')
<script>

function addProp(prop)
{
  var $newprop = $('.id_proptem').child.clone();
  $newprop.find('.id_remprop').click(function(){
    $newprop.addClass('hidden');
  });
  $newprop.find('.id_name').val(prop.name);
  $newprop.find('.id_remprop')

  $(".id_proplist").append($newprop);
}
$('.id_addprop').click(function(){
  addProp({code: "", name: ""})
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

  <div class="id_proplist"></div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>

<div class="hidden id_proptem">
<div class="form-group ">
  <label >Properties code (should not contains any space)</label>
  <input type="text" class="form-control" name="codename" placeholder="Code">
  <label >Properties name</label>
  <input type="text" class="form-control" name="codename" placeholder="Name">
  <button class="id_remprop">Remove</button>
</div></div>
