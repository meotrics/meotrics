@extends('../layout/master')

@section('script')
	<script>
		function confirmDelete(acode){
			return confirm('Are you sure ? Detele `' + acode + '` action type !');
		}

		onPageLoad(function(){
			$('.id_add').click(function(){

			});
		});
	</script>
@endsection

@section('content')
	<div class="card row">
		<div class="header col-sm-12">
			<h3>Action type </h3>
			<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
				Track new app
			</button>
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

	<div class="modal fade" id="addModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Track new app</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-2 ">
							<h6 class="pull-right">Name</h6>
						</div>
						<div class="col-sm-5">
							<input type="text" class="form-control" placeholder="App Name" required>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary id_add" >Create</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
@endsection