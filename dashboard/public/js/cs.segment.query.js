function SegmentQuery() {

	this.produce = function (callback) {
		var $container = $('<div class="id_textbf"> sd sdf <a class="id_largequery btn "> More </a></div>');

		var options = '<ul> \
	<li data-value="Purchase" data-type="action"> \
  <a href="#"  >Purchase</a> \
		<ul> \
			<li data-value="count"  ><a  href="#">Number of occours</a></li> \
			<li data-value="sum"><a  href="#">Total</a></li> \
			<li data-value="avg"><a  href="#">Average</a></li> \
		</ul> \
  </li> \
  <li> \
    <a href="#">Pageview</a> \
    <ul > \
			<li><a href="#">Number of occours</a></li> \
			<li><a href="#">Total</a></li> \
			<li><a href="#">Average</a></li> \
     </ul> \
  </li> \
  <li> \
    <a href="#">Gender</a> \
    <ul> \
      <li data-value="less"><a href="#">less than</a></li> \
      <li data-value="greater"><a href="#">greater than</a></li> \
      <li data-value="equal"><a href="#">equal</a></li> \
      <li data-value="contain"><a href="#">contains</a></li> \
      <li data-value="startswith"><a href="#">starts with</a></li> \
      <li data-value="endswith"><a href="#">ends with</a></li> \
      <li data-value ="not"><a href="#">not equal</a></li> \
      <li data-value="from"><a href="#">from</a></li> \
      <li data-value ="fromto"><a href="#">from ... to ...</a></li> \
      <li data-value="isset"><a href="#">is set</a></li> \
      <li data-value = "isnotset"><a href="#">is not set</a></li> \
    </ul> \
  </li> \
  <li> \
    <a href="#">Age</a> \
    <ul> \
      <li data-value="less"><a href="#">less than</a></li> \
      <li data-value="greater"><a href="#">greater than</a></li> \
      <li data-value="equal"><a href="#">equal</a></li> \
      <li data-value="contain"><a href="#">contains</a></li> \
      <li data-value="startswith"><a href="#">starts with</a></li> \
      <li data-value="endswith"><a href="#">ends with</a></li> \
      <li data-value ="not"><a href="#">not equal</a></li> \
      <li data-value="from"><a href="#">from</a></li> \
      <li data-value ="fromto"><a href="#">from ... to ...</a></li> \
      <li data-value="isset"><a href="#">is set</a></li> \
      <li data-value = "isnotset"><a href="#">is not set</a></li> \
    </ul> \
  </li> \
  <li> \
    <a href="#">User type</a> \
    <ul> \
      <li data-value="less"><a href="#">less than</a></li> \
      <li data-value="greater"><a href="#">greater than</a></li> \
      <li data-value="equal"><a href="#">equal</a></li> \
      <li data-value="contain"><a href="#">contains</a></li> \
      <li data-value="startswith"><a href="#">starts with</a></li> \
      <li data-value="endswith"><a href="#">ends with</a></li> \
      <li data-value ="not"><a href="#">not equal</a></li> \
      <li data-value="from"><a href="#">from</a></li> \
      <li data-value ="fromto"><a href="#">from ... to ...</a></li> \
      <li data-value="isset"><a href="#">is set</a></li> \
      <li data-value = "isnotset"><a href="#">is not set</a></li> \
    </ul> \
  </li> \
</ul>';
		console.log($container.find('.id_largequery'));
		var segmentop = new SegmentOp();
		//generate menu
		$container.find('.id_largequery').menu({
			content: options,
			flyOut: true,
			showSpeed: 0,
			selback: function (values) {

				if (values[0].type === 'action') {
					if (values[1].value == 'count') {
						$container.find('.id_largequery').html("Number occours of " + values[0].value + " is ");
					}
					else if (values[1].value == 'sum') {
						$container.find('.id_largequery').html("Total of " + values[0].value);
					}
					else if (values[1].value == 'avg') {
						$container.find('.id_largequery').html("Average of " + values[0].value);
					}
					else
						throw "wrong data";

					segmentop.destroy();
					$container.append(segmentop.produce({type: 'number'}));
				}
				else if (values[0].type === 'prop') {

				}
			}
		});

		callback($container);
	}


}


function SegmentOp() {
	var $container = $('<span>');
	this.destroy = function () {
		$container.empty();
	};

	this.produce = function (options) {


		var $opnum = $('<select class="form-control"> \
		<option value="greater">greater than</option> \
		<option value="less">less than</option> \
		<option value="greater">greater than</option> \
		<option value="equal">equal</option> \
		</select> <input type="number" class="id_val form-control" value="1" />');

		var $opselect = $('<select class="form-control"> \
		<option value="less">less than</option> \
		<option value="greater">greater than</option> \
		<option value="equal">equal</option> \
		<option value="contain">contains</option> \
		<option value="startswith">starts with</option> \
		<option value="endswith">ends with</option> \
		<option value ="not">not equal</option> \
		<option value="from">from</option> \
		<option value ="fromto">from ... to ...</option> \
		<option value="isset">is set</option> \
		<option value = "isnotset">is not set</option> \
		</select> <input type="text" class="id_val form-control" />');

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
			$container.append($opnum);
			return $container;
		}

		return $opselect;
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