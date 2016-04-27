@extends('../layout/master')
@section('style')
	<link rel="stylesheet" href="{{asset('/css/typehead.css')}}"/>
	@endsection
@section('script')
	<script src="{{asset('/js/typehead.js')}}"></script>
<script type="text/javascript">

	function createSuggession(appid, typeid, field,  $dom)
	{
		var source = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '/api/' + appid + '/suggest/' + typeid +'/' + field +'/%QUERY',
				wildcard: '%QUERY'
			}
		});

		$dom.typeahead(null, {
			name: 'best-pictures',
			display: 'value',
			source: source
		});
	}

	var appid = "{{\Auth::user()->id}}";
 <?php $i = 0; ?>
	@foreach($conditions as $condition)

	@if($condition->select_type == 'user')
					createSuggession(appid,'user', "{{$condition->type}}", $('input[name="Segment[{{$i}}][value]"]'));
@else


					@endif

					<?php $i++ ?>
					@endforeach

    var conditions = [];
    var type_options = [];
    <?php
    $type_options = [];?>
    @if(isset($props) && is_array($props))
    @foreach ($props as $prop)
    <?php $type_options[] = (object)[
                                    'value' => $prop->code,
                                    'name' => $prop->name,
                                    'select_type' => 'user',
    ]
    ?>
    var operators = [];
    @foreach($prop->operators as $tmp_operator)
    operators.push({
            code: '{{$tmp_operator->code}}',
            name: '{{$tmp_operator->name}}'
    });
                                    @endforeach

    var tmp_type_option = {
                                            value: '{{ $prop->code ? $prop->code : ''}}',
                                            name: '{{$prop->code ? $prop->name : ''}}',
                                            operators: operators,
                                            select_type: 'user'
                                    };
    type_options.push(tmp_type_option);

    @endforeach
    @endif
    <?php
    $type_options[] = (object)[
                    'value' => '[disabled]',
                    'name' => 'OR SELECT',
                    'select_type' => '',
    ]; ?>

    type_options.push({
            value: '[disabled]',
            name: 'OR SELECT',
            select_type: ''
    });
    <?php
    if($actions && is_array($actions)):

    foreach ($actions as $action):

            $type_options[] = (object)[
                'value' => $action->codename,
                'name' => 'Did ' . $action->name,
                'select_type' => 'behavior',
            ];
            ?>

            var fields = [];
            <?php
            if($action->fields):
            foreach($action->fields as $tmp_field):
            ?>
            fields.push({
                    pcode: '{{$tmp_field->pcode}}',
                    pname: "{{$tmp_field->pname}}"
            });
            <?php
            endforeach;
            endif;
            ?>
            var tmp_type_option = {
                value: '{{$action->codename ? $action->codename : ''}}',
                name: 'Did {{$action->name ? $action->name : ''}}',
                fields: fields,
                select_type: 'behavior'
            };
            type_options.push(tmp_type_option);
        <?php
        endforeach;
        endif;
        $f_behaviors = [
                                        (object)['code' => 'sum', 'name' => 'Sum'],
                                        (object)['code' => 'avg', 'name' => 'Average'],
                                        (object)['code' => 'count', 'name' => 'Count']
        ];
        ?>
    var f_behavior = [
                                            {code: 'sum', name: 'Sum'},
                                            {code: 'avg', name: 'Average'},
                                            {code: 'count', name: 'Count'}
                                    ];
    var operator_behavior = [
            {code: '>', name: 'greater than'},
            {code: '>=', name: 'less or equal'},
            {code: '=', name: 'equal'},
            {code: '<', name: 'less than'},
            {code: '<=', name: 'greater or equal'}
    ];
    <?php $condtion_sub_operators = App\Enum\SegmentEnum::conditionSubOperators(); ?>

    $('#segment-date-range').dateRangePicker();
    //load segment time range

    $('#segment-date-range').data('dateRangePicker').setDateRange('2013-11-20','2013-11-25');
