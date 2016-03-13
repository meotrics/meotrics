function SegmentQuery() {

	this.produce = function (callback, data) {
		var $container = $('<div class="id_textbf mb mr5"><a href="#" class="dim lefticon"> <i class="fa fa-trash-o hidden id_rmbtn" ></i></a><a class="id_largequery btn btn-fill mr5 querybtn form-control"> Add Filter ...</a> </div>');
		var $data = $('<ul>');
		var $action2level = $('<ul><li data-value="count"  ><a  href="#">Total of occours</a></li> <li data-value="sum"><a  href="#">Sum</a></li> <li data-value="avg"><a  href="#">Average</a></li></ul>');
		var $prop2level = $('<ul> \
      <li data-value="less"><a href="#">less than</a></li> \
      <li data-value="greater"><a href="#">greater than</a></li> \
      <li data-value="equal"><a href="#">equal</a></li> \
      <li data-value="contain"><a href="#">contains</a></li> \
      <li data-value="startswith"><a href="#">starts with</a></li> \
      <li data-value="endswith"><a href="#">ends with</a></li> \
      <li data-value ="not"><a href="#">not equal</a></li> \
      <li data-value="from"><a href="#">from</a></li> \
      <li data-value ="fromto"><a href="#">from ... to ...</a></li> \
    </ul>');

		//find item by name
		function find(name) {
			for (var i in data) {
				if (data[i].type == 'action' && data[i].name == name) {
					return data[i];
				}
			}
			return null;
		}

		//convert data to better form
		for (var i  in data) {
			var item = data[i];
			var $litem = $('<li>');

			if (item.type == 'action') {

				$litem.attr('data-type', 'action');
				$litem.attr('data-value', item.name);
				$litem.attr('data-id', item.id);
				var $a = $('<a href="#">' + he.encode(item.name) + '</a>');
				$litem.append($a);
				$litem.append($action2level.clone());
			}
			else {
				$litem.attr('data-type', 'prop');

				$litem.attr('data-value', he.encode(item.name));
				var $a = $('<a href="#">' + he.encode(item.dpname) + '</a>');
				$litem.append($a);
				$litem.append($prop2level.clone());
			}
			$data.append($litem)
		}

		var segmentop = new SegmentOp();

		$container.find('.id_largequery').menu({
			content: $data[0].outerHTML,
			flyOut: true,
			showSpeed: 0,
			selback: largequeryselback
		});

		function remove() {
			$container.addClass('hidden');
		}

		function largequeryselback(values) {

			//make sure 2nd level is selected
			if (values.length == 1) {
				return;
			}
			$container.find('.id_rmbtn').unbind('click', remove).bind('click', remove);
			//check if user change select or create a new one
			if ($container.data('edited') !== 'true') //change
			{
				//append another query
				var seg = new SegmentQuery();
				seg.produce(function (html) {
					$container.after(html);
				}, data);
			}

			//mark that list has been edited
			$container.data('edited', 'true');
			if (values[0].type === 'action') {
				if (values[1].value == 'count') {
					$container.find('.id_largequery').html('<b>Has done</b> ' + values[0].value);
				}
				else if (values[1].value == 'sum') {
					$container.find('.id_largequery').html('<b>Has done </b>' + "Sum of " + values[0].value);

				}
				else if (values[1].value == 'avg') {
					$container.find('.id_largequery').html('<b>Has done</b>' + "Average of " + values[0].value);
				}
				else
					throw "wrong data";
				$container.find('.id_rmbtn').removeClass('hidden');
				segmentop.destroy();
				$container.append(segmentop.produce({
					type: 'number',
					data: find(values[0].value).fields,


				}));
			}
			else if (values[0].type === 'prop') {
				$container.find('.id_largequery').html('<b>Has </b>' + values[0].value);
				$container.find('.id_rmbtn').removeClass('hidden');
				segmentop.destroy();
				$container.append(segmentop.produce({defop: values[1].value}));
			}

		}


		callback($container);
	}


}

