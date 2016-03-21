@extends('../layout/master', ['sidebarselect' => 'trend'])
@section('title', 'Trend')
{{--
$types: list of action type in the system
each $types have fields in it

--}}
@section('script')
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script>

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

		//$('#actionselect').select2();
		function updateQuery() {
			var objcode = $('#fieldselect').val();
			var typeid = $('#actionselect').val();
			var op = $('#opertorselect').val();
			var param = $('#paramselect').val();


			$.get('queryTrend', {
				typeid: typeid,
				operation: op,
				object: objcode,
				param: param
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

					head += '<th> ' + getLabel(op, objcode, typeid) + ' </th>';
					rowstr += '<td>' + he.encode(row.result) + '</td>';

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
		$('#fieldselect, #opertorselect, #paramselect').change(function () {
			updateQuery();
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
			updateQuery();
		}

		var types ={!!   $types !!} ;
		onPageLoad(function () {
			$('#actionselect').empty();
			for (var i in types) {
				var $option = $('<option>');
				$option.html(types[i].name);
				$option.val(types[i]._id);
				$('#actionselect').append($option);
			}

			$('#actionselect').change(function () {
				change_type($(this).val());
			});


		})
	</script>
@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('content')

	<div class="card row">
		<div class="header col-md-12">
			<form class="">
				<label class="mr5">TREND SELECT</label>
				<select id="trendselect" class="form-control mr" style="width: 250px; display:inline-block">

					<option value="56dab10c44aee0d1bd499a29">Purchase</option>
					<option value="56dab10544aee0d1bd499a27">Pageview</option>
				</select>
				<!-- <a class="id_querytrend btn btn-fill btn-success mr "><i class="fa fa-play" style="font-size: 15px; vertical-align: middle"></i> <span style="vertical-align: middle">Query</span></a> -->
				<a class="btn"> <i class="fa fa-floppy-o"
				                   style="font-size: 18px;padding-top: 4px;vertical-align: middle"></i></a>
				<a class="id_querytrend btn "><i class="fa fa-trash-o" style="font-size: 19px;vertical-align: middle"></i></a>
			</form>
		</div>


		<div class="content col-md-12">
			<form class="">
				<label>IN ACTION</label>
				<select id="actionselect" class="form-control" style="width: 150px; display:inline-block">
				</select>


				<label>LIST TOP</label>
				<select id="fieldselect" class="form-control" style="width: 150px; display:inline-block">
				</select>

				<label>ORDER BY </label>
				<select id="opertorselect" class="form-control" style="width: 150px; display:inline-block">
					<option value="sum">Sum</option>
					<option value="count">Number of occurs</option>

					<option value="avg">Avg</option>
				</select>
				<select id="paramselect" class="form-control" style="width: 150px; display:inline-block">
				</select>
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
