
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
]
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
</script>
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')
<div class="card row">
    <div class="header col-md-12">
        <form class="form-horizontal form-segment" method="post" action="{{URL::to('trend/write')}}">
            <?php
            foreach($conditions as $condition):
            ?>
            <div class="form-group col-md-12">
                <!--<label class="col-md-2" style="margin-top: 10px">List top</label>-->
                <div class="col-md-{{$condition->select_type == 'user' ? 4 : 2 }}">
                    <select class="form-control" id="" name="Segment[type][]" onchange="typeChange(this)">
                        <?php
                        foreach ($type_options as $type_option):
                        ?>
                        <option value="{{$type_option->value}}" 
                            <?= $type_option->value == $condition->type ? 'selected=""' : '' ?> 
                            <?= $type_option->value == '[disabled]' ? 'disabled' : ''?> 
                            data-select-type="<?= $type_option->select_type ?>"    >
                            {{$type_option->name}}
                        </option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-2" <?= $condition->select_type == 'user' ? 'style="display: none"' : '' ?>>
                    <select class="form-control" id="" name="Segment[f][]" value="{{$condition->f}}">
                        <?php
                        foreach($f_behaviors as $f_behavior):
                        ?>
                        <option value="{{$f_behavior->code}}">{{$f_behavior->name}}</option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-2" <?= $condition->select_type == 'user' ? 'style="display: none"' : '' ?>>
                    <select class="form-control" id="" name="Segment[field][]" value="{{$condition->field}}">
                        <?php
                        foreach ($condition->fields as $c_field):
                        ?>
                        <option value="{{$c_field->pcode}}">{{$c_field->pname}}</option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-{{$condition->select_type == 'user' ? 4 : 2 }}">
                    <select class="form-control" id="" name="Segment[operator][]">
                        <?php
                        if(property_exists($condition, 'operators')){
                            $operators = $condition->operators;
                        }
                        foreach ($operators as $operator):
                        ?>
                        <option value="{{$operator->code}}" <?= $operator->code == $condition->operator ? 'selected=""' : '' ?>>
                            {{$operator->name}}
                        </option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control " name="Segment[value][]" required="" value="{{$condition->value}}"/>
                </div>
                <div class="col-md-1 col-add-filter add">
                    <a class="" href="javascript:void(0);" onclick="addFilter(this)">+ Add</a>
                </div>
                <div class="col-md-1 col-add-filter delete">
                    <a class="" href="javascript:void(0);" onclick="deleteFilter(this)">- Del</a>
                </div>
            </div>
            <?php
            endforeach;
            ?>
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
    
    // change type
    $('select[name="Segment[type][]"]').on('change', function(){
        var that = $(this);
        
        
        
//        $.ajax({
//            type: 'GET',
//            dataType: 'JSON',
//            url: '{{ URL::to('segment/htmlcondition') }}',
//            data: {
//                'type': that.val(),
//                'select_type': that.find(':selected').attr('data-select-type'),
//            },
//            success: function(data){
//                if(data.success && data.html_condition_item){
//                    that.parent().parent().html(data.html_condition_item);
//                    //$('#outputs_table').html(data.html_outputs);
//                }
//            },
//        });
//        return false;
    });
    
    function typeChange(e){
        var that = $(e);
        var containter = that.parent().parent();
        $.each(type_options, function(i,v){
            if(v.value == that.val()){
                if(v.select_type == 'user'){
                    containter.find('select[name="Segment[operator][]"]').html('');
                    containter.find('select[name="Segment[operator][]"]').parent().removeClass('col-md-2');
                    containter.find('select[name="Segment[operator][]"]').parent().addClass('col-md-4');
                    containter.find('select[name="Segment[type][]"]').parent().removeClass('col-md-2');
                    containter.find('select[name="Segment[type][]"]').parent().addClass('col-md-4');
                    containter.find('select[name="Segment[f][]"]').parent().hide();
                    containter.find('select[name="Segment[field][]"]').parent().hide();
                    if(v.operators.length){
                        $.each(v.operators, function(oi, ov){
                            containter.find('select[name="Segment[operator][]"]').append('<option value="'+ov.code+'">'+ov.name+'</option>');
                        });
                    }
                }
                else{
                    containter.find('select[name="Segment[operator][]"]').parent().removeClass('col-md-4');
                    containter.find('select[name="Segment[operator][]"]').parent().addClass('col-md-2');
                    containter.find('select[name="Segment[type][]"]').parent().removeClass('col-md-4');
                    containter.find('select[name="Segment[type][]"]').parent().addClass('col-md-2');
                    containter.find('select[name="Segment[f][]"]').parent().show();
                    containter.find('select[name="Segment[field][]"]').parent().show();
                    containter.find('select[name="Segment[operator][]"]').html('');
                    
                    $.each(operator_behavior, function(obi, obv){
                        containter.find('select[name="Segment[operator][]"]').append('<option value="'+obv.code+'">'+obv.name+'</option>');
                    });
                    $.each(f_behavior, function(fi, fv){
                        containter.find('select[name="Segment[f][]"]').append('<option value="'+fv.code+'">'+fv.name+'</option>');
                    });
                    if(v.fields.length){
                        $.each(v.fields, function(fieldi, fieldv){
                            $('select[name="Segment[field][]"]').append('<option value="'+fieldv.pcode+'">'+fieldv.pname+'</option>');
                        });
                    }
                }
                containter.find('input[name="Segment[value][]"]').val('');
            }
        });
    }
    
    function addFilter(e){
        var that = $(e).parent().parent();
        var clone = that.clone();
        clone.find('select[name="Segment[type][]"]').val(that.find('select[name="Segment[type][]"]').val());
        clone.find('input[name="Segment[value][]"]').val('');
        that.after(clone);
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
</script>
@endsection

