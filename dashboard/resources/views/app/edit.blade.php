@extends('layout.master')
@section('script')
	<script>
		onPageLoad(function () {
			$('.id_grant').click(function () {
				var email = prompt('Please type user email');
				$.post('/perm/{{$appcode}}/add', {email: email}, function () {
					location.reload();
				}).fail(function () {
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
				<div>
					<a href="/">
						<small class="text-muted pull-left uppercase">
							<b><i class="fa fa-chevron-left"></i>All Apps</b>
						</small>
					</a><br>

					<div class="row">
						<div class="col-sm-1">
							<h5>Name</h5>
						</div>
						<div class="col-sm-5">
							<input type="text" name="name" class="form-control" placeholder="Display Name" required value="{{ $ap->name }}">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-1">
							<h5>Url</h5>
						</div>
						<div class="col-sm-5">
							<input type="text" name="url" class="form-control" placeholder="App Url" value="{{ $ap->url or "" }}">
						</div>
					</div>

					<div class="row mt">
						<div class="col-sm-2">
							<h5 style="margin-bottom: 0">Access control</h5>
						</div>
						<div class="col-sm-12" style="padding-top: 0; padding-bottom: 0">
							<table class="table table-hover" style="margin-bottom: 0">
								<thead>
								<tr>
									<th style="padding-left: 0px">Email</th>
									<th>Full control</th>
									<th>Edit app structure</th>
									<th>Create or edit reports</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								@foreach($ap->agencies as $ag)
									<tr id="tr{{$ag->userid}}">
										<td style="padding-left: 0px">{{$ag->email}}</td>
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
										<td><a href="#" id="rm{{$ag->userid}}" data-appid={{$appcode}} data-userid="{{$ag->userid}}">Remove
												access</a></td>
									</tr>

									<script class="hidden">
										onPageLoad(function () {
											$('#rm{{$ag->userid}}').click(function () {
												var ok = confirm("are you sure want to delete access from user {{$ag->name}}?");
												if (ok) {
													$.post('/perm/{{$appcode}}/delete/{{$ag->userid}}', function () {
														$('#tr{{$ag->userid}}').addClass('hidden');
													}).fail(function () {
														alert('something went wrong, maybe server has disconnection or access denied');
													});
												}
											});

											$('#of_{{$ag->userid}}_1').change(function () {
												var $this = $(this);
												$.post('/perm/{{$appcode}}/set/{{$ag->userid}}', {can_perm: $this.prop('checked') == true ? 1 : 0}, function () {
												}).fail(function () {
													$this.prop('checked', !$this.prop('checked'));
													alert('something went wrong, maybe server has disconnection or access denied');
												});
											});
											$('#of_{{$ag->userid}}_2').change(function () {
												var $this = $(this);
												$.post('/perm/{{$appcode}}/set/{{$ag->userid}}', {can_struct: $this.prop('checked') == true ? 1 : 0}, function () {
												}).fail(function () {
													$this.prop('checked', !$this.prop('checked'));
													alert('something went wrong, maybe server has disconnection or access denied');
												});
											});
											$('#of_{{$ag->userid}}_3').change(function () {
												var $this = $(this);
												$.post('/perm/{{$appcode}}/set/{{$ag->userid}}', {can_report: $this.prop('checked') == true ? 1 : 0}, function () {
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
					</div>
					<div class="row">
						<div class="col-sm-12" style="padding-top: 0">
							<a class="id_grant" href="#">Grant access for other user</a>
						</div>
					</div>

					<div class="row mt">
						<div class="col-sm-12">
							<button type="submit" class=" button action blue">
								<span class="label"><i class="pe-7s-diskette"></i> Save</span></button>

							<a href="/" class="ml button action">
								<span class="label">Back</span></a>
						</div>
					</div>
				</div>
			</form>

		</div>
@endsection