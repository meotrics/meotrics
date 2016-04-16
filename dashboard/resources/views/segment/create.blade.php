
@extends('../layout/master')
@section('script')
<script type="text/javascript">
var conditions = [];
var type_options = [];
<?php
$type_options = [];
if($props && is_array($props)):
    foreach ($props as $prop):
        $type_options[] = (object)[
            'value' => $prop->code,
            'name' => $prop->name,
            'select_type' => 'user',
        ];
?>
    var operators = [];
    <?php
    foreach($prop->operators as $tmp_operator):
    ?>
    operators.push({
        code: '<?= $tmp_operator->code ?>',
        name: '<?= $tmp_operator->name ?>',
    });
    <?php
    endforeach;
    ?>
    var tmp_type_option = {
        value: '<?= $prop->code ? $prop->code:'' ?>',
        name: '<?= $prop->code ? $prop->name:'' ?>',
        operators: operators,
        select_type: 'user',
    };
    type_options.push(tmp_type_option)  ;  
<?php
    endforeach;
endif;
$type_options[] = (object)[
    'value' => '[disabled]',
    'name' => 'OR SELECT',
    'select_type' => '',
];
?>
type_options.push({
    value: '[disabled]',
    name: 'OR SELECT',
    select_type: '',
});
<?php
if($actions && is_array($actions)):
    foreach ($actions as $action):
        $type_options[] = (object)[
            'value' => $action->codename,
            'name' => 'Has done '.$action->name,
            'select_type' => 'behavior',
        ];
?>
    var fields = [];
    <?php
    if($action->fields):
    foreach($action->fields as $tmp_field):
    ?>
    fields.push({
        pcode: '<?= $tmp_field->pcode ?>',
        pname: "<?= $tmp_field->pname ?>",
    });
    <?php
    endforeach;
    endif;
    ?>
    var tmp_type_option = {
        value: '<?= $action->codename ? $action->codename:'' ?>',
        name: '<?= $action->name ? $action->name:'' ?>',
        fields: fields,
        select_type: 'behavior',
    };
    type_options.push(tmp_type_option)  ;  
<?php
    endforeach;
endif;
$f_behaviors = [
    (object)['code' => 'sum', 'name' =>'Sum'],
    (object)['code' => 'avg', 'name' =>'Avg'],
    (object)['code' => 'count', 'name' =>'Count']
];
?>
    var f_behavior = [
        {code: 'sum', name: 'Sum'},
        {code: 'avg', name: 'Avg'},
        {code: 'count', name: 'Count'},
    ];
    var operator_behavior = [
        {code: '>', name: '>'},
        {code: '>=', name: '>='},
        {code: '=', name: '='},
        {code: '<', name: '<'},
        {code: '<=', name: '<='},
    ];
<?php
$condtion_sub_operators = App\Enum\SegmentEnum::conditionSubOperators();
?>
</script>
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')
<div class="card row">
    <div class="header col-md-12">
        <form id='segment-form' class="form-horizontal form-segment" method="post" action="{{URL::to('segment/write')}}">
            <input type="hidden" value="<?= $segment->_id ?>" name="id">
            <div data-name="condition-group" data-i-condition-max="<?= count($conditions) ?>">
                <?php
                $i_condition = 0;
                foreach($conditions as $condition):
                ?>
                @include('segment.partials.condition_item', [
                    'condition' => $condition,
                    'type_options' => $type_options,
                    'i_condition' => $i_condition,
                ])

                <?php
                    $i_condition ++;
                endforeach;
                ?>
            </div>
            <div class="row">
                <div class="col-sm-push-2 col-sm-2">
                    <button type="submit" class="btn btn-success btn-fill ">
                        <i class="pe-7s-diskette mr5" style="font-size:19px; vertical-align: middle"></i>
                        <span class="" style="vertical-align: middle">Create</span>
                    </button>
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
    
    function typeChange(e){
        var that = $(e);
        var containter = that.parent().parent();
        var condition_item = that.closest('div[data-name="condition-item"]');
        var i_condition = condition_item.attr('data-i-condition');
        $.each(type_options, function(i,v){
            if(v.value == that.val()){
                if(v.select_type == 'user'){
                    containter.find('input[name="Segment['+i_condition+'][select_type]"]').val('user');
                    containter.find('select[name="Segment['+i_condition+'][operator]"]').html('');
                    containter.find('select[name="Segment['+i_condition+'][operator]"]').parent().removeClass('col-md-2');
                    containter.find('select[name="Segment['+i_condition+'][operator]"]').parent().addClass('col-md-4');
                    containter.find('select[name="Segment['+i_condition+'][type]"]').parent().removeClass('col-md-2');
                    containter.find('select[name="Segment['+i_condition+'][type]"]').parent().addClass('col-md-4');
                    containter.find('select[name="Segment['+i_condition+'][f]"]').parent().hide();
                    containter.find('select[name="Segment['+i_condition+'][field]"]').parent().hide();
                    if(v.operators.length){
                        containter.find('select[name="Segment['+i_condition+'][operator]"]').html('');
                        $.each(v.operators, function(oi, ov){
                            containter.find('select[name="Segment['+i_condition+'][operator]"]').append('<option value="'+ov.code+'">'+ov.name+'</option>');
                        });
                    }
                }
                else{
                    containter.find('input[name="Segment['+i_condition+'][select_type]"]').val('behavior');
                    containter.find('select[name="Segment['+i_condition+'][operator]"]').parent().removeClass('col-md-4');
                    containter.find('select[name="Segment['+i_condition+'][operator]"]').parent().addClass('col-md-2');
                    containter.find('select[name="Segment['+i_condition+'][type]"]').parent().removeClass('col-md-4');
                    containter.find('select[name="Segment['+i_condition+'][type]"]').parent().addClass('col-md-2');
                    containter.find('select[name="Segment['+i_condition+'][f]"]').parent().show();
                    containter.find('select[name="Segment['+i_condition+'][field]"]').parent().show();
                    containter.find('select[name="Segment['+i_condition+'][operator]"]').html('');
                    
                    $.each(operator_behavior, function(obi, obv){
                        containter.find('select[name="Segment['+i_condition+'][operator]"]').append('<option value="'+obv.code+'">'+obv.name+'</option>');
                    });
                    $.each(f_behavior, function(fi, fv){
                        containter.find('select[name="Segment['+i_condition+'][f]"]').append('<option value="'+fv.code+'">'+fv.name+'</option>');
                    });
                    if(v.fields.length){
                        $('select[name="Segment['+i_condition+'][field]"]').html('');
                        $.each(v.fields, function(fieldi, fieldv){
                            $('select[name="Segment['+i_condition+'][field]"]').append('<option value="'+fieldv.pcode+'">'+fieldv.pname+'</option>');
                        });
                    }
                    var add_condition = containter.find('div[data-name="add-condition"]');
                    add_condition.show();
                }
                containter.find('input[name="Segment['+i_condition+'][value]"]').val('');
                
            }
        });
        /*
        * delete condition sub
        */
        var condition_item = that.closest('div[data-name="condition-item"]');
        condition_item.find('div[data-name="condition-sub-group"]').html('');
    }
    
    function changeField(e){
        console.log('here');
    }
    
    function addFilter(e){
        var that = $(e);
        var condition_item = that.closest('div[data-name="condition-item"]');
        var html = '<?= $html_condition_item ?>';
        condition_item.after(html);
        /*
         * fill select type, select operator
         */
        condition_item.next().find('select[name="Segment[i_condition_replace][type]"]').html('');
        $.each(type_options, function(i, v){
            var disabled = v.value == '[disabled]' ? 'disabled' : '';
            condition_item.next().find('select[name="Segment[i_condition_replace][type]"]')
                    .append('<option value="'+v.value+'" '+disabled+'>'+v.name+'</option>');
            if(i == 0){
                var vos = v.operators;
                condition_item.next().find('select[name="Segment[i_condition_replace][operator]"]').html('');
                $.each(vos, function(vos_i, vos_v){
                    condition_item.next().find('select[name="Segment[i_condition_replace][operator]"]').append('<option value="'+vos_v.code+'">'+vos_v.name+'</option>');
                });
            }
        });
        var condition_group = condition_item.closest('div[data-name="condition-group"]');
        var html = condition_item.next().html().replace(/i_condition_replace/g, condition_group.attr('data-i-condition-max'));
        condition_item.next().attr('data-i-condition', condition_group.attr('data-i-condition-max'));
        condition_group.attr('data-i-condition-max', parseInt(condition_group.attr('data-i-condition-max')) + 1);
        condition_item.next().html(html);
        checkDisableDelete();
    };
    
    function deleteFilter(e){
        var that = $(e).parent().parent();
        that.remove();
        checkDisableDelete();
    };
    
    function checkDisableDelete(){
        var count = $('.add').length;
        if(count == 1){
            $('.delete').hide();
        }
        else{
            $('.delete').show();
        }
    }
    checkDisableDelete();
    
    function addCondition(e){
        var that = $(e);
        var condition_item = that.closest('div[data-name="condition-item"]');
        var i_condition = condition_item.attr('data-i-condition');
        var selected_type = that.closest('div[data-name="condition-item"]').find('select[name="Segment['+i_condition+'][type]"]').val();
        var html = '<?= $html_sub_condition ?>';
        var condition_sub_group = that.closest('div[data-name="condition-item"]').find('div[data-name="condition-sub-group"]');
        
        var condition_sub_item = that.closest('div[data-name="condition-sub-item"]');
        if(condition_sub_item.length){
            condition_sub_item.after(html);
            /* 
            * fill files for item
            */
            $.each(type_options, function(i,v){
                if(v.value == selected_type){
                    if(v.fields.length){
                        $.each(v.fields, function(vf_i, vf_v){
                            condition_sub_item.next().find("select[name*='cs_field']").append('<option value="'+vf_v.pcode+'">'+vf_v.pname+'</option>');
                        });
                    }
                }
            });
            var html = condition_sub_item.next().html().replace(/i_condition_sub_replace/g, condition_sub_group.attr('data-i-condition-sub-max'));
            var html = html.replace(/i_condition_replace/g, condition_item.attr('data-i-condition'));
            condition_sub_group.attr('data-i-condition-sub-max', parseInt(condition_sub_group.attr('data-i-condition-sub-max'))+1);
            condition_sub_item.next().html(html);
        }
        else{
            condition_item.find('div[data-name="condition-sub-group"]').append(html);
            /* 
            * fill files for item
            */
            $.each(type_options, function(i,v){
                if(v.value == selected_type){
                    if(v.fields.length){
                        $.each(v.fields, function(vf_i, vf_v){
                            condition_item.find('div[data-name="condition-sub-group"]')
                                    .find('div[data-name="condition-sub-item"]')
                                    .last()
                                    .find('select[name*="cs_field"]')
                                    .append('<option value="'+vf_v.pcode+'">'+vf_v.pname+'</option>');
                        });
                    }
                }
            });
            var html = condition_item.find('div[data-name="condition-sub-item"]').last().html().replace(/i_condition_sub_replace/g, condition_item
                .find('div[data-name="condition-sub-item"]').length-1);
            var html = html.replace(/i_condition_replace/g, condition_item.attr('data-i-condition'));
            condition_item.find('div[data-name="condition-sub-item"]').last().html(html);
        }
        return false;
    }
    
    function deleteCondition(e){
        var that = $(e);
        var condition_sub_item = that.closest('div[data-name="condition-sub-item"]');
        if(condition_sub_item.length){
            condition_sub_item.remove();
        }
    }
</script>
@endsection

