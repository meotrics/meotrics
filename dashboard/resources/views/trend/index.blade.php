@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')

@section('header-script')
	<script>
		onPageLoad(function () {

			var $tp = $('#timepick');
			$tp.dateRangePicker();
			//load segment time range

			@if(isset($starttime))
			$('#segment-date-range').data('dateRangePicker').setDateRange('{{$starttime}}', '{{$endtime}}');
			@else
			// 30 ngay truoc do
			var today = new Date().toISOString().substr(0, 10);
			var lastyear = new Date(new Date().getTime() - 31104000000).toISOString();
			$tp.data('dateRangePicker').setDateRange(lastyear, today);
			@endif
			// bind event
			$('#segpick').on('change', function () {
				var val = $(this).val();
				$.post('/trend/currentsegment/', {'segmentid': val}, function () {
					location.reload();
				});
			});

			$tp.on('change', function () {
				var val = $(this).val();
				$.post('/trend/currenttime/', {'endTime': val.split(' ')[2], 'startTime': val.split(' ')[0]}, function () {
					location.reload();
				});
			});
		});
	</script>
@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('action')
	<li>
		<a href="/trend/create" class="button action blue"><span class="label">New Trend</span></a>
	</li>
@endsection

@section('content')
	<div>
		<div class="card col-sm-12">
			<div class="row">
				<div class="header col-md-12">
					<div class="pull-right">

						<input style="width: 220px;display: inline-block;" class="form-control mr" id="timepick">

						<label style="vertical-align: bottom; margin-right: 5px">Segment</label>
						<select id="segpick" class="form-control input-sm" style="width: 200px; display:inline-block;">
							@foreach($segments as $segment)
								@if( $segmentid == $segment->_id)
									<?php $trend_segment = $segment ?>
									<option selected
													value="{{$segment->_id}}">{{ isset($segment->name) ? $segment->name : "unnamed"}}</option>
								@else
									<option value="{{$segment->_id}}">{{ isset($segment->name) ? $segment->name : "unnamed"}}</option>
								@endif
							@endforeach
						</select>
					</div>

					<form class="">
						<label style="vertical-align: bottom; margin-right: 6px">Trend</label>
						<select id="trend" class="form-control input-sm " style="width: 200px; display:inline-block">
							<?php $trend_first = $trends[0]; ?>
							@foreach($trends as $trend)
								@if(isset($trendid) && $trendid == $trend->_id)
									<?php $trend_first = $trend; ?>
									<option selected value="{{$trend->_id}}">{{ isset($trend->name) ? $trend->name : "unnamed"}}</option>
								@else
									<option value="{{$trend->_id}}">{{ isset($trend->name) ? $trend->name : "unnamed"}}</option>
								@endif
							@endforeach
						</select>

						<div class="btn-group " style="margin-left: 5px;">
							<a id="action_update" type="button" class="button btn" data-href="{{URL::to('trend/update')}}" href="{{URL::to('trend/update', ['id' => $trend_first ? $trend_first->_id : ''])}}"  ><i class="fa fa-pencil"></i></a>
							<a type="button" class="button btn  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#" id="action_delete"><i class="fa fa-remove"></i> Delete</a></li>
							</ul>
						</div>

											</form>
				</div>
			</div>
			<div class="row">
				<div class="content col-md-12" id="outputs_table">
					@if(isset($outputs))
						@include('trend.partials.outputs', [
									'actiontypes' => $types,
									'typeid' => $trend_first->typeid,
									'object' => $trend_first->object,
									'outputs' => $outputs,
									'op' => $trend_first->operation,
									'param' => $trend_first->param
							])
					@endif
				</div>
			</div>
		</div>
	</div>

@endsection

@section('additional')
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script type="text/javascript">
		$('select').select2();

		$('#trend').on('change', function () {
			var that = $(this);
			$.post('/trend/currenttrend/' + that.val(), function () {
				location.reload();
			});

		});

		$('#action_delete').on('click', function () {
			var cf = confirm('This trend will be removed. Are you sure?');
			if (cf) {
				$.ajax({
					type: 'DELETE',
					dataType: 'JSON',
					url: '{{ URL::to('trend/remove') }}' + '/' + $('#trend').val(),
					success: function (data) {
						if (data.success) {
							location.reload();
						}
					},
				});
			}
		});
	</script>
@endsection