@extends('../layout/master')

@section('script')
	<script>
		function confirmDelete(acode){
			return confirm('Are you sure ? Detele `' + acode + '` action type !');
		}
	</script>
@endsection

@section('content')
	<div class="card row">
		<div class="header col-sm-12">
			<h3>Action type </h3>
			<a href="/app/create" class="button action blue"><span class="label">Track new app</span></a>

		</div>
		<div class="content col-sm-12">
			<div class="content table-responsive table-full-width col-sm-12">

				<table class="table table-hover table-striped">
					<thead>
					<tr>
						<th>Code</th>
						<th>Name</th>
						<th>Owner</th>
						<th>Agency</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach($apps as $ap)
						<tr>
							<td><code class="fmonospaced">{{$ap->code}}</code></td>
							<td>{{$ap->name}}</td>
							<td>{{$ap->owner}}</td>
							<td>
								@foreach($ap->agencies as $ag)
									{{$ag->name}} {{ $ag->can_perm == 1 ? '<i class="fa fa-star orange"><i/>' : ($ag->can_struct == 1 ? '<i class="fa fa-star"><i/>': '' )}} <br/>
								@endforeach
							</td>
							<td class="row">
								<a class="btn btn-primary btn-sm btn-fill" href="/home/{{$ap->_id}}"><i class="fa fa-sign-in"></i> Enter</a>
								<a class="btn btn-sm" href="/app/{{$ap->_id}}"><i class="fa fa-edit"></i></a>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>

	</div>

@endsection