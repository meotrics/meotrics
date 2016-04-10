<?php
use App\Enum\TrendEnum;
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
				    @if($trends)
                                        @foreach($trends as $trend)
                                        <option value="{{$trend->_id}}">{{$trend->name ? $trend->name : TrendEnum::EMPTY_NAME}}</option>
                                        @endforeach
                                    @endif
				</select>
				&nbsp; or &nbsp;<a href="{{ URL::to('trend/create') }}">+ CREATE NEW TREND</a>
			</form>
		</div>


                <div class="content col-md-12" id="outputs_table">
                    @include('trend.partials.outputs', [
                        'outputs' => $outputs,
                    ])
		</div>
	</div>


@endsection

@section('additional')
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
    $('select').select2();
    
    $('#trend').on('change', function(){
        var that = $(this);
        $.ajax({
        type: 'GET',
        dataType: 'JSON',
        url: '{{ URL::to('trend/htmloutputs') }}',
        data: {
            '_id' : that.val(),
        },
        success: function(data){
            if(data.success && data.html_outputs){
                $('#outputs_table').html(data.html_outputs);
            }
        },
    });
    return false;
    });
</script>    
@endsection