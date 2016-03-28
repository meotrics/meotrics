@extends('../layout/master')

@section('content')
	<div class="card row">
		<div class="header col-sm-12">
			<h3>Action type</h3>
		</div>
		<div class="content col-sm-12">
			<div class="content table-responsive table-full-width col-sm-12">
				<div class="pull-right">
					<a href="/actiontype/create" class="btn btn-success btn-sm btn-fill">+ Add new</a>
				</div>
				<table class="table table-hover table-striped">
					<thead>
					<tr>
						<th>CodeName</th>
						<th>Name</th>
						<th>Description</th>
						<th>Properties</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach($actiontypes as $type)
						<tr>
							<td><code class="fmonospaced">{{$type->codename}}</code></td>
							<td>{{$type->name}}</td>
							<td>{{$type->desc}}</td>
							<td>
								@foreach($type->fields as $field)
									{{$field->pname}} (<code class="fmonospaced">{{$field->pcode}}</code>) <br/>
								@endforeach
							</td>
							<td>
								<a class="btn btn-primary btn-sm btn-fill"
									 href="/actiontype/edit/{{$type->_id}}"><i class="fa fa-edit"></i></a>
								<a class="btn btn-danger  btn-sm btn-fill"
									 href="/actiontype/delete/{{$type->_id}}"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>

	</div>

@endsection