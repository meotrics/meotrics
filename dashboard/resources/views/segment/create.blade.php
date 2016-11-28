@extends('layout.master')
@section('style')
    <link rel="stylesheet" href="{{asset('/css/typehead.css')}}"/>
@endsection
@section('script')
    <script src="{{asset('/js/typehead.js')}}"></script>
    <script type="text/javascript">
        function createSuggession(appid, typeid, field, $dom) {
            if(typeid == 'user')
                typeid = 'pageview';
            var source = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '//api.meotrics.com/' + appid + '/suggest/' + typeid + '/' + field + '/%QUERY',
//                    url: '//api.meotrics.dev/' + appid + '/suggest/' + typeid + '/' + field + '/%QUERY',
                    wildcard: '%QUERY'
                }
            });
            $dom.typeahead({minLength: 0, highlight: true}, {
                name: 'best-pictures',
                display: 'value',
                source: source,
                limit: 14
            });
        }

        function destroySuggession($dom) {
            $dom.typeahead("destroy");
        }


        var appid = "{{$appcode}}";
        <?php $i = 0; ?>
        @foreach($conditions as $condition)
                @if($condition->select_type == 'user')
                        @if($condition->operator == 'eq')
                                createSuggession(appid, 'user', "{{$condition->type}}", $('input[name="Segment[{{$i}}][value]"]'));
                        @endif
                @else
                @if($condition->operator == '=' || $condition->operator == 'eq')
                        createSuggession(appid, "{{$condition->type}}", "{{$condition->field}}", $('input[name="Segment[{{$i}}][value]"]'));
                @endif
                <?php $i_cc = 0; ?>
                @foreach($condition->conditions as $c_condition)
                        @if($c_condition->cs_operator == 'eq')
                                createSuggession(appid, "{{$condition->type}}", "{{$c_condition->cs_field}}", $('input[name="Segment[{{$i}}][conditions][{{$i_cc}}][cs_value]"]'));
                        @endif
                        <?php $i_cc++; ?>
                        @endforeach
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
                                'select_type' => $action->codename,
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
            select_type: '{{$action->codename ? $action->codename : ''}}'
        };
        type_options.push(tmp_type_option);
                <?php
                endforeach;
                endif;
                $f_behaviors = [
                                            (object)['code' => 'count', 'name' => 'Count'],
                                            (object)['code' => 'sum', 'name' => 'Sum'],
                                            (object)['code' => 'avg', 'name' => 'Average']
                ];
                                        $f_behaviors_purchase = [
                                            (object)['code' => 'count', 'name' => 'Count'],
                ];
                ?>
var f_behavior = [
            {code: 'count', name: 'Count'},
            {code: 'sum', name: 'Sum'},
            {code: 'avg', name: 'Average'},
        ];
        var f_behavior_purchase = [{code: 'count', name: 'Count'}];
//        var operator_behavior = [
//            {code: 'gt', name: 'greater than'},
//            {code: 'gte', name: 'greater or equal'},
//            {code: 'eq', name: 'equal'},
//            {code: 'lt', name: 'less than'},
//            {code: 'lte', name: 'less or equal'}
//        ];
        var operator_behavior = [
            {code: '>', name: 'greater than'},
            {code: '>=', name: 'greater or equal'},
            {code: '=', name: 'equal'},
            {code: '<', name: 'less than'},
            {code: '<=', name: 'less or equal'}
        ];
        <?php $condtion_sub_operators = App\Enum\SegmentEnum::conditionSubOperators(); ?>
        //load segment time range
        var now = new Date();
        var date_string = now.toLocaleDateString();
        var arr_date = date_string.split("/");
        var first_date = arr_date[2]+"-"+(arr_date[0]-1)+"-"+arr_date[1];
        var end_date = arr_date[2]+"-"+arr_date[0]+"-"+arr_date[1];

        function selectDate(id,input_date){
            $(function() {
                var tp = $('#' + id).dateRangePicker({
                        autoClose: true,
                        singleDate : true,
                        showShortcuts: false,
                        singleMonth: true,
                        format: 'DD-MM-YYYY'
                        });
                tp.bind('datepicker-change', function (event, obj) {
                    var myDate= obj.value;
                    myDate=myDate.split("-");
                    var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
                    var date = new Date(newDate).getTime()/1000;
                    input_date.val(date);
                });
            });
        }
        window.onload = function(e){
            var arrtime = $('select[value="_ctime"]');
            console.log(arrtime);   
            for(var i = 0;i<arrtime.length;i++){
                var that = $(arrtime[i]);
                var container =  that.parent().parent();
                var divInput =  container.find('div[data-name="condition-item-value"]');
                if(divInput.length == 1){
                    var input = divInput.find($('input'));
                }else{
                    var input = container.find($('input'));
                }
                input.hide();
                var timeInt = input.val();
                var datetime = new Date(timeInt*1000).toLocaleDateString();
                datetime = datetime.replace(/[/]/g,'-');;
                var id = "time_"+i;
                var time = '<input class="form-control mr" id="' + id + '">';
                if(divInput.length == 1){
                    container.find('div[data-name="condition-item-value"]').append(time);
                }else{
                    container.find('div[data-name="condition-sub-value"]').append(time);
                }
                selectDate(id, that);
                $('#'+id).val(datetime);
            }
        }
    </script>
