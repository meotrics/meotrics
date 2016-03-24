@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')
{{--
$types: list of action type in the system
each $types have fields in it

--}}
@section('script')
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script>
		function loadTrend(trend) {

			$('#actionselect').val(trend.typeid);
			change_type(trend.typeid);
			$('#fieldselect').val(trend.object);
			$('#opertorselect').val(trend.operation);
			if(trend.operation == 'count')
				$('#paramselect').addClass('hidden');
			else
				$('#paramselect').removeClass('hidden');
			$('#paramselect').val(trend.param);
			$('#sortcb').val(trend.order == 1);

			localStorage.trend = JSON.stringify(trend);
		}

		function matchFieldName(code, actionid) {
			for (var i in types) {
				if (types[i]._id == actionid) {
					for (var j in types[i].fields) {
						var f = types[i].fields[j];
						if (f.pcode == code) return f.pname;
					}
				}
			}
			return null;
		}

		function getLabel(op, objd, typeid) {
			var label = {
				count: "Total occurs",
				sum: "Total of ",
				avg: "Avg of "
			};
			if (op == 'count') {
				return label[op];
			}
			else {
				return label[op] + he.encode(matchFieldName(objd, typeid))
			}
		}

		function findTrend(trendid) {
			for (var i in trends)
				if (trends[i]._id == trendid)
					return trends[i];
			return undefined;
		}

		function getCurrentTrend() {
			var trend = JSON.parse(localStorage.trend);
			var _id, name;
			if (trend !== undefined) {
				_id = trend._id;
				name = trend.name;

			}
			var objcode = $('#fieldselect').val();
			var typeid = $('#actionselect').val();
			var op = $('#opertorselect').val();
			var param = $('#paramselect').val();
			var order = $('#sortcb').val() == true ? 1 : -1;
			return {
				typeid: typeid,
				operation: op,
				object: objcode,
				param: param,
				order: order,
				_id: _id,
				name: name
			};
		}

		var updatequerytimer = null;
		function tupdateQuery() {
			clearTimeout(updatequerytimer);
			updatequerytimer = setTimeout(function () {
				updateQuery();
			}, 1000);
		}
		//$('#actionselect').select2();
		function updateQuery() {

			var objcode = $('#fieldselect').val();
			var typeid = $('#actionselect').val();
			var op = $('#opertorselect').val();
			var param = $('#paramselect').val();
			var order = $('#sortcb').val() == true ? 1 : -1;

			if (param == null || objcode == null) return console.log('f');
			$.get('trend/query', {
				typeid: typeid,
				operation: op,
				object: objcode,
				param: param,
				order: order
			}, function (data) {
				$body = $('.tbbody');
				$head = $('.tbhead');
				$body.empty();
				var head;
				//data = JSON.parse(data);
				var stt = 0;
				for (var i in data) {
					stt++;
					var row = data[i];
					var rowstr = "<td class='text-muted'>" + stt + "</td>";
					head = "<th class='text-muted'>#</th>";

					head += '<th> ' + he.encode(matchFieldName(objcode, typeid)) + ' </th>';
					rowstr += '<td>' + he.encode(row.temp[objcode]) + '</td>';

					head += '<th> ' + getLabel(op, param, typeid) + ' </th>';
					rowstr += '<td>' + he.encode(op == 'count' ? row.count : row.result) + '</td>';

					for (var j in row.temp) if (row.temp.hasOwnProperty(j) && j.toString().startsWith('_') == false && j.toString() !== objcode) {
						head += '<th> ' + he.encode(matchFieldName(j, typeid)) + ' </th>';
						rowstr += '<td>' + he.encode(row.temp[j]) + '</td>';
					}
					rowstr = '<tr>' + rowstr + '</tr>';
					head = '<tr>' + head + '</tr>';
					$body.append(rowstr);
				}

				$head.empty();
				$head.append(head);
			});

		}

		$('#opertorselect').change(function () {
			if ($(this).val() == 'count') {
				$('#paramselect').addClass('hidden');
			}
			else {
				$('#paramselect').removeClass('hidden');
			}
			localStorage.trend = JSON.stringify(getCurrentTrend());
			tupdateQuery();
		});

		$('#fieldselect, #paramselect, #sortcb').change(function () {
			if (!(localStorage.drafttrend == "true")) {
				localStorage.trend_save = "false";
			}

			//save to localstorage
			localStorage.trend = JSON.stringify(getCurrentTrend());
			tupdateQuery();
		});


		function change_type(id) {
			//find in types
			for (var i in types) {
				if (types[i]._id == id) {
					$('#paramselect').empty();
					$('#fieldselect').empty();

					for (var j in types[i].fields) {
						var field = types[i].fields[j];
						var $option = $('<option>');
						$option.val(field.pcode);
						$option.html(field.pname);
						$('#paramselect').append($option.clone());
						$('#fieldselect').append($option.clone());
					}
				}
			}
			tupdateQuery();
		}

		function refreshTrendSelect() {
			$('#trendselect').empty();
			for (var i in trends) {
				$option = $('<option>');
				$option.html(he.encode(trends[i].name));
				$option.val(trends[i]._id);
				$('#trendselect').append($option);
			}

			$option = $('<option>');
			$option.html(he.encode('Draft'));
			$option.val('draft');
			$('#trendselect').append($option);
		}

		var types ={!!   $types !!};
		var trends ={!! $trends !!};
		onPageLoad(function () {

			//fetch data
			var $option;
			$('#actionselect').empty();
			for (var i in types) {
				$option = $('<option>');
				$option.html(he.encode(types[i].name));
				$option.val(types[i]._id);
				$('#actionselect').append($option);
			}

			$('#actionselect').change(function () {
				change_type($(this).val());
			});

			refreshTrendSelect();

			$('#trendselect').change(function () {
				var val = $(this).val();
				if (val == 'draft') {
					//do nothing
					localStorage.drafttrend = "true";
				}
				else {
					loadTrend(findTrend(val));
					localStorage.drafttrend = "false";
					localStorage.trend_save = "true";
				}
			});


			var trend = localStorage.trend == undefined ? undefined : JSON.parse(localStorage.trend);
			if (trend == undefined)// || localStorage.trend._idDraft == true || localStorage.trend_save == false)
			{
				//show tutorial here
				$('#trendselect').val('draft');
				localStorage.drafttrend = "true";
				change_type($('#actionselect').val());
			}
			else if (localStorage.drafttrend == "true") {
				loadTrend(trend);
				$('#trendselect').val('draft');
				tupdateQuery();
			}
			else {

				$('#trendselect').val(trend._id);
				loadTrend(findTrend(trend._id));
				localStorage.trend_save = "true";
				tupdateQuery();
			}


			$('.id_savelnk').click(function () {

				if (localStorage.drafttrend == 'true') {
					$('.id_saveasbt').trigger('click');
				}
				else {
					localStorage.trend_save = "true";
					localStorage.drafttrend = "false";
				}
			});

			$('.id_saveasbt').click(function () {
				var trend = JSON.parse(localStorage.trend);
				trend.name = $('#trendnametb').val();
				$('#trendnametb').val("");
				$.get('trend/save', trend, function (data) {
					trend._id = data;
					$.get('trend/list', function (data) {

						trends = JSON.parse(data);
						refreshTrendSelect();

						$('#trendselect').val(trend._id);
						localStorage.trend = JSON.stringify(trend);
						localStorage.trend_save = "true";
						localStorage.drafttrend = "false";
					});
				});
			});


		});


	</script>
