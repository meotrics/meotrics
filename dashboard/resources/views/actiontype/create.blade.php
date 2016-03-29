@extends('../layout/master')
@section('script')
	<script>

		function addProp(prop) {
			var $newprop = $('.id_proptem').children().clone();
			$newprop.find('.id_remprop').click(function () {
				$newprop.addClass('hidden');

				$newprop.find('input').prop('disabled', true);
				var $children = $list.children();
				console.log($children);
				var c = 0;
				for (var i = 0; i < $children.length; i++) {
					if (!$($children[i]).hasClass('hidden'))
						c++;
				}

				//if there is only one element left. Hidden whole div
				if (c < 2) $list.addClass('hidden');
			});
			$newprop.find('.id_name').val(prop.name);
			$newprop.find('.id_remprop');

			var $list = $(".id_proplist");
			if ($list.hasClass('hidden'))
				$list.removeClass('hidden');
			$list.append($newprop);
		}
		$('.id_addprop').click(function () {
			addProp({code: "", name: ""})
		})

	</script>
@endsection

@section('content')

	<div class="card row">
		<div class="content col-sm-12">
			<form action="/actiontype" method="POST">
				<div class="row">
					<div class="form-group col-sm-2 ">
						<h6 class="pull-right " style="margin-top: 11px">Code Name</h6>
					</div>
					<div class="form-group col-sm-5">
						<input type="text" class="form-control fmonospaced" name="codename" placeholder="Name" required>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-2">
						<h6 class="pull-right" style="margin-top: 11px">Display Name</h6>
					</div>
					<div class="col-sm-5">
						<input type="text" name="name" class="form-control" id="exampleInputEmail1"
                   placeholder=" Display Name" required>
          </div>
				</div>
				<div class="row">
					<div class="form-group col-sm-2">
						<h6 class="pull-right" style="margin-top: 11px">Description</h6>
					</div>
					<div class="col-sm-5">
						<input type="text" name="desc" class="form-control" id="exampleInputEmail1"
                   placeholder="Description">
          </div>
				</div>
				<div class="row">

					<label class="col-sm-12"></label>
				</div>
				<div class="id_proplist hidden">
					<div class="row">
						<div class="col-sm-2">
							<h6 class="pull-right" style="margin-top: 5px">Properties</h6>
						</div>
						<div class="col-sm-3">
							<span class="fmonospaced">Properties code</span>
						</div>
						<div class="col-sm-3">
							<span>Properties name</span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-push-2 col-sm-4">
						<a class="id_addprop" href="#">+ Add Properties</a>
					</div>

				</div>
				<div class="row">
					<div class="col-sm-push-2 col-sm-2">
						<button class="btn btn-success btn-fill ">
							<i class="pe-7s-diskette mr5" style="font-size:19px; vertical-align: middle"></i>
							<span class="" style="vertical-align: middle">Create</span>
						</button>
					</div>

				</div>
			</form>

			<div class="hidden id_proptem">
				<div class="row">
					<div class="form-group col-sm-2">

					</div>
					<div class="col-sm-3">
						<input type="text" name="pcodes[]" class="form-control fmonospaced" placeholder="Properties Code" required>
					</div>
					<div class="col-sm-3">
						<input type="text" name="pnames[]" class="form-control" placeholder="Properties Display Name" required>
					</div>
					<div class="col-sm-3">
						<a class="id_remprop btn  "><i class="fa fa-trash"></i></a>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection