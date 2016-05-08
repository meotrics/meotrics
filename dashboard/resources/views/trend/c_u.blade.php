<?php
?>
@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')

@section('content')
	<div class="row">
		<div class="card col-sm-12">
			<div class="row">
				<div class="header col-md-12">
					<form class="form-horizontal pb10" method="post" action="{{URL::to('trend/write')}}">
						<input type="hidden" name="Trend[_id]" value="{{$trend->_id}}"/>
						<div class="row">
							<div class="col-sm-2">
								<h6 class="pull-right" style="margin-top: 11px">Display Name</h6>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control " name="Trend[name]" required=""
												value="{{isset($trend->name) ? $trend->name: ""}}"/>
								@if($errors->any())
									<p class="errror">{{$errors->first('name')}}</p>
								@endif
							</div>
						</div>

						<div class="row">
							<div class="col-sm-2">
								<h6 class="pull-right" style="margin-top: 11px">Description</h6>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control " name="Trend[desc]"
												value="{{isset($trend->desc) ? $trend->desc: ""}}"/>
							</div>
						</div>

						<div class="row mt">
							<div class="col-sm-2">
								<h6 class="pull-right" style="margin-top: 11px">LIST</h6>
							</div>
							<div class="col-sm-2">
								<select class="form-control" id="order" name="Trend[order]">
									<option value="1" {{$trend->order == 1 ? 'selected=""' : ''}}>TOP</option>
									<option value="-1" {{$trend->order == -1 ? 'selected=""' : ''}}>LEAST</option>
								</select>
							</div>
							<div class="col-sm-2">
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
							<div class="col-md-2">
								<select class="form-control" id="object" name="Trend[object]">
									<option value="">Object list</option>
								</select>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-2">
								<h6 class="pull-right" style="margin-top: 11px">by</h6>
							</div>
							<div class="col-sm-6">
								<select class="form-control" id="meotrics" name="Trend[meotrics]">
								</select>
								<input type="hidden" id="operation" name="Trend[operation]" value="{{$trend->operation}}"/>
								<input type="hidden" id="param" name="Trend[param]" value="{{$trend->param}}"/>
							</div>
						</div>

						<div class="row mt">
							<div class="col-sm-2">

							</div>

							<div class="col-sm-5">
                                                            <div class="back" onclick="backFn()">
                                                                <i class="fa fa-fw fa-chevron-left"></i>
                                                            </div>
								<button type="submit" class="action button blue button-radius">
									<span class="label">{{$trend->_id ? 'Update' : 'Create'}}</span>
								</button>
                                                                
<!--								<a href="/trend" class="action button button-radius">
									<span class="label">Back</span>
								</a>-->
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('additional')
	<script type="text/javascript">
            function backFn(){
                parent.history.back();
                return false;
            }
		var objects = {};
		var op = {};
		@foreach($actiontypes as $actiontype)
						objects['{{$actiontype->codename}}'] = [];
		op['{{$actiontype->codename}}'] = [];
		@if(isset($actiontype->deftrendobjects))
						@foreach($actiontype->deftrendobjects as $f)
						op['{{$actiontype->codename}}'].push({
			desc: "{{$f->desc}}",
			param: "{{$f->param}}",
			operation: "{{$f->operation}}"
		});
		@endforeach
						@else
						op['{{$actiontype->codename}}'].push({
			desc: "Number of action occured",
			param: "_id",
			operation: "count"
		});
		op['{{$actiontype->codename}}'].push({
			desc: "Number of unique user that did the action",
			param: "_mtid",
			operation: "count"
		});
		@foreach($actiontype->fields as $field)
						op['{{$actiontype->codename}}'].push({
			@if(isset($field->pname)) desc: "Sum of {{$field->pname}}", @endif
							@if(isset($field->pcode)) param: "{{$field->pcode}}", @endif
			operation: "sum"
		});

		op['{{$actiontype->codename}}'].push({
			@if(isset($field->pname))desc: "Average of {{$field->pname}}", @endif
							@if(isset($field->pcode))param: "{{$field->pcode}}", @endif
			operation: "avg"
		});
		@endforeach
						@endif

						@if(isset($actiontype->deftrendfields))
						@foreach($actiontype->deftrendfields as $field)
						objects['{{$actiontype->codename}}'].push({'pcode': '{{$field->pcode}}', 'pname': '{{$field->pname}}'});
		@endforeach
						@else
						objects['{{$actiontype->codename}}'] = [];
		@foreach($actiontype->fields as $field)
						objects['{{$actiontype->codename}}'].push({
			@if(isset($field->pcode))'pcode': '{{$field->pcode}}',@endif
			@if(isset($field->pname))'pname': '{{$field->pname}}'@endif
		});
		@endforeach
		@endif
	@endforeach


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

			$('#meotrics').html('');
			var selected_value = '';
			var selected_key = '';
			$.each(op[typeid], function (i, v) {
				if ($('#operation').val() == v.operation && $('#param').val() == v.param) {
					selected_value = v;
					selected_key = i;
				}
				$('#meotrics').append('<option value="' + i + '" \n\
                        data-operation="' + v.operation + '"\n\
                        data-param="' + v.param + '">' + v.desc + '</option>');
			});
			if (!selected_value) {
				selected_value = op[typeid][0];
				selected_key = 0;
				console.log(selected_value, selected_key)
			}
			$('#meotrics').val(selected_key);
			meotricsChange(selected_value.operation, selected_value.param);

			return false;
		}

		$('#meotrics').on('change', function () {
			var $me = $(this).find('[value=' + $(this).val() + ']');
			meotricsChange($me.attr('data-operation'), $me.attr('data-param'));
		});

		function meotricsChange(operation, param) {
			console.log(operation, param)
			$('#operation').val(operation);
			$('#param').val(param);
		}
	</script>
@endsection