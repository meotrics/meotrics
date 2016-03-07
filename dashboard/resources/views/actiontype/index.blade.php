

<h1>Action type manager</h1>
<a href="/actiontype/create" class="btn btn-success">Add action type</a>
<div class="content table-responsive table-full-width col-sm-12">
  <table class="table table-hover table-striped">
    <thead>
      <tr><th>ID</th>
        <th>CodeName</th>
        <th>Name</th>
        <th>Description</th>
        <th>Fields</th>
        <th></th>
      </tr></thead>
      <tbody>
        @foreach($actiontypes as $type)
        <tr>
          <td>{{$type->id}}</td>
          <td>{{$type->codename}}</td>
          <td>{{$type->name}}</td>
          <td>{{$type->desc}}</td>
          <td>{{$type->fields}}</td>
          <td><a href="/actiontype/edit/{{$type->id}}">edit</a> <a href="/actiontype/delete/{{$type->id}}">delete</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
