<table class="table table-hover table-striped">
	<thead class="tbhead">
	</thead>
	<tbody class="tbbody">
	</tbody>
</table>
<script>
	var objcode = "{{$object}}";
	var types ={!!   $actiontypes !!};
	var op = "{{$op}}";
	var param = "{{$param}}";

	var typeid = "{{$typeid}}";
	var data = JSON.parse('{!! json_encode($outputs) !!}');
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

	function matchFieldName(code, actionid) {
		for (var i in types) {
			if (types[i].codename == actionid) {
				for (var j in types[i].fields) {
					var f = types[i].fields[j];
					if (f.pcode == code) return f.pname;
				}
			}
		}
		return null;
	}
</script>

