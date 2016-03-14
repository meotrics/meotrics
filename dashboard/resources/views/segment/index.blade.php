@extends('../layout/master', ['sidebarselect' => 'segment'])

@section('script')
	<script src="{{asset('js/cs.segmentop.js')}}"></script>
	<script src="{{asset('js/cs.segment.query.js')}}"></script>
	<script>
		var sq;
		var actions ={!!   $actions !!} ;
		var props ={!! $props !!};

		var data = [];

		for (var i in actions) {
			var action = actions[i];
			var fis = [];
			for (var f in action.fields)
				fis.push({name: action.fields[f].pname, code: action.fields[f].pcode});

			data.push({type: 'action', id: action._id, name: action.name, fields: fis});
		}

		for (var i in props) {
			var prop = props[i];
			data.push({type: 'prop', name: prop.name, dpname: prop.dpname});
		}

		$(function () {

			sq = new SegmentQuery();

			sq.produce(function ($query) {
				$('.id_query').append($query);

			}, data);

		});


	</script>
@endsection



@section('content')
	<div class="card row">
		<div class="col-md-12">

			<div class="header row">

				<div class="col-sm-12 id_query">

				</div>

			</div>
			<div class="content row">
				<div class="col-md-12">
					<h5 class="mr5 mb">FILTER BY </h5>
					<select class="fieldselect form-control mr5" style="width:150px; display: inline-block">
						<option value="pid">Product ID</option>
						<option value="cid">Category ID</option>
					</select>
					<select class="fieldselect form-control mr5" style="width:150px; display: inline-block">
						<option value="pid">Product ID</option>
						<option value="cid">Category ID</option>
					</select>
					<a class="btn btn-success btn-fill mr5"><i class="fa fa-bolt"></i> Excute</a>
					<a class="btn "><i class="fa fa-save"></i></a>
				</div>
			</div>

			<script></script>
@endsection

