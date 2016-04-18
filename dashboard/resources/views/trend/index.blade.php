<?php
?>
@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')
{{--
$types: list of action type in the system
each $types have fields in it

--}}
@section('script')

@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')

	<div class="card row">
		<div class="header col-md-12">
			<form class="">
				<label class="">Select trend</label>&nbsp;&nbsp;
				<select id="trend" class="form-control input-sm " style="width: 250px; display:inline-block">
					<?php
					$trend_first = null;
					if($trends):
					$trend_first = $trends[0];
					?>
					@foreach($trends as $trend)
						<option value="{{$trend->_id}}">{{$trend->name ? $trend->name : TrendEnum::EMPTY_NAME}}</option>
					@endforeach
					<?php
					endif;
					?>
				</select>
				<a id="action_update" data-href="{{URL::to('trend/update')}}" href="{{URL::to('trend/update', [
                                    'id' => $trend_first ? $trend_first->_id : ''
                                ])}}" class="button action blue" role="button"><span class="label">Update</span></a>
				<a id="action_delete" href="javascript:void(0)" class="button action red" role="button"><span class="label">Delete</span></a>
				&nbsp; or &nbsp;<a href="{{ URL::to('trend/create') }}">+ CREATE NEW TREND</a>
			</form>
		</div>


		<div class="content col-md-12" id="outputs_table">
			<?php
			if($outputs):
			?>
			@include('trend.partials.outputs', [
					'outputs' => $outputs,
			])
			<?php
			endif;
			?>
		</div>
	</div>


@endsection

@section('additional')
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script type="text/javascript">
		$('select').select2();

		$('#trend').on('change', function () {
			var that = $(this);
			$('#action_update').attr('href', $('#action_update').attr('data-href') + '/' + that.val());
			$.ajax({
				type: 'GET',
				dataType: 'JSON',
				url: '{{ URL::to('trend/htmloutputs') }}',
				data: {
					'_id': that.val(),
				},
				success: function (data) {
					if (data.success && data.html_outputs) {
						$('#outputs_table').html(data.html_outputs);
					}
				},
			});
			return false;
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