function SegmentQuery() {
	var $container = $('<div class="id_textbf mb mr5"><a href="#" class="dim lefticon"> <i class="fa fa-trash-o hidden id_rmbtn" ></i></a><a class="id_largequery mt5 btn btn-fill mr5 querybtn form-control"> Add Filter ...</a> </div>');
	var me = this;
	this.query = function () {
		var $children = $container.parent().children();
		var query = [];

		for (var i = 0; i < $children.length; i++) {

			var $child = $($children[i]);
			if ($child.hasClass('hidden')) continue;
			var ele = {};
			//type: "purchase", f: "avg", field: "amount", operator: ">", value: 5,
			//conditions: ["amount", ">", 2, "and", "price", "==", 40]
			if ($child.attr('data-type') == 'action') {
				ele.type = $child.data('id');
				ele.f = $child.data('f');
				ele.fields = [];
				var $conds = $child.find('.id_condition');
				for (var t = 0; t < $conds.length; t++) if ($conds.hasOwnProperty(t)) {
					var $cond = $($conds[t]);

					//jump over deleted condition
					if($cond.hasClass('hidden')) continue;
					
					if (ele.fields.length !== 0) ele.fields.push('and');
					ele.fields.push($cond.find('.id_fi').val());
					ele.fields.push($cond.find('.id_op').val());
					ele.fields.push($cond.find('.id_val').val());
				}
				ele.operator = $child.find('.id_fop').find('.id_op').val();
				ele.value = $child.find('.id_fop').find('.id_val').val();
				query.push(ele);
			}
			else if ($child.attr('data-type') == 'prop') {
				ele.type = undefined;
				ele.field = $child.data('code');
				ele.operator = $child.find('.id_fop').find('.id_op').val();
				ele.value = $child.find('.id_fop').find('.id_val').val();
				query.push(ele);
			}
			else {
				//do nothing
			}
		}
		return query;
	};

	this.produce = function (callback, data) {
		var $data = $('<ul>');
		var $action2level = $('<ul><li data-value="count"  ><a  href="#">Total of occours</a></li> <li data-value="sum"><a  href="#">Sum</a></li> <li data-value="avg"><a  href="#">Average</a></li></ul>');

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
				$litem.attr('data-id', he.encode(item.name));
				$litem.attr('data-value', he.encode(item.dpname));
				var $a = $('<a href="#">' + he.encode(item.dpname) + '</a>');
				$litem.append($a);
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
			if (values.length == 1 && values[0].type === 'action') return;

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
			$container.find('.id_rmbtn').removeClass('hidden');
			if (values[0].type === 'action') {
				$container.attr('data-id', values[0].id);
				$container.attr('data-type', 'action');
				if (values[1].value == 'count') {
					$container.attr('data-f', 'count');
					$container.find('.id_largequery').html('<b>Has done</b> ' + values[0].value);

					//make a operator select
					$container.append(segmentop.produce({fields: find(values[0].value).fields}));
				}
				else if (values[1].value == 'sum') {
					$container.attr('data-f', 'sum');
					$container.find('.id_largequery').html('<b>Has done </b> ' + values[0].value + " which <b>sum</b> of");

					//make a operator select
					$container.append(segmentop.produce({
						data: find(values[0].value).fields,
						fields: find(values[0].value).fields
					}));
				}
				else if (values[1].value == 'avg') {
					$container.attr('data-f', 'avg');
					$container.find('.id_largequery').html('<b>Has done</b> ' + values[0].value + ' which <b>average</b> of ');

					//make a operator select
					$container.append(segmentop.produce({
						data: find(values[0].value).fields,
						fields: find(values[0].value).fields
					}));
				}
				else
					throw "wrong data";

				//$container.find('.id_rmbtn').removeClass('hidden');
				//segmentop.destroy();

			}
			else if (values[0].type === 'prop') {
				$container.attr('data-type', 'prop');
				$container.attr('data-code', values[0].id);
				$container.find('.id_largequery').html('<b>Has </b>' + values[0].value);

				segmentop.destroy();
				$container.append(segmentop.produce({canadd: false}));
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
		options = options || {};
		var field = "";
		if (options.data) {
			field = '<select class="form-control id_fi">';
			for (var i in options.data) {
				var f = options.data[i];
				field += '<option value="' + he.encode(f.code) + '">' + he.encode(f.name) + '</option>';
			}

			field += '</select> \
			<select class="form-control id_op mr5"> \
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
		else { //for master field creation
			field += '\
			<select class="form-control id_op mr5"> \
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
		var $fiselect = $('<div class="mt5" style="display: inline-block;"><a href="#" class="dim lefticon id_rmbt"><i class="fa fa-trash"></i></a>' + field + '</div>');

		if (options.class !== undefined) {
			$fiselect.addClass(options.class);
		}

		if (options.canremove === false) {
			$fiselect.find('.id_rmbt').addClass('hidden');
		}


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
	var $container = $('<span style="display: inline-block; width:500px">');
	var data, fields;

	//clear
	this.destroy = function () {
		$addBtn.detach();
		$container.empty();
		//$container = $('<span style="display: inline-block">');
	};

	var $addBtn = $('<a href="#" class=" dim  id_addbtn"><i class="fa fa-plus" style="margin-top: 11px;"></i></a>');

	$addBtn.click(function () {
		var field = new FieldOp();
		var $field = field.produce({data: fields, class: "id_condition"});

		$container.append($field);
		$field.append($addBtn);
	});

	var field = new FieldOp();

	this.produce = function (options) {
		options = options || {};

		if (options.canadd == false) $addBtn.addClass('hidden');
		defop = options.defop;
		data = options.data;
		fields = options.fields;

		if (data !== undefined) {
			$container.append(field.produce({
				data: data,
				defop: options.defop,
				canremove: false,
				type: 'number',
				class: 'id_fop'
			}))
		}
		else {
			$container.append(field.produce({
				defop: options.defop,
				canremove: false,
				class: 'id_fop'
			}))
		}

		$container.append($addBtn);
		return $container;
	};

}