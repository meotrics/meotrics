<table class="table table-hover table-striped table-trend">
	<thead class="tbhead">
	</thead>
	<tbody class="tbbody">
	</tbody>
</table>
<script>
	function countProp(obj)
{
	var c = 0;
	for(var i in obj) if(obj.hasOwnProperty(i)){
c++;

}
return c;
}
	onPageLoad(function () {
		var objcode = "{{$object}}";
		var types ={!!   $actiontypes !!};
		var op = "{{$op}}";
		var param = "{{$param}}";

		var typeid = "{{$typeid}}";
		var data = {!! json_encode($outputs) !!};
		$body = $('.tbbody');
		$body.css('text-align', 'center');
		$head = $('.tbhead');
		$body.empty();
		var head;
		//data = JSON.parse(data);
		var stt = 0;
		var maxn = 0;
		for (var i in data) {
			stt++;
			var row = data[i];
			var rowstr = "<td class='text-muted'>" + stt + "</td>";
		
			rowstr += '<td style="text-align: left">' + he.encode(row.temp[objcode]) + '</td>';
		
			if(countProp(row.temp) >= maxn){
					   maxn = countProp(row.temp);
			head = "<th class='text-muted' style='text-align: center;'>#</th>";
			head += '<th style="text-align: center;">' + he.encode(matchFieldName(objcode, typeid)) + ' </th>';	
				head += '<th style="text-align: center;"> ' + getLabel(op, param, typeid) + ' </th>';
			}
			rowstr += '<td>' + he.encode(row.result) + '</td>';
			for (var j in row.temp) if (row.temp.hasOwnProperty(j) && j.toString().startsWith('_') == false && j.toString() !== objcode && 'null'!== he.encode(matchFieldName(j, typeid))){
			    if(countProp(row.temp) >= maxn){
				head += '<th style="text-align: center;"> ' + he.encode(matchFieldName(j, typeid)) + ' </th>';
				
				
}
				rowstr += '<td>' + he.encode(row.temp[j]) + '</td>';
			}
console.log(he.encode(matchFieldName(j, typeid)));
			rowstr = '<tr>' + rowstr + '</tr>';
			head = '<tr>' + head + '</tr>';
			$body.prepend(rowstr);
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
	});
</script>

