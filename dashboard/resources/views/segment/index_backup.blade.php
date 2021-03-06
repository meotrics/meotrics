@extends('layout.master', ['sidebarselect' => 'segment'])
@section('title', 'Segment')

<?php
$segments = isset($segments) ? $segments : [];
$props = isset($props) ? $props : [];
?>

@section('script')
	<script type="text/javascript">
		var segments = {};
		<?php if($segments):
						$segment_first = null;

						foreach ($segments as $key => $segment):
						if ($segmentid == null && $key == 0)
							$segment_first = $segment;
						else if ($segmentid == $segment->_id)
							$segment_first = $segment;
						?>
						segments['{{ $segment->_id }}'] = {
			name: '{{ property_exists($segment, 'name') ? $segment->name : '' }}',
			description: '{{property_exists($segment, 'description') ? $segment->description : '' }}',
			count: '{{ property_exists($segment, 'count') ? $segment->count : '' }}',
			startTime: '{{ property_exists($segment, 'startTime') ? $segment->startTime : '' }}',
			endTime: '{{ property_exists($segment, 'endTime') ? $segment->endTime : '' }}',
		};
		<?php
		endforeach;
		endif;
		?>
	</script>
@endsection

@section('action')
	<li>
		<a href="{{ URL::to('segment/'. $appcode . '/create') }}" class="button action blue button-radius">
			<span class="label"><b>New Segmentation</b></span>
		</a>
	</li>
@endsection

@section('content')
	<div class="row">
		<div class="card col-md-12">
			<div class="header row col-segment">
				<!--<form class="col-md-12">-->
				<div class="row">
					<div class="col-md-6">
						<div class="col-md-3 fix-padding">
							<h6>Segmentation</h6>
						</div>
						<div class="col-md-5 fix-padding">
							<select id="segment" class="form-control input-sm" style="display:inline-block">
								@foreach($segments as $segment)
									<option value="{{$segment->_id}}" {{ $segment->_id == $segment_first->_id ? 'selected' : '' }}>
										{{$segment->name ? $segment->name : TrendEnum::EMPTY_NAME}}
									</option>
								@endforeach
							</select>
						</div>
						<div class=" col-md-2 div-edit-obj fix-padding">
							<a id="action_update" data-href="{{URL::to('segment/'. $appcode .'/update')}}" href="{{URL::to('segment/'. $appcode .'/update', [
                                        'id' => isset($segment_first) ? $segment_first->_id : ''
                                    ])}}" class="a-edit-obj" role="button">
								<!--<span class="glyphicon glyphicon-pencil"></span>-->
								<i class="fa fa-pencil" aria-hidden="true"></i>
							</a>
						</div>
						<div class="col-md-2 div-trash-obj fix-padding">
							<a id="action_delete" href="javascript:void(0)" class="a-trash-obj" role="button">
								<!--<span class="glyphicon glyphicon-trash"></span>-->
								<i class="fa fa-trash" aria-hidden="true"></i>
							</a>
						</div>
					</div>
				</div>
				@if(isset($segment_first))
					<div class="row">
						<div class="content col-md-6">
							<div class="col-md-3 fix-padding">
								<h6>Description</h6>
							</div>
							<div class="col-md-5 limit-char-des fix-padding">
								<p class="segment-desc" style="font-size: 14px"
								   id="desc"><?= property_exists($segment_first, 'description') ? $segment_first->description : '' ?></p>
							</div>
						</div>
						<div class="col-md-6 content">
							{{--@if(isset($segment_first->startTime ))--}}
								{{--<div class="col-md-3">--}}
									{{--Time range:--}}
								{{--</div>--}}
								{{--<div class="col-md-5">--}}
									{{--<span id="startTime">{{$segment_first->startTime}}--}}
									{{--to {{$segment_first->endTime}}--}}
									{{--</span>--}}
								{{--</div>--}}
							{{--@endif--}}
							<div class="col-md-4">
								<button type="button" data-loading-text="Loading..." autocomplete="off" class="action button blue button-radius" id="refresh" >
									<span class="label">Refresh</span>
								</button>
							</div>
							<div class="col-md-4">
								<div class="col-md-6">
									Count:
								</div>
								<div class="col-md-6">
									<span id="count">@if(isset($segment_first->count)){{$segment_first->count}}@endif</span>
								</div>
							</div>
						</div>
					</div>
				@endif
									<!--</form>-->
			</div>

			<!--    <div class="content col-md-12" data-name="name">
<label class="col-md-2" style="margin-top: 4px">Segment name: </label>
<p class="col-md-10"><?= isset($segment_first) && isset($segment_first->name) ? $segment_first->name : '' ?></p>
</div>-->
			<!--        @if(isset($segment_first))
							<div class="content row" data-name="description">
									<div class=" col-md-2">
											<h6>Description</h6>
									</div>
									<label class="col-md-2" style="margin-top: 4px">Segment description: </label>
							</div>
							@endif
							-->
			<div class=" content row">
				<div class="row col-md-6">
					<div class="col-md-3 fix-padding">
						<h6>Filter by</h6>
					</div>
					<div class="col-md-4 fix-padding" id="div-filter-one ">
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
					<div class="col-md-4 fix-padding" id="div-filter-two" style="display: none">
						<select name="Prop[two]" class="form-control">
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
					<div class="col-md-1 col-add-filter fix-padding" id="div-filter-tool">
						{{--<i class="fa fa-plus fa-2 fix-padding" aria-hidden="true" onclick="addFilter(this)"></i>--}}
						{{--<i class="fa fa-minus fa-2 fix-padding" aria-hidden="true" onclick="removeFilter(this)"--}}
						   {{--style="display: none"></i>--}}
					</div>
				</div>
				<div class="col-md-5 pull-right">
					<div class="col-md-12 col-md-offset-1" id="div-action">
						<div class="col-md-4">
							<button type="button" class="action button blue button-radius" onclick="execute()">
								<span class="label">Generate</span>
							</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="action button red button-radius" onclick="cancelExecute()">
								<span class="label">Cancel</span>
							</button>
						</div>
					</div>
				</div>
				<!--            <div class="col-md-2">
												<button type="button" class="action button red" onclick="cancelExecute()">
														<span class="label">Cancel</span>
												</button>
										</div>-->

				<!--        <canvas id="myChart" width="400" height="100" style="display: none"></canvas>-->
			</div>
			<div class="row">
				<div class="col-md-12" data-name="canvas-chart"></div>
			</div>
			<div class="row">
				<div class="col-md-12" id = "user_table"></div>
			</div>
		</div>
	</div>
@endsection

@section('additional')
	<script src="{{asset('js/Chart.js')}}"></script>
	<script type="text/javascript">
		$('select').select2();

		$('#refresh').on('click',function(){
			var that = $('#segment');
			var $btn = $(this).button('loading');
			$.get('/segment/{{$appcode}}/refresh/' + that.val(),function(data){
				console.log(data);
				$btn.button('reset');
				data = JSON.parse(data);
				$('#count').html(data.count);
			});
		});

		$('#segment').on('change', function () {
			var that = $(this);
			/*
			 * set name and description
			 */
			var name_div = $('div[data-name="name"]').find('p');
			if (name_div.length) {
				name_div.text(segments[that.val()] ? segments[that.val()]['name'] : '');
			}

			$('#action_update').attr('href', $('#action_update').attr('data-href') + '/' + that.val());
			$.get('/segment/{{$appcode}}/execute/' + that.val());
		
			$('#desc').html(segments[that.val()].description);
			$('#count').html(segments[that.val()].count);
			$('#startTime').html(segments[that.val()].startTime + ' to ' + segments[that.val()].endTime)
			console.log(segments);
			$('div[data-name="canvas-chart"]').html('');
			$("#user_table").empty();
			return false;
		});

		$('#action_delete').on('click', function () {
			var cf = confirm('This segment will be removed. Are you sure?');
			if (cf) {
				$.ajax({
					type: 'DELETE',
					dataType: 'JSON',
					url: '{{ URL::to('segment/'. $appcode .'/remove') }}' + '/' + $('#segment').val(),
					success: function (data) {
						if (data.success) {
							location.reload();
						}
					},
				});
			}
		});

		function addFilter(e) {
			$('#div-filter-two').show();
			$('#div-filter-two').find('span.select2-container').width('154px');
//                        $('#div-action').removeClass('col-md-offset-3');
//                        $('#div-action').addClass('col-md-offset-1');
			$(e).parent().find('.fa-minus').show();
			$(e).hide();
		}

		function removeFilter(e) {
			$('#div-filter-two').hide();
//                        $('#div-action').removeClass('col-md-offset-1');
//                        $('#div-action').addClass('col-md-offset-3');
			$(e).parent().find('.fa-plus').show();
			$(e).hide();
		}

		function cancelExecute() {
			$('#div-filter-one').find('select').val('').change();
			$('#div-filter-two').hide();
			$('#div-filter-tool').find('.fa-plus').show();
			$('#div-filter-tool').find('.fa-minus').hide();
			$('#div-action').removeClass('col-md-offset-1');
			$('#div-action').addClass('col-md-offset-3');
			$('div[data-name="canvas-chart"]').html('');
		}
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
			var appcode  =  '{{$appcode}}';
			var url = '';
			var data_get = {};
			var label_field = '';
			var demonstrate = '';
			if (field1 && field2 && field1 != field2) {
				url = '{{ URL::to('segment/'. $appcode .'/charttwofields') }}';
				data_get = {
					'segment_id': segment_id,
					'field1': field1,
					'field2': field2,
				};
				demonstrate = $('select[name="Prop[one]"]').find(':selected').text().toUpperCase() + ' and ' + $('select[name="Prop[two]"]').find(':selected').text().toUpperCase();
			}
			else if (field1 || field2) {
				url = '{{ URL::to('segment/'. $appcode .'/chartonefield') }}';
				data_get = {
					'segment_id': segment_id,
				};
				data_get.field = field1 ? field1 : field2;
				label_field = field1 ? $('select[name="Prop[one]"]').find(':selected').text() : $('select[name="Prop[two]"]').find(':selected').text()
				demonstrate = field1 ? $('select[name="Prop[one]"]').find(':selected').text().toUpperCase() : $('select[name="Prop[two]"]').find(':selected').text().toUpperCase()
			}
//			getTable(appcode,segment_id,field1,field2);
			if (url) {
				$.ajax({
					type: 'GET',
					dataType: 'JSON',
					url: url,
					data: data_get,
					success: function (data) {
						if (data.success && data.labels && data.datasets) {
							console.log(data);
							$.each(data.labels,function(index,value){
								if(value == "" || value == null){
									data.labels[index] = "N/A";
								}
							});
//                        if($('#segment_chart').length){
//                            $('#segment_chart').remove();
//                        }
							$('div[data-name="canvas-chart"]').html('');
							$('div[data-name="canvas-chart"]')
											.append('<p style="font-size: 14px;margin-bottom: 30px;margin-top: 20px;">This chart demonstrates that the filter by ' + demonstrate + '</p>');
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
									}]
								},
								legend: {
									display: false
								},
								stacked: true,
                                                                gridLines: {
                                                                    display: false
                                                                }
							};
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
                                                        var tmp_labels = [];
                                                        if(data_chart.labels.length < 7){
                                                            var add = 0;
                                                            add = Math.floor((7 - data_chart.labels.length)/2);
                                                            console.log(add);
                                                            $.each(data_chart.datasets, function(ddi, ddv){
                                                                tmp_labels = [];
                                                                var tmp_data = [];
                                                                for(var i = 0; i<add; i++){
                                                                    tmp_data.push(0);
                                                                    tmp_labels.push('');
                                                                }
                                                                $.each(ddv.data, function(ddv_data_i, ddv_data_v){
                                                                    tmp_data.push(ddv_data_v);
                                                                    tmp_labels.push(data_chart.labels[ddv_data_i]);
                                                                });
                                                                for(var i = 0; i<add; i++){
                                                                    tmp_data.push(0);
                                                                    tmp_labels.push('');
                                                                }
                                                                data_chart.datasets[ddi].data = tmp_data;
                                                            })
                                                            data_chart.labels = tmp_labels;
                                                            
                                                        }
							$('#myChart').show();
							$('#myChart').html('');
							var myBarChart = new Chart(ctx, {
								type: 'bar',
								data: data_chart,
								options: options
							});
						}
                                                if(data.users){
                                                    /*
                                                     * set list user here
                                                     */
                                                    var field1 = $('select[name="Prop[one]"]').val();
                                                    var field2 = $('select[name="Prop[two]"]').val();
                                                    getTable(segment_id, field1, field2,1);
                                                }
					},
				});
			}
		}

		function getTable(segment_id, field1, field2, page){
                    var url =  '{{ URL::to('segment/'. $appcode .'/usersbyfield') }}';
                    $.ajax({
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            segment_id: segment_id,
                            field1: field1,
                            field2: field2,
                            page: page
                        },
                        url: url,
                        success: function (data) {
                            if(data.success && data.users){
                                setTable(data.users,field1, field2,segment_id,page);
                            }
                            window.listuser = data;
                            console.log(data);
                        }
                    });
		}
                
        function setTable(users, field1, field2,segment_id,page_selected){
                    if(!Array.isArray(users)){
                        return false;
                    }
					var column2 = "";
                    if(field2){
                        column2 = "<td>"+field2.replace("_","")+"</td>";
                    }
					var label_field1  = field1.replace("_","");
					if(label_field1 == "reftype")
						label_field1 = "channel"
                    var column = "<tr style='text-transform: capitalize'><td>#</td>" +
                                    "<td width='100px'>Id</td>" +
                                    "<td>Name</td>" +
                                    "<td>Email</td>" +
                                    "<td>Phone</td>" +
                                    "<td>Last seen</td>" +
                                    "<td>"+label_field1+"</td>" +
                                    column2+
                                    "</tr>";
                    for(var i = 0; i < users.length; i++){
                        var item = users[i];
                        var columnfield = "";
                        if(field2){
                                columnfield = "<td>"+item[field2]+"</td>";
                        }
						var value_field1 = item[field1];
						if(value_field1 == null){
							value_field1 = "N/A";
						}
						var timespant = item._lastSeen;
						var strtime = timespant.toString();
						if(strtime.length == 10){
							timespant *= 1000;
						}else if(strtime.length > 13){
							timespant = parseInt(timespant/1000);
						}
						var date = new Date(timespant);
						var lastSeen = date.toLocaleString();
                        var cl = "<tr>" +
                                        "<td>"+(i+1)+"</td>" +
                                        "<td>"+item._mtid+"</td>"+
                                        "<td>"+item.name+"</td>"+
                                        "<td>"+item.email+"</td>"+
                                        "<td>"+item.phone+"</td>"+
                                        "<td>"+lastSeen+"</td>"+
                                        "<td>"+value_field1+"</td>"+
                                        columnfield+
                                        "</tr>";
                        column += cl;
                    }
                    var table = "<table class ='table table-hover'>"+column+"</table>";
					var b = "";
					//vitle: take page
					var count = 0;
					@if(isset($segment_first->count))
						count = $("#count").text();
						{{--console.log("fuck"+{{$segment_first->count}});--}}
						{{--console.log($("#count").text());--}}
						{{--console.log(document.getElementById("count").textContent);--}}
					@endif
					console.log("count: "+ count);
					var page = parseInt(count/15)+1;
					// end take page
					var max_index = page;
					var min_index = 1;
					if(page_selected >=2){
						min_index = page_selected-1;
					}
					if(page > page_selected + 8){
						max_index = page_selected+8;
					}
                        for(var i = min_index; i <= max_index; i++){
							var temp = "";
							if(i==page_selected)
								temp= "class='active'";
						b += "<li onclick='onSelectPage("+i+")' "+ temp +"><a href='#'>"+i+"</a></li>";
					}
					var select_page = "<nav>" +
							"<ul class='pagination'>" +
							"<li onclick='onSelectPage(1)' "+ temp +"><a href='#' aria-label='Previous'><span aria-hidden='true'>&lt;&lt;</span></a></li>" +
							b+
							"<li onclick='onSelectPage("+page+")' "+ temp +"><a href='#' aria-label='Next'><span aria-hidden='true'>&gt;&gt;</span></a></li>" +
							"</ul></nav>";
					table += select_page;
                    $("#user_table").empty();
                    $("#user_table").append(table);
                    return true;
                }

		function onSelectPage(i){
			var segment_id = $('#segment').val();
			var field1 = $('select[name="Prop[one]"]').val();
			var field2 = $('select[name="Prop[two]"]').val();
			getTable(segment_id,field1,field2,i);
		}
	</script>
@endsection