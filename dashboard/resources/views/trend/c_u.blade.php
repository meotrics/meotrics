<?php
?>
@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')

@section('content')

	<div class="card row">
		<div class="header col-md-12">
			<form class="form-horizontal" method="post" action="{{URL::to('trend/write')}}">
				<input type="hidden" name="Trend[_id]" value="{{$trend->_id}}"/>
				<div class="row">
				<div class="form-group col-md-12">
					<label class="col-md-2" style="margin-top: 10px">Trend name</label>
					<div class="col-md-4">
						<input type="text" class="form-control " name="Trend[name]" required="" value="{{isset($trend->name) ? $trend->name: ""}}"/>
						@if($errors->any())
							<p class="errror">{{$errors->first('name')}}</p>
						@endif
					</div>
				</div>
				</div>
				<div class="row">
				<div class="form-group col-md-12">
					<label class="col-md-2" style="margin-top: 10px">List top</label>
					<div class="col-md-4">
						<select class="form-control" id="typeid" name="Trend[typeid]">
							@if($actiontypes)
								$objects = [];
								@foreach($actiontypes as $actiontype)
									@if($actiontype->codename && $actiontype->name)
										$objects[$actiontype->codename] = $actiontype->fields;
										<option value="{{$actiontype->codename}}"
														{{$trend->typeid == $actiontype->codename ? 'selected=""' : ''}}>
											{{$actiontype->name}}
										</option>
									@endif
								@endforeach
							@endif
						</select>
					</div>
				</div>

					<div class="col-md-4">
						<select class="form-control" id="object" name="Trend[object]">
							<option value="">Object list</option>
						</select>
					</div>
				</div>
				<div class="row">
				<div class="form-group col-md-12">
					<label class="col-md-2" style="margin-top: 10px">Which has</label>
					<div class="col-md-4">
						<select class="form-control" id="meotrics" name="Trend[meotrics]">
							<option value="">Meotrics list</option>
						</select>
						<input type="hidden" id="operation" name="Trend[operation]" value="{{$trend->operation}}"/>
						<input type="hidden" id="param" name="Trend[param]" value="{{$trend->param}}"/>
					</div>
				</div>
				</div>
				<div class="form-group col-md-12">
					<label class="col-md-2" style="margin-top: 10px">Order</label>
					<div class="col-md-4">
						<select class="form-control" id="order" name="Trend[order]">
							<option value="1" {{$trend->order == 1 ? 'selected=""' : ''}}>ASC</option>
							<option value="-1" {{$trend->order == -1 ? 'selected=""' : ''}}>DESC</option>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-push-2 col-sm-2">
						<button type="submit" class="action button blue">

							<span class="label">{{$trend->_id ? 'Update' : 'Create'}}</span>
						</button>
					</div>

				</div>
			</form>
		</div>

	</div>


@endsection

@section('additional')
	<script type="text/javascript">
		var objects = {};
		@if($actiontypes)
						@foreach($actiontypes as $actiontype)
						objects['{{$actiontype->codename}}'] = [];
		@foreach($actiontype->fields as $field)
						objects['{{$actiontype->codename}}'].push({
			'pcode': '{{$field->pcode}}',
			'pname': '{{$field->pname}}',
		});
		@endforeach
@endforeach
@endif
actionChange($('#typeid').val());
		$('#typeid').on('change', function () {
			actionChange($(this).val());
		});

		function actionChange(typeid) {
			$('#object').html('');
			if (objects[typeid] && objects[typeid].length) {
				$('#object').show();
				$.each(objects[typeid], function (i, v) {
					$('#object').append('<option value="' + v.pcode + '">' + v.pname + '</option>');
				});
			}
			else {
				$('#object').hide();
			}

			$.ajax({
				type: 'GET',
				dataType: 'JSON',
				url: '{{ URL::to('trend/meotrics', ['app_id' => $app_id]) }}' + '/action_id/' + typeid,
				success: function (data) {
					if (data.success && data.meotrics) {
						$('#meotrics').html('');
						var selected_value = '';
						var selected_key = ''
						$.each(data.meotrics, function (i, v) {
							if ($('#operation').val() == v.operation && $('#param').val() == v.param) {
								selected_value = v;
								selected_key = i;
							}
							$('#meotrics').append('<option value="' + i + '" \n\
                        data-operation="' + v.operation + '"\n\
                        data-param="' + v.param + '">' + v.name + '</option>');
						});
						if (!selected_value) {
							selected_value = data.meotrics[0];
							selected_key = 0;
						}
						$('#meotrics').val(selected_key);
						meotricsChange(selected_value.operation, selected_value.param);
					}
				},
			});
			return false;
		}

		$('#meotrics').on('change', function () {
			meotricsChange($(this).attr('data-operation'), $(this).attr('data-param'));
		});

		function meotricsChange(operation, param) {
			$('#operation').val(operation);
			$('#param').val(param);
		}
	</script>
@endsection