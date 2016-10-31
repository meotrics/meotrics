@extends('layout.apmgr')

@section('script')

	<script src="{{asset('js/jquery.sparkline.min.js')}}" type="text/javascript"></script>
	<script>
		function confirmDelete(acode) {
			return confirm('Are you sure ? Detele `' + acode + '` action type !');
		}
		// ko cho realtime nua
		onPageLoad(function () {
			var status_app_pending = {};
			function update_status(app) {
				if(status_app_pending[app] == undefined || status_app_pending[app] == false){
					status_app_pending[app] = true;
//					$.post('/app/count_traffic/' + app, function(data){
//						$('.traffic_' + app).html(data);
//							var $st = $('.status_' + app);
//							$st.empty();
//						if (data >0) {
//							$st.append('<span class="greendot"></span> <span style="color:#0ea622">OK</span>');
//						}else{
//							$st.append('<span class="graydot"></span> <span style="color:#aaa;" >NOT CONNECTED</span>')
//						}
////						status_app_pending[app] = false;
////						$.post('/app/setup_status/' + app, function (data) {
////							var $st = $('.status_' + app);
////							$st.empty();
////
////							if (data == 'true') {
////								$st.append('<span class="greendot"></span> <span style="color:#0ea622">OK</span>');
////							}
////
////							if (data == 'false') {
////								$st.append('<span class="graydot"></span> <span style="color:#aaa;" >NOT CONNECTED</span>')
////							}
////							status_app_pending[app] = false;
////						});
//
//					});
				}
			}
			@foreach($apps as $ap)
			update_status('{{$ap->code}}');
			websock.appChange('{{$ap->code}}','type.pageview', update_status);
			@endforeach

		$(".sparkline").sparkline([ 0,0,0,0,0,0,0,0,0,0,0,0,0,0], {
				type: 'line',
				lineColor: '#00007f',
				lineWidth: 1,
				spotColor: undefined,
				minSpotColor: undefined,
				maxSpotColor: undefined,
				highlightSpotColor: undefined,
				spotRadius: 0
			});
		{{--@foreach($apps as $ap)--}}

		{{--$.post('/app/traffic14/{{$ap->code}}', function(data){--}}
			{{--$spl = $(".spl_{{$ap->code}}");--}}
			{{--$spl.empty();--}}
			{{--$spl.sparkline(data, {--}}
				{{--type: 'line',--}}
				{{--lineColor: '#00007f',--}}
				{{--lineWidth: 1,--}}
				{{--spotColor: undefined,--}}
				{{--minSpotColor: undefined,--}}
				{{--maxSpotColor: undefined,--}}
				{{--highlightSpotColor: undefined,--}}
				{{--spotRadius: 0--}}
			{{--});--}}
		{{--});--}}
			{{--@endforeach--}}

			$('.id_add').click(function () {
				$.post('/app/create', {name: $('.id_name').val(), url:$('.id_url').val()}, function (appcode) {
					showCodeDialog(appcode, function () {
						location.reload();
					});
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
			<h4 style="display: inline-block">Apps manager </h4>
			<button type="button" data-toggle="modal" data-target="#addModal" class=" button action blue">
				<span class="label">Track new app</span></button>
		</div>
		<div class="content col-sm-12">
			@if(count($apps)>0)
				<div class="content table-responsive table-full-width col-sm-12">
					<table class="table ">
						<thead>
						<tr>
							<th>Name</th>
							<th>Traffic</th>
							<th>Status</th>
							<th>Agency</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
						@foreach($apps as $ap)
							<tr>
								<td>{{$ap->name}}
									<br/>
									<code class="fmonospaced">{{$ap->code}}</code></td>
								<td><span class="traffic_{{$ap->code}}">{{$ap->count}}</span>
									<div class="spl_{{$ap->code}} sparkline"></div>
								</td>

								<td class="status_{{$ap->code}}"></td>
								<td>
									@foreach($ap->agencies as $ag)
									{!! $ag->id == $ap->ownerid ? '<b>' : ''!!}
										{{ $ag->name}}{!! $ag->id == $ap->ownerid ? '</b>' : ''!!} <span class="text-muted"> {{$ag->email}}</span>
										{!!   $ag->can_perm == 1 ? '<i class="fa fa-star orange"></i>' : ($ag->can_struct == 1 ? '<i class="fa fa-star gray"></i>': '' )!!}
										<br/>
									@endforeach
								</td>
								<td class="row">
									<a class="button action" href="/dashboard/{{$ap->code}}"> Enter Dashboard</a>
									<a class="button action" href="/app/edit/{{$ap->code}}"><i class="fa fa-pencil"></i></a>
									<a class="button action" onclick="showCodeDialog('{{$ap->code}}')" href="#"><i
														class="fa fa-file-code-o"></i></a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>

				</div>@endif
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

					<div class="row pt pb10">
						<div class="col-sm-4 ">
							<h6 class="pull-right">url of the app</h6>
						</div>
						<div class="col-sm-7">
							<input type="text" class="form-control id_url" placeholder="App URL" >
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
