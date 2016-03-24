@extends('../layout/master', ['sidebarselect' => 'segment'])

@section('script')
	<script src="{{asset('js/cs.segmentop.js')}}"></script>
	<script src="{{asset('js/cs.segment.query.js')}}"></script>
	<script>
		var sq;
		var actions ={!!   $actions !!} ;
		var props ={!! $props !!};

		var segments = {!! $segments !!};
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

		onPageLoad(function () {
			sq = new SegmentQuery();

			sq.produce(function ($query) {
				$('.id_query').append($query);

			}, data);
		});


		$('.id_exebtn').click(function () {
			$(this).attr('disabled', true);


			var $field1 = $('.id_field1-43');
			var $field2 = $('.id_field1-54');

			$.get('/segment/execute', {
				id: -1,
				name: 'Draf',
				query: sq.query(),
				f1: $field1.val(),
				f2: $field2.val()
			}, function (data) {
				console.log(data);
			});

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
					<select class="fieldselect form-control id_field1-43 mr5" style="width:150px; display: inline-block">
						<option value="pid">Product ID</option>
						<option value="cid">Category ID</option>
					</select>
					<select class="fieldselect form-control id_field2-54 mr5" style="width:150px; display: inline-block">
						<option value="pid">Product ID</option>
						<option value="cid">Category ID</option>
					</select>
					<a class="btn btn-success btn-fill mr5 id_exebtn"><i class="fa fa-bolt"></i> Excute</a>
					<a class="btn "><i class="fa fa-save"></i></a>
				</div>
			</div>

			<script></script>
		</div>
	</div>
@endsection


@section('footer')
	<div>
		<div id="saveSegmentModal" class="modal fade " role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Create New Segment</h4>
					</div>
					<div class="modal-body ">
						<form class=" row">
							<div class="col-sm-12">
								<p>Enter a name for your segment. This name will be seen by others if you share this query</p>
							</div>
							<div class="col-sm-12">
								<input type="text" id="trendnametb" class="form-control" style="width:100%" placeholder="Active users"/>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-success btn-fill id_savesegmentasbt" data-dismiss="modal">Save</button>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection
