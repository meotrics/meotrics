<?php
use App\Enum\TrendEnum;

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
        if($key == 0){
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

<div class="card row">
    <div class="header col-md-12">
        <!--<form class="col-md-12">-->
            <label class="col-md-2">Select Segmentation</label>&nbsp;&nbsp;
            <div class="col-md-4">
                <select id="segment" class="form-control input-sm" style="display:inline-block">
                    @foreach($segments as $segment)
                    <option value="{{$segment->_id}}" <?= $segment->_id == $segment_first->_id?'selected=""':'' ?>>{{$segment->name ? $segment->name : TrendEnum::EMPTY_NAME}}</option>
                    @endforeach
                </select>
            </div>
            
            <a id="action_update" data-href="{{URL::to('segment/update')}}" href="{{URL::to('segment/update', [
                'id' => $segment_first ? $segment_first->_id : ''
            ])}}" class="btn btn-primary" role="button">Update</a>
            <a id="action_delete" href="javascript:void(0)" class="btn btn-danger" role="button">Delete</a>
            &nbsp; or &nbsp;<a href="{{ URL::to('segment/create') }}">+ CREATE NEW SEGMENTATION</a>
        <!--</form>-->
    </div>

    <div class="content col-md-12" data-name="name">
        <label class="col-md-2" style="margin-top: 4px">Segment name: </label>
        <p class="col-md-10"><?= property_exists($segment_first, 'name') ? $segment_first->name : ''?></p>
    </div>
    <div class="content col-md-12" data-name="description">
        <label class="col-md-2" style="margin-top: 4px">Segment description: </label>
        <p class="col-md-10"><?= property_exists($segment_first, 'description') ? $segment_first->description : ''?></p>
    </div>
</div>
<div class="card row">
    <div class="header col-md-12">
        <div class="content col-md-2">
            <label>Filter to execute:</label>
        </div>
        <div class="col-md-4">
            <select name="Prop[one]" class="form-control">
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
        <div class="col-md-4">
            <select name="Prop[one]" class="form-control">
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
            <button type="submit" class="btn btn-success btn-fill ">
                <span class="" style="vertical-align: middle">Execute chart</span>
            </button>
        </div>
    </div>
    <div class="col-md-12">
        

    </div>
</div>

@endsection

@section('additional')
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
    $('select').select2();
    
    $('#segment').on('change', function(){
        var that = $(this);
        /*
         * set name and description
         */
        var name_div = $('div[data-name="name"]').find('p');
        if(name_div.length){
            name_div.text(segments[that.val()] ? segments[that.val()]['name'] : '');
        }
        var description_div = $('div[data-name="description"]').find('p');
        if(description_div.length){
            description_div.text(segments[that.val()] ? segments[that.val()]['description'] : '');
        }
        $('#action_update').attr('href', $('#action_update').attr('data-href')+'/'+that.val());
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
    
    $('#action_delete').on('click', function(){
        var cf = confirm('This segment will be removed. Are you sure?');
        if(cf){
            $.ajax({
            type: 'DELETE',
            dataType: 'JSON',
            url: '{{ URL::to('segment/remove') }}'+'/'+$('#segment').val(),
            success: function(data){
                if(data.success){
                    location.reload();
                }
            },
        });
        }
    });
</script>    
@endsection