function FieldOp() {
	var $container = $('<span>');

	this.destroy = function () {
		$container.empty();
	};


	this.produce = function (options) {


		var field = "";
		if (options.data) {
			field = '<select class="form-control">';
			for (var i in options.data) {
				var f = options.data[i];
				field += '<option value="' + he.encode(f.code) + '">' + he.encode(f.name) + '</option>';
			}

			field += '</select> \
		<select class="form-control"> \
			<option value="less">less than</option> \
			<option value="greater">greater than</option> \
			<option value="equal">equal</option> \
			<option value="contain">contains</option> \
			<option value="startswith">starts with</option> \
			<option value="endswith">ends with</option> \
			<option value ="not">not equal</option> \
			<option value="from">from</option> \
			<option value ="fromto">from ... to ...</option> \
		</select> <input type="text" class="id_val form-control" />';
		}


		if (field !== '' && options.defop !== undefined) {
			field = $(field).val(options.defop)[0].outerHTML;
		}


		var $fiselect = $('<div class="mt5"><a href="#" class="dim lefticon id_rmbt"><i class="fa fa-trash"></i></a>' + field + '</div>');

		$fiselect.find('.id_rmbt').click(function () {
			$fiselect.addClass('hidden');
		});

		$fiselect.change(function () {
			var val = $(this).val();

			if (val == 'from') {

			}
			else if (val == 'fromto') {

			}
			else if (val == 'isset' || val == 'isnotset') {

			}
			else {
				//cac phep toan thong thuong on string, number
			}


		});


		return $fiselect;
	};

	this.val = function () {
		if (val == 'from') {

		}
		else if (val == 'fromto') {

		}
		else if (val == 'isset' || val == 'isnotset') {

		}
		else {
			//cac phep toan thong thuong on string, number
		}
	};
}

function SegmentOp() {
	var $container = $('<span style="display: inline-block">');
	var data;

	//clear
	this.destroy = function () {
		$addBtn.detach();
		$container.empty();
		//$container = $('<span style="display: inline-block">');
	};

	var $addBtn = $('<a href="#" class=" dim "><i class="fa fa-plus" style="margin-top: 11px;"></i></a>');

	$addBtn.click(function () {
		var field = new FieldOp();
		var $field = field.produce({data: data});

		$container.append($field);
		$container.append($addBtn);
	});

	this.produce = function (options) {
		options = options || {};
		defop = options.defop;
		data = options.data;
		var $opnum = $('<select class="form-control mr5"> \
		<option value="greater">greater than</option> \
		<option value="less">less than</option> \
		<option value="greater">greater than</option> \
		<option value="equal">equal</option> \
		</select> <input type="number" class="id_val form-control mr5" value="1" />');

		var $opselect = $('<select class="form-control mr5"> \
		<option value="less">less than</option> \
		<option value="greater">greater than</option> \
		<option value="equal">equal</option> \
		<option value="contain">contains</option> \
		<option value="startswith">starts with</option> \
		<option value="endswith">ends with</option> \
		<option value ="not">not equal</option> \
		<option value="from">from</option> \
		<option value ="fromto">from ... to ...</option> \
		</select> <input type="text" class="id_val form-control mr5" />');

		$opselect.change(function () {
			var val = $(this).val();

			if (val == 'from') {

			}
			else if (val == 'fromto') {

			}
			else if (val == 'isset' || val == 'isnotset') {

			}
			else {
				//cac phep toan thong thuong on string, number
			}


		});

		if (options.type == 'number') {
			if (options.defop !== undefined)
				$opnum.find('select').val(options.defop);
			$container.append($opnum);
			if (data !== undefined)
				$container.append($addBtn);
			return $container;
		}
		$container.append($opselect);
		if (options.defop !== undefined)
			$opselect.find('select').val(options.defop);
		if (data !== undefined)
			$container.append($addBtn);
		return $container;
	};

	this.val = function () {
		if (val == 'from') {

		}
		else if (val == 'fromto') {

		}
		else if (val == 'isset' || val == 'isnotset') {

		}
		else {
			//cac phep toan thong thuong on string, number
		}
	};

}