</script>
@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')
<div class="card row">
    <div class="header col-md-12">
        <form id='segment-form' class="form-segment row" method="post"
              action="{{URL::to('segment/write')}}">

            <div class="col-sm-12">
                <input type="hidden" class="row hidden" value="{{$segment->_id}}" name="id">
                <div class=" row">
                    <div class="col-sm-2">
                        <h6>Name</h6>
                        <input type="text" class="form-control " name="name" value="{{Input::old('name', $segment->name)}}"
                               placeholder="Enter name"/>
                        @if($errors->any())
                        <p class="errror">{{$errors->first('name')}}</p>
                        <p class="errror">{{$errors->first('conditions')}}</p>
                        @endif
                    </div>

                    <div class="col-sm-10">
                        <h6>Filter date</h6>


                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-addon">
                                <i class="pe-7s-date" style="font-size:26px; padding-left:6px;"></i>
                            </span>
                            <input type="text" class="form-control" id="segment-date-range" name="timerange">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="padding-top: 0;padding-bottom: 0">
                        <div class="row">
                            <div class="col-sm-12" style="padding-bottom: 0 ;padding-bottom: 0"><h6
                                    style="margin-bottom:0px">Filter condition</h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12" style="padding-top: 0;padding-bottom: 0">
                                <div data-name="condition-group" class="row" data-i-condition-max="{{count($conditions)}}">
                                    <div class="col-sm-12" style="padding-top: 0;padding-bottom: 0">
                                        <?php
                                        $i_condition = 0;
                                        foreach ($conditions as $condition):
                                            ?>
                                            @include('segment.partials.condition_item', [
                                            'condition' => $condition,
                                            'type_options' => $type_options,
                                            'i_condition' => $i_condition,
                                            ])
                                            <?php
                                            $i_condition++;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12" style="padding-top: 0">
                        <h6>Description</h6>
                        <!--<label class="col-md-12" style="margin-top: 10px">Segment description</label>-->

                        <input type="text" class="form-control" name="description" placeholder="Enter description"
                               value="{{isset($segment->description) ? $segment->description : ''}}"/>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="action button blue " onclick="backFn()">
                            <span class="label">Back</span>
                        </button>
                        <button type="submit" class="action button blue ">
                            <span class="label">{{$segment->_id ? 'Update' : 'Create'}}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('additional')
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script type="text/javascript">
		//    $('select').select2();
                function backFn(){
                    parent.history.back();
                    return false;
                }
                
		function typeChange(e) {
			var that = $(e);
			var containter = that.parent().parent();
			var condition_item = that.closest('div[data-name="condition-item"]');
			var i_condition = condition_item.attr('data-i-condition');
                        
			$.each(type_options, function (i, v) {
				if (v.value == that.val()) {
					if (v.select_type == 'user') {
						containter.find('input[name="Segment[' + i_condition + '][select_type]"]').val('user');
						containter.find('select[name="Segment[' + i_condition + '][operator]"]').html('');
//						containter.find('select[name="Segment[' + i_condition + '][operator]"]').parent().removeClass('col-md-2');
//						containter.find('select[name="Segment[' + i_condition + '][operator]"]').parent().addClass('col-md-4');
						//containter.find('select[name="Segment[' + i_condition + '][type]"]').parent().removeClass('col-md-2');
						//containter.find('select[name="Segment[' + i_condition + '][type]"]').parent().addClass('col-md-4');
						containter.find('select[name="Segment[' + i_condition + '][f]"]').parent().hide();
						containter.find('select[name="Segment[' + i_condition + '][field]"]').parent().hide();
                                                containter.find('select[name="Segment[' + i_condition + '][operator]"]').html('');
						if (v.operators.length) {
                                                    $.each(v.operators, function (oi, ov) {
                                                        containter.find('select[name="Segment[' + i_condition + '][operator]"]').append('<option value="' + ov.code + '">' + ov.name + '</option>');
                                                    });
						}
                                                condition_item.find('div[data-name="add-condition"]').hide();
					}
					else {
						containter.find('input[name="Segment[' + i_condition + '][select_type]"]').val('behavior');
						containter.find('select[name="Segment[' + i_condition + '][operator]"]').parent().removeClass('col-md-4');
						containter.find('select[name="Segment[' + i_condition + '][operator]"]').parent().addClass('col-md-2');
						//containter.find('select[name="Segment[' + i_condition + '][type]"]').parent().removeClass('col-md-4');
						//containter.find('select[name="Segment[' + i_condition + '][type]"]').parent().addClass('col-md-2');
						containter.find('select[name="Segment[' + i_condition + '][f]"]').parent().show();
						containter.find('select[name="Segment[' + i_condition + '][field]"]').parent().show();
						containter.find('select[name="Segment[' + i_condition + '][operator]"]').html('');
						$.each(operator_behavior, function (obi, obv) {
							containter.find('select[name="Segment[' + i_condition + '][operator]"]').append('<option value="' + obv.code + '">' + obv.name + '</option>');
						});
                                                containter.find('select[name="Segment[' + i_condition + '][f]"]').html('');
						$.each(f_behavior, function (fi, fv) {
							containter.find('select[name="Segment[' + i_condition + '][f]"]').append('<option value="' + fv.code + '">' + fv.name + '</option>');
						});
                                                $('select[name="Segment[' + i_condition + '][field]"]').html('');
						if (v.fields.length) {
                                                    $.each(v.fields, function (fieldi, fieldv) {
                                                            $('select[name="Segment[' + i_condition + '][field]"]').append('<option value="' + fieldv.pcode + '">' + fieldv.pname + '</option>');
                                                    });
						}
						var add_condition = containter.find('div[data-name="add-condition"]');
						add_condition.show();
					}
					containter.find('input[name="Segment[' + i_condition + '][value]"]').val('');
					return false;
				}

			});
			/*
			 * delete condition sub
			 */
			condition_item = that.closest('div[data-name="condition-item"]');
			condition_item.find('div[data-name="condition-sub-group"]').html('');
		}

		function changeField(e) {
			console.log('here');
		}
                
                
                
		function addFilter(e) {
			var that = $(e);
			var condition_item = that.closest('div[data-name="condition-item"]');
			var html = '{!!$html_condition_item !!}';
			condition_item.after(html);
			/*
			 * fill select type, select operator
			 */
			condition_item.next().find('select[name="Segment[i_condition_replace][type]"]').html('');
			$.each(type_options, function (i, v) {
				var disabled = v.value == '[disabled]' ? 'disabled' : '';
				condition_item.next().find('select[name="Segment[i_condition_replace][type]"]')
								.append('<option value="' + v.value + '" ' + disabled + '>' + v.name + '</option>');
				if (i == 0) {
					var vos = v.operators;
					condition_item.next().find('select[name="Segment[i_condition_replace][operator]"]').html('');
					$.each(vos, function (vos_i, vos_v) {
						condition_item.next().find('select[name="Segment[i_condition_replace][operator]"]').append('<option value="' + vos_v.code + '">' + vos_v.name + '</option>');
					});
				}
			});
			var condition_group = condition_item.closest('div[data-name="condition-group"]');
			html = condition_item.next().html().replace(/i_condition_replace/g, condition_group.attr('data-i-condition-max'));
			condition_item.next().attr('data-i-condition', condition_group.attr('data-i-condition-max'));
			condition_group.attr('data-i-condition-max', parseInt(condition_group.attr('data-i-condition-max')) + 1);
			condition_item.next().html(html);
			checkDisableDelete();
		}

		function deleteFilter(e) {
			var that = $(e).closest('div[data-name="condition-item"]');
			that.remove();
			checkDisableDelete();
		}

		function checkDisableDelete() {
			var count = $('.add').length;
			if (count == 1) {
				$('.delete').hide();
			}
			else {
				$('.delete').show();
			}
                        
                        var count_filter = $('a[data-name="a-add-filter"]').length;
                        if(count_filter == 1){
                            $('a[data-name="a-delete-filter"]').hide();
                        }
                        else{
                            $('a[data-name="a-delete-filter"]').show();
                        }
		}
		checkDisableDelete();

		function addCondition(e) {
			var that = $(e);
			var condition_item = that.closest('div[data-name="condition-item"]');
			var i_condition = condition_item.attr('data-i-condition');

			var selected_type = that.closest('div[data-name="condition-item"]').find('select[name="Segment[' + i_condition + '][type]"]').val();
			var html = '{!! $html_sub_condition  !!}';
			var condition_sub_group = that.closest('div[data-name="condition-item"]').find('div[data-name="condition-sub-group"]');

			var condition_sub_item = that.closest('div[data-name="condition-sub-item"]');
			if (condition_sub_item.length) {
				condition_sub_item.after(html);
				/*
				 * fill files for item
				 */
				$.each(type_options, function (i, v) {
					if (v.value == selected_type) {
						if (v.fields && v.fields.length) {
							$.each(v.fields, function (vf_i, vf_v) {
								condition_sub_item.next().find("select[name*='cs_field']").append('<option value="' + vf_v.pcode + '">' + vf_v.pname + '</option>');
							});
						}
						return false;
					}
				});
				html = condition_sub_item.next().html().replace(/i_condition_sub_replace/g, condition_sub_group.attr('data-i-condition-sub-max'));
				html = html.replace(/i_condition_replace/g, condition_item.attr('data-i-condition'));
				condition_sub_group.attr('data-i-condition-sub-max', parseInt(condition_sub_group.attr('data-i-condition-sub-max')) + 1);
				condition_sub_item.next().html(html);
			}
			else {
				condition_item.find('div[data-name="condition-sub-group"]').append(html);
				/*
				 * fill files for item
				 */
				$.each(type_options, function (i, v) {
					if (v.value == selected_type) {
						if (v.fields.length) {
							$.each(v.fields, function (vf_i, vf_v) {
								condition_item.find('div[data-name="condition-sub-group"]')
												.find('div[data-name="condition-sub-item"]')
												.last()
												.find('select[name*="cs_field"]')
												.append('<option value="' + vf_v.pcode + '">' + vf_v.pname + '</option>');
							});
						}
						return false;
					}
				});
				html = condition_item.find('div[data-name="condition-sub-item"]').last().html().replace(/i_condition_sub_replace/g, condition_item
												.find('div[data-name="condition-sub-item"]').length - 1);
				html = html.replace(/i_condition_replace/g, condition_item.attr('data-i-condition'));
				condition_item.find('div[data-name="condition-sub-item"]').last().html(html);
			}
			return false;
		}

		function deleteCondition(e) {
			var that = $(e);
			var condition_sub_item = that.closest('div[data-name="condition-sub-item"]');
			if (condition_sub_item.length) {
				condition_sub_item.remove();
			}
		}
	</script>
@endsection