@endsection

@section('content')
    <div class="card row">
        <div class="header col-md-12">
            <form id='segment-form' class="form-segment row" method="post"
                  action="{{URL::to('segment/'. $appcode . '/write')}}">

                <div class="col-md-12">
                    <input type="hidden" class="row hidden" value="{{$segment->_id}}" name="id">

                    <div class=" row">
                        <div class="col-md-1">
                            <h6>Name</h6>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control " name="name"
                                   value="{{Input::old('name', $segment->name)}}"
                                   placeholder="Enter name"/>
                            @if($errors->any())
                                <p class="errror">{{$errors->first('name')}}</p>
                                <p class="errror">{{$errors->first('conditions')}}</p>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1" style="padding-top: 0">
                            <h6>Description</h6>
                        </div>
                        <div class="col-md-6">
                            <!--<label class="col-md-12" style="margin-top: 10px">Segment description</label>-->
                            <input type="text" class="form-control" name="description" placeholder="Enter description"
                                   value="{{isset($segment->description) ? $segment->description : ''}}"/>
                        </div>
                    </div>
                    <div>
                        <div class="row" id="div-filter-date" style="display: none">
                            <div class="col-md-1" style="padding-top: 0">
                                <h6>Filter date</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group" style="width: 300px;">
                            		<span class="input-group-addon">
                                		<i class="pe-7s-date" style="font-size:26px; padding-left:6px;"></i>
                            		</span>
                                    <input type="text" class="form-control" id="segment-date-range" name="timerange">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="padding-top: 0;padding-bottom: 0">
                            <div class="row">
                                <div class="col-md-12" style="padding-bottom: 0 ;padding-bottom: 0"><h6
                                            style="margin-bottom:0px">Filter condition</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12" style="padding-top: 0;padding-bottom: 0">
                                    <div data-name="condition-group" class="row"
                                         data-i-condition-max="{{count($conditions)}}">
                                        <div class="col-md-12" style="padding-top: 0;padding-bottom: 0">
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
                        <div class="col-md-12">
                            <!--                        <button type="button" class="action button blue button-radius" onclick="backFn()">
                                                                                    <span class="label">Back</span>
                                                                            </button>-->
                            <div class="back" onclick="backFn()">
                                <i class="fa fa-fw fa-chevron-left"></i>
                            </div>
                            <button type="submit" class="action button blue button-radius">
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

    <script type="text/javascript">
        //    $('select').select2();
        function backFn() {
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
                        $('#label_for_did_action').parent().hide();

                        containter.find('input[name="Segment[' + i_condition + '][select_type]"]').val('user');
                        containter.find('select[name="Segment[' + i_condition + '][operator]"]').html('');
                        containter.find('select[name="Segment[' + i_condition + '][f]"]').parent().hide();
                        containter.find('select[name="Segment[' + i_condition + '][field]"]').parent().hide();
                        containter.find('select[name="Segment[' + i_condition + '][operator]"]').html('');
                        destroySuggession(containter.find('input[name="Segment[' + i_condition + '][value]"]'));
                        if (v.operators.length) {
                            $.each(v.operators, function (oi, ov) {
                                containter.find('select[name="Segment[' + i_condition + '][operator]"]').append('<option value="' + ov.code + '">' + ov.name + '</option>');
                            });
                        }
                        if (containter.find('select[name="Segment[' + i_condition + '][operator]"]').val() == 'eq') {
                            createSuggession(appid, 'user', containter.find('select[name="Segment[' + i_condition + '][type]"]').val(), containter.find('input[name="Segment[' + i_condition + '][value]"]'));
                        }
                        else {
                            destroySuggession(containter.find('input[name="Segment[' + i_condition + '][value]"]'));
                        }
                        condition_item.find('div[data-name="add-condition"]').hide();
                        if(that.val() == '_ctime'){
                            containter.find('input[name="Segment[' + i_condition + '][value]"]').hide();
                            var id = "a"+i_condition;
                            var time = '<input class="form-control mr" id="'+id+'">';
                            containter.find('div[data-name="condition-item-value"]').append(time);
                            selectDate(id,containter.find('input[name="Segment[' + i_condition + '][value]"]'));
                        }
                    }
                    else {
                        $('#label_for_did_action').parent().show();
                        $('#label_for_did_action').text('With number of time');

                        containter.find('input[name="Segment[' + i_condition + '][select_type]"]').val('behavior');
                        containter.find('select[name="Segment[' + i_condition + '][operator]"]').parent().removeClass('col-md-4');
                        containter.find('select[name="Segment[' + i_condition + '][operator]"]').parent().addClass('col-md-2');
                        containter.find('select[name="Segment[' + i_condition + '][f]"]').parent().hide();
                        containter.find('select[name="Segment[' + i_condition + '][field]"]').parent().hide();
                        containter.find('select[name="Segment[' + i_condition + '][operator]"]').html('');
                        $.each(operator_behavior, function (obi, obv) {
                            containter.find('select[name="Segment[' + i_condition + '][operator]"]').append('<option value="' + obv.code + '">' + obv.name + '</option>');
                        });
                        if (containter.find('select[name="Segment[' + i_condition + '][operator]"]').val() == '='|| containter.find('select[name="Segment[' + i_condition + '][operator]"]').val() == 'eq') {
                            createSuggession(appid, containter.find('select[name="Segment[' + i_condition + '][type]"]').val(), containter.find('select[name="Segment[' + i_condition + '][field]"]').val(), containter.find('input[name="Segment[' + i_condition + '][value]"]'));
                        }
                        else {
                            destroySuggession(containter.find('input[name="Segment[' + i_condition + '][value]"]'));
                        }
                        containter.find('select[name="Segment[' + i_condition + '][f]"]').html('');
                        var tmp_behavior = that.val() == 'purchase' ? f_behavior : f_behavior_purchase;
                        $.each(tmp_behavior, function (fi, fv) {
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
            throwConditions();
        }

        function changeField(e) {
            var that = $(e);
            var containter = that.parent().parent();
            var condition_item = that.closest('div[data-name="condition-item"]');
            getOperator(condition_item, 'condition-item-operator', that.val());
        }


        function changeSubField(e) {
            var that = $(e);
            var containter = that.parent().parent();
            var condition_item = that.closest('div[data-name="condition-sub-item"]');
            var i_condition = containter.attr("data-i-condition-sub");
            var data_i_condition = containter.parent().parent().parent().parent().attr("data-i-condition");
            getOperator(condition_item, 'condition-sub-operator', that.val());
            // check ctime
            if(that.val() == '_ctime'){
                containter.find('input[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_value]"]').hide();
                var id = data_i_condition+"a"+i_condition;
                var time = '<input class="form-control mr" id="'+id+'">';
                containter.find('div[data-name="condition-sub-value"]').append(time);
                selectDate(id,containter.find('input[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_value]"]'));
            }else{
                containter.find('input[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_value]"]').val('');
                if (containter.find('select[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_field]"]').val() == '='|| containter.find('select[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_field]"]').val() == 'eq') {
                    createSuggession(appid, containter.find('select[name="Segment[' + i_condition + '][type]"]').val(),
                            containter.find('select[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_field]"]').val(),
                            containter.find('input[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_value]"]'));
                }
                else {
                    destroySuggession(containter.find('input[name="Segment[' + data_i_condition + '][conditions][' + i_condition + '][cs_value]"]'));
                }
            }
        }

        function getOperator(item, operator_name, field_val) {
            var div_operator = item.find('div[data-name="' + operator_name + '"]');
            var select_operator = div_operator.find('select');
            var operator_behavior = [
                {code: 'gt', name: 'greater than'},
                {code: 'gte', name: 'greater or equal'},
                {code: 'eq', name: 'equal'},
                {code: 'lt', name: 'less than'},
                {code: 'lte', name: 'less or equal'}
            ];
//            var operator_behavior = [
//                {code: '>', name: 'greater than'},
//                {code: '>=', name: 'greater or equal'},
//                {code: '=', name: 'equal'},
//                {code: '<', name: 'less than'},
//                {code: '<=', name: 'less or equal'}
//            ];
            var operator_default = [
                {code: 'contain', name: 'Contain'},
                {code: 'eq', name: 'Equal'},
                {code: 'start_with', name: 'Start with'},
                {code: 'end_with', name: 'End with'},
                {code: 'ncon', name: 'Not contain'},
            ];
            var operator = operator_default;
            if (field_val == 'pid' || field_val == 'cid' || field_val == 'price' ||
                    field_val == 'quantity' || field_val == '_ctime' || field_val == 'oid' ||
                    field_val == 'level' || field_val == 'userid') {
                operator = operator_behavior;
            }
            select_operator.html('');
            $.each(operator, function (oi, ov) {
                select_operator.append('<option value="' + ov.code + '">' + ov.name + '</option>');
            });
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
            throwConditions();
        }

        function deleteFilter(e) {
            var that = $(e).closest('div[data-name="condition-item"]');
            that.remove();
            checkDisableDelete();
            throwConditions();
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
            if (count_filter == 1) {
                $('a[data-name="a-delete-filter"]').hide();
            }
            else {
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
                html = condition_sub_item.next().html().replace(/i_condition_sub_replace/g, condition_item.find('div[data-name="condition-sub-item"]').length - 1);
                html = html.replace(/i_condition_replace/g, condition_item.attr('data-i-condition'));
                condition_sub_group.attr('data-i-condition-sub-max', parseInt(condition_sub_group.attr('data-i-condition-sub-max')) + 1);
                condition_sub_item.next().html(html);
                condition_sub_item.next().attr("data-i-condition-sub", condition_item.find('div[data-name="condition-sub-item"]').length - 1);

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
                html = condition_item.find('div[data-name="condition-sub-item"]').last().html().replace(/i_condition_sub_replace/g, condition_item.find('div[data-name="condition-sub-item"]').length - 1);
                html = html.replace(/i_condition_replace/g, condition_item.attr('data-i-condition'));
                condition_item.find('div[data-name="condition-sub-item"]').last().html(html);
                condition_item.find('div[data-name="condition-sub-item"]').last().attr("data-i-condition-sub",condition_item.find('div[data-name="condition-sub-item"]').length - 1);

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

        function operatorChange(e) {
            var that = $(e);
                var container = that.closest('div[data-name="condition-item"]');
            if (container.length) {
                var i_condition = container.attr('data-i-condition');
                console.log(container.find('select[name="Segment[' + i_condition + '][operator]"]').val());
                if (container.find('select[name="Segment[' + i_condition + '][operator]"]').val() == '='|| container.find('select[name="Segment[' + i_condition + '][operator]"]').val() == 'eq') {
                   console.log("create");
                    createSuggession(appid, container.find('select[name="Segment[' + i_condition + '][type]"]').val(), container.find('select[name="Segment[' + i_condition + '][field]"]').val(), $('input[name="Segment[' + i_condition + '][value]"]'));
                }
                else {
                    console.log("destroy");
                    destroySuggession(container.find('input[name="Segment[' + i_condition + '][value]"]'));
                }
            }
        }

        function operatorSubChange(e) {
            var that = $(e);
            var condition_item = that.closest('div[data-name="condition-item"]');
            var container_sub_item = that.closest('div[data-name="condition-sub-item"]');
            console.log(that.val());
            if (condition_item.length && container_sub_item.length) {
                var i_condition = condition_item.attr('data-i-condition');
                var i_sub_condition = container_sub_item.attr('data-i-condition-sub');
                  if (that.val() == 'eq') {
                    createSuggession(appid, condition_item.find('select[name="Segment[' + i_condition + '][type]"]').val(),
                            container_sub_item.find('select[name="Segment[' + i_condition + '][conditions][' + i_sub_condition + '][cs_field]"]').val(),
                            $('input[name="Segment[' + i_condition + '][conditions][' + i_sub_condition + '][cs_value]"]'));
                }
                else {
                    destroySuggession($('input[name="Segment[' + i_condition + '][conditions][' + i_sub_condition + '][cs_value]"]'));
                }
            }
        }

        function throwConditions() {
            var show_filter_date = false;
            $('div[data-name="condition-item"]').each(function () {
                var i_condition = $(this).attr('data-i-condition');
                if ($(this).find('input[name="Segment[' + i_condition + '][select_type]"]').val() != 'user') {
                    show_filter_date = false;
                }
            });
            if (show_filter_date) {
                $('#div-filter-date').show();
            }
            else {
                $('#div-filter-date').hide();
            }
        }
        throwConditions();
    </script>
@endsection