@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')

	<div class="card row">
		<div class="header col-md-12">
			<form class="">
				<label class="">TREND SELECT</label>
				<select id="trendselect" class="form-control " style="width: 250px; display:inline-block"></select>
				<!-- <a class="id_querytrend btn btn-fill btn-success mr "><i class="fa fa-play" style="font-size: 15px; vertical-align: middle"></i> <span style="vertical-align: middle">Query</span></a> -->
				<a class="btn btn-fill btn-success ml" data-toggle="modal" data-target="#saveTrendModal">Save as ...</a>
				<a class="btn id_savelnk">
					<i class="fa fa-floppy-o" style="font-size: 18px;padding-top: 4px;vertical-align: middle"></i>
				</a>

				<a class="id_querytrend btn "><i class="fa fa-trash-o" style="font-size: 19px;vertical-align: middle"></i></a>
			</form>
		</div>


		<div class="content col-md-12">
			<form class="">
				<label>IN ACTION</label>
				<select id="actionselect" class="form-control" style="width: 150px; display:inline-block"></select>
				<label>LIST TOP</label>
				<select id="fieldselect" class="form-control" style="width: 150px; display:inline-block"></select>
				<label>ORDER BY </label>
				<select id="opertorselect" class="form-control " style="width: 150px; display:inline-block">
					<option value="sum">Sum</option>
					<option value="count">Number of occurs</option>
					<option value="avg">Avg</option>
				</select>
				<select id="paramselect" class="form-control mr " style="width: 150px; display:inline-block"></select>

				<label class=""><input id="sortcb" type="checkbox"/> <i class="fa fa-sort-amount-asc"></i> ascending</label>

			</form>

			<div class="row">
				<div class=" col-sm-12">
					<table class="table table-hover table-striped">
						<thead class="tbhead"></thead>
						<tbody class="tbbody">
						</tbody>
					</table>

				</div>
			</div>

		</div>
	</div>


@endsection

@section('footer')
	<div>
		<div id="saveTrendModal" class="modal fade " role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Save Trend Query</h4>
					</div>
					<div class="modal-body ">
						<form class=" row">
							<div class="col-sm-12">
								<p>Enter a name for your query. This name will be seen by others if you share this query</p>
							</div>
							<div class="col-sm-12">
								<input type="text" id="trendnametb" class="form-control" style="width:100%" placeholder="Top product"/>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-success btn-fill id_saveasbt" data-dismiss="modal">Save</button>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection