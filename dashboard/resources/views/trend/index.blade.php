@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')

@section('script')

@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')
	<div>
		<div class="card col-sm-12">
			<div class="row">
				<div class="header col-md-12">
					<form class="">
						<label class="">Select trend</label>&nbsp;&nbsp;
						<select id="trend" class="form-control input-sm " style="width: 250px; display:inline-block">
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
						<a id="action_update" data-href="{{URL::to('trend/update')}}" href="{{URL::to('trend/update', [
                                    'id' => $trend_first ? $trend_first->_id : ''
                                ])}}" class="button action blue" role="button"><span class="label">Update</span></a>
						<a id="action_delete" href="javascript:void(0)" class="button action red" role="button"><span class="label">Delete</span></a>
						&nbsp; or &nbsp;<a href="{{ URL::to('trend/create') }}">+ CREATE NEW TREND</a>
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
							]);
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