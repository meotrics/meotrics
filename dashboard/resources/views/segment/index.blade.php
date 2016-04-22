<?php

$segments = isset($segments) ? $segments : [];
$props = isset($props) ? $props : [];
?>
@extends('../layout/master', ['sidebarselect' => 'segment'])
@section('title', 'Segment')
@section('script')
	<script type="text/javascript">
		var segments = {};
		<?php
						if($segments):
						$segment_first = null;
						foreach ($segments as $key => $segment):
						if ($key == 0) {
							$segment_first = $segment;
						}
						?>
						segments['<?= $segment->_id ?>'] = {
			name: '<?= property_exists($segment, 'name') ? $segment->name : '' ?>',
			description: '<?= property_exists($segment, 'description') ? $segment->description : '' ?>'
		}
		<?php
		endforeach;
		endif;
		?>
	</script>
@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')
	<div class="row">
		<div class="card col-sm-12">
			<div class="header row">
				<!--<form class="col-md-12">-->
				<h6 class="col-md-2">Segmentation</h6>&nbsp;&nbsp;
				<div class="col-md-2">
					<select id="segment" class="form-control input-sm" style="display:inline-block">
						@foreach($segments as $segment)
							<option value="{{$segment->_id}}" <?= $segment->_id == $segment_first->_id ? 'selected=""' : '' ?>>{{$segment->name ? $segment->name : TrendEnum::EMPTY_NAME}}</option>
						@endforeach
					</select>
				</div>

				<a id="action_update" data-href="{{URL::to('segment/update')}}" href="{{URL::to('segment/update', [
                'id' => $segment_first ? $segment_first->_id : ''
            ])}}" class="action button blue" role="button"><span class="label">Update</span> </a>
				<a id="action_delete" href="javascript:void(0)" class="action button red" role="button"> <span class="label">Delete</span></a>
				&nbsp; or &nbsp;<a href="{{ URL::to('segment/create') }}">+ CREATE NEW SEGMENTATION</a>
				<!--</form>-->
			</div>

			<!--    <div class="content col-md-12" data-name="name">
        <label class="col-md-2" style="margin-top: 4px">Segment name: </label>
        <p class="col-md-10"><?= property_exists($segment_first, 'name') ? $segment_first->name : ''?></p>
    </div>-->
			<div class="content row" data-name="description">
				<div class=" col-md-2">
					<h6>Description</h6>
				</div>
				<!--<label class="col-md-2" style="margin-top: 4px">Segment description: </label>-->
				<p class="col-md-10"><?= property_exists($segment_first, 'description') ? $segment_first->description : ''?></p>
			</div>

			<div class=" content row">
				<div class=" col-md-2">
					<h6>Filter to execute:</h6>
				</div>
				<div class="col-md-2">
					<select name="Prop[one]" class="form-control">
						<option value="">Select property</option>
						<?php
						foreach ($props as $prop):
						?>
						<option value="<?= property_exists($prop, 'code') ? $prop->code : '' ?>">
							<?= property_exists($prop, 'name') ? $prop->name : '' ?>
						</option>
						<?php
						endforeach;
						?>
					</select>
				</div>
				<div class="col-md-2">
					<select name="Prop[two]" class="form-control">
						<option value="">N/A</option>
						<?php
						foreach ($props as $prop):
						?>
						<option value="<?= property_exists($prop, 'code') ? $prop->code : '' ?>">
							<?= property_exists($prop, 'name') ? $prop->name : '' ?>
						</option>
						<?php
						endforeach;
						?>
					</select>
				</div>
				<div class="col-sm-2">
					<button type="button" class="action button blue" onclick="execute()">
						<span class="label">Generate</span>
					</button>
				</div>

				<!--        <canvas id="myChart" width="400" height="100" style="display: none"></canvas>-->
			</div>
			<div class="row">
				<div class="col-md-12" data-name="canvas-chart"></div>
			</div>
		</div>
	</div>
@endsection

@section('additional')
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script src="{{asset('js/Chart.js')}}"></script>
	<script type="text/javascript">
		$('select').select2();

		$('#segment').on('change', function () {
			var that = $(this);
			/*
			 * set name and description
			 */
			var name_div = $('div[data-name="name"]').find('p');
			if (name_div.length) {
				name_div.text(segments[that.val()] ? segments[that.val()]['name'] : '');
			}
			var description_div = $('div[data-name="description"]').find('p');
			if (description_div.length) {
				description_div.text(segments[that.val()] ? segments[that.val()]['description'] : '');
			}
			$('#action_update').attr('href', $('#action_update').attr('data-href') + '/' + that.val());
//        $.ajax({
//            type: 'GET',
//            dataType: 'JSON',
//            url: '{{ URL::to('trend/htmloutputs') }}',
//            data: {
//                '_id' : that.val(),
//            },
//            success: function(data){
//                if(data.success && data.html_outputs){
//                    $('#outputs_table').html(data.html_outputs);
//                }
//            },
//        });
			return false;
		});

		$('#action_delete').on('click', function () {
			var cf = confirm('This segment will be removed. Are you sure?');
			if (cf) {
				$.ajax({
					type: 'DELETE',
					dataType: 'JSON',
					url: '{{ URL::to('segment/remove') }}' + '/' + $('#segment').val(),
					success: function (data) {
						if (data.success) {
							location.reload();
						}
					},
				});
			}
		});

		/*
		 * chart
		 */
		var chart_colors = [];
		<?php
		foreach (App\Enum\SegmentEnum::chartColor() as $color):
		?>
		chart_colors.push('<?= $color ?>');
		<?php
		endforeach;
		?>
		function execute() {
			var segment_id = $('#segment').val();
			var field1 = $('select[name="Prop[one]"]').val();
			var field2 = $('select[name="Prop[two]"]').val();
			var url = '';
			var data_get = {};
			var label_field = '';
			var demonstrate = '';
			if (field1 && field2 && field1 != field2) {
				url = '{{ URL::to('segment/charttwofields') }}';
				data_get = {
					'segment_id': segment_id,
					'field1': field1,
					'field2': field2,
				};
				demonstrate = $('select[name="Prop[one]"]').find(':selected').text().toUpperCase() + ' and ' + $('select[name="Prop[two]"]').find(':selected').text().toUpperCase();
			}
			else if (field1 || field2) {
				url = '{{ URL::to('segment/chartonefield') }}';
				data_get = {
					'segment_id': segment_id,
				};
				data_get.field = field1 ? field1 : field2;
				label_field = field1 ? $('select[name="Prop[one]"]').find(':selected').text() : $('select[name="Prop[two]"]').find(':selected').text()
				demonstrate = field1 ? $('select[name="Prop[one]"]').find(':selected').text().toUpperCase() : $('select[name="Prop[two]"]').find(':selected').text().toUpperCase()
			}
			if (url) {
				$.ajax({
					type: 'GET',
					dataType: 'JSON',
					url: url,
					data: data_get,
					success: function (data) {
						if (data.success && data.labels && data.datasets) {
//                        if($('#segment_chart').length){
//                            $('#segment_chart').remove();
//                        }
							$('div[data-name="canvas-chart"]').html('');
							$('div[data-name="canvas-chart"]')
											.append('<p>This chart demonstrates that the filter by ' + demonstrate + '</p>');
							$('div[data-name="canvas-chart"]')
											.append('<canvas id="segment_chart" width="400" height="100"></canvas>');
							var ctx = $("#segment_chart").get(0).getContext("2d");
							var data_chart = {
								labels: [],
								datasets: [],
							};
							var options = {
								scales: {
									yAxes: [{
										stacked: true,
										ticks: {
											beginAtZero: true
										}
									}],
									xAxes: [{
										stacked: true,
									}],
								},
								stacked: true,
							}
							data_chart.labels = data.labels;
							data_chart.datasets = [];
							var datasets_labels = data.datasets_labels ? data.datasets_labels : [];
							$.each(data.datasets, function (di, dv) {

								data_chart.datasets.push({
									'label': datasets_labels[di] ? datasets_labels[di] : (label_field ? label_field : ''),
									'data': dv['data'],
									'backgroundColor': chart_colors[di] ? chart_colors[di] : '',
									'hoverBackgroundColor': chart_colors[di] ? chart_colors[di] : '',
								});
//                            data_chart.datasets[di]['data'] = dv['data'];
							});
							$('#myChart').show();
							$('#myChart').html('');
							var myBarChart = new Chart(ctx, {
								type: 'bar',
								data: data_chart,
								options: options
							});
						}
					},
				});
			}
		}

		// Get context with jQuery - using jQuery's .get() method.
		//    var ctx = $("#myChart").get(0).getContext("2d");
		// This will get the first returned node in the jQuery collection.
		//    var data = {
		//        labels: ["Female", "Male"],
		//        datasets: [
		////            {
		////                label: "My First dataset",
		////
		////                // The properties below allow an array to be specified to change the value of the item at the given index
		////                // String  or array - the bar color
		////                backgroundColor: "red",
		////
		////                // String or array - bar stroke color
		////                borderColor: "rgba(220,220,220,1)",
		////
		////                // Number or array - bar border width
		////                borderWidth: 1,
		////
		////                // String or array - fill color when hovered
		////                hoverBackgroundColor: "rgba(220,220,220,0.2)",
		////
		////                // String or array - border color when hovered
		////                hoverBorderColor: "rgba(220,220,220,1)",
		////
		////                // The actual data
		////                data: [65, 59, 80, 81, 56, 55, 40],
		////
		////                // String - If specified, binds the dataset to a certain y-axis. If not specified, the first y-axis is used.
		////                yAxisID: "y-axis-0",
		////            },
		//            {
		//                label: "Gender",
		//                backgroundColor: "rgba(220,220,220,0.2)",
		//                borderColor: "rgba(220,220,220,1)",
		//                borderWidth: 1,
		//                hoverBackgroundColor: "rgba(220,220,220,0.2)",
		//                hoverBorderColor: "rgba(220,220,220,1)",
		//                data: [1250, 257]
		//            }
		//        ]
		//    };
		//    var options = {
		//        scales: {
		//            yAxes: [{
		//                stacked: true,
		//                ticks: {
		//                    beginAtZero:true
		//                }
		//            }],
		//            xAxes: [{
		//                stacked: true,
		//            }],
		//        },
		//        stacked: true,
		//    }
		//    var myBarChart = new Chart(ctx, {
		//        type: 'bar',
		//        data: data,
		//        options: options
		//    });
	</script>
@endsection