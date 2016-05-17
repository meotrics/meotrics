@extends('../layout/master')
@section('script')
	<script>
		onPageLoad(function(){
		$('.id_grant').click(function () {
			var email = prompt('Please type user email');
			$.post('/perm/{{$appid}}/add', {email: email}, function(){
				location.reload();
			}).fail(function(){
				alert('something went wrong, maybe server has disconnection or access denied');
			});
		});
		});
	</script>
@endsection

@section('content')

	<div class="card row">
		<div class="content col-sm-12">
			<form method="POST">
				<input type="hidden" name="_method" value="PUT">
				<div>
					<a href="{{ URL::to('app') }}">
						<small class="text-muted pull-left uppercase">
							<b><i class="fa fa-chevron-left"></i>All Apps</b>
						</small>
					</a><br>

					<div class="row">
						<div class="col-sm-2">
							<h6 class="pull-right">Name</h6>
						</div>
						<div class="col-sm-5">
							<input type="text" name="name" class="form-control" id="exampleInputEmail1"
							       placeholder=" Display Name" required
							       value="{{ $ap->name }}">
						</div>
					</div>
					<div class="row">
						<p>Access control</p>
						<table class="table table-hover table-striped">
							<thead>
							<tr>
								<th>Email</th>
								<th>Full control</th>
								<th>Edit app structure</th>
								<th>Create or edit reports</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($ap->agencies as $ag)
								<tr id="tr{{$ag->userid}}">
									<td>$ag->name</td>
									<td>
										<div class="onoffswitch">
											<input type="checkbox" class="onoffswitch-checkbox"
											       id="of_{{$ag->userid}}_1" {{$ag->can_perm == 1 ? 'checked':''}} >
											<label class="onoffswitch-label" for="of_{{$ag->userid}}_1"></label>
										</div>
									</td>
									<td>
										<div class="onoffswitch">
											<input type="checkbox" class="onoffswitch-checkbox"
											       id="of_{{$ag->userid}}_2" {{$ag->can_struct == 1 ? 'checked':''}}>
											<label class="onoffswitch-label" for="of_{{$ag->userid}}_2"></label>
										</div>
									</td>
									<td>
										<div class="onoffswitch">
											<input type="checkbox" class="onoffswitch-checkbox"
											       id="of_{{$ag->userid}}_3" {{$ag->can_report == 1 ? 'checked':''}}>
											<label class="onoffswitch-label" for="of_{{$ag->userid}}_3"></label>
										</div>
									</td>
									<td><a href="#" id="rm{{$ag->userid}}" data-appid={{$ag->appid}} data-userid="{{$ag->userid}}">Remove
											access</a></td>
								</tr>

								<script class="hidden">
									onPageLoad(function () {
										$('#rm{{$ag->userid}}').click(function () {
											var ok = confirm("are you sure want to delete access from user {{$ag->name}}?");
											if (ok) {
												$.post('/perm/{{$ag->appid}}/delete/{{$ag->userid}}', function () {
													$('#tr{{$ag->userid}}').addClass('hidden');
												}).fail(function () {
													alert('something went wrong, maybe server has disconnection or access denied');
												});
											}
										});

										$('#of_{{$ag->userid}}_1').change(function () {
											var $this = $(this);
											$.post('/perm/{{$ag->appid}}/{{$ag->userid}}', {can_perm: $this.prop('checked') == true ? 1 : 0}, function () {
											}).fail(function () {
												$this.prop('checked', !$this.prop('checked'));
												alert('something went wrong, maybe server has disconnection or access denied');
											});
										});
										$('#of_{{$ag->userid}}_1').change(function () {
											$.post('/perm/{{$ag->appid}}/{{$ag->userid}}', {can_struct: $this.prop('checked') == true ? 1 : 0}, function () {
											}).fail(function () {
												$this.prop('checked', !$this.prop('checked'));
												alert('something went wrong, maybe server has disconnection or access denied');
											});
										});
										$('#of_{{$ag->userid}}_1').change(function () {
											$.post('/perm/{{$ag->appid}}/{{$ag->userid}}', {can_report: $this.prop('checked') == true ? 1 : 0}, function () {
											}).fail(function () {
												$this.prop('checked', !$this.prop('checked'));
												alert('something went wrong, maybe server has disconnection or access denied');
											});
										});
									});
								</script>
							@endforeach
							</tbody>
						</table>
					</div>

					<div class="row">
						<a class="id_grant" href="#">Grant access for other user</a>
					</div>

					<div class="row">
						<div class="col-sm-push-2 col-sm-2">
							<button class="btn btn-success btn-fill ">
								<i class="pe-7s-diskette mr5" style="font-size:19px; vertical-align: middle"></i>
								<span class="" style="vertical-align: middle">Save changes</span>
							</button>
						</div>
					</div>
				</div>
			</form>

		</div>
@endsection