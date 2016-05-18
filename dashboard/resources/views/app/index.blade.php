@extends('../layout/master')

@section('script')
	<script>
		function confirmDelete(acode) {
			return confirm('Are you sure ? Detele `' + acode + '` action type !');
		}

		onPageLoad(function () {
			$('.id_add').click(function () {
				$.post('/app/create', {name: $('.id_name').val()}, function (appid) {
					showCodeDialog(appid);
				}).fail(function () {
					alert('cannot create app');
				});
				$('.id_name').val("");
			});
		});
	</script>
@endsection

@section('content')

	<div class="card row">
		<div class="header col-sm-12">
			<h3>Apps </h3>
			<button type="button" data-toggle="modal" data-target="#addModal" class="button action blue">
				<span class="label">Track new app</span></button>

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
							<td>{{$ap->owner->name .'('.$ap->owner->email.')'}}</td>
							<td>
								@foreach($ap->agencies as $ag)
									{{$ag->name . '('.$ag->email.')'}} {!!   $ag->can_perm == 1 ? '<i class="fa fa-star orange"><i/>' : ($ag->can_struct == 1 ? '<i class="fa fa-star"><i/>': '' )!!}
									<br/>
								@endforeach
							</td>
							<td class="row">
								<a class="btn btn-primary btn-sm btn-fill" href="/home/{{$ap->id}}"><i class="fa fa-sign-in"></i> Enter</a>
								<a class="btn btn-sm" href="/app/{{$ap->id}}"><i class="fa fa-edit"></i></a>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>

	</div>

	<div class="hidden">

	</div>
@endsection

@section('additional')
	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
										aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Track new app</h4>
				</div>
				<div class="modal-body">
					<div class="row pt pb10">
						<div class="col-sm-4 ">
							<h6 class="pull-right">name of the app</h6>
						</div>
						<div class="col-sm-7">
							<input type="text" class="form-control id_name" placeholder="App Name" required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="button action ">
						<span class="label">Cancel</span></button>
					<button type="button" data-dismiss="modal" class="button action blue id_add">
						<span class="label">Next step</span></button>

				</div>
			</div>
		</div>
	</div>
@endsection