@extends('../layout/master', ['sidebarselect' => 'segment'])

@section('script')
	<script src="{{asset('js/cs.segmentop.js')}}"></script>
	<script src="{{asset('js/cs.segment.query.js')}}"></script>
	<script>

		$(function () {


			var sq = new SegmentQuery();

			sq.produce(function ($query) {
				$('#flyout').append($query);

			});

		});
		function loadSegment(segment) {

		}


		$('.addactionlink').click(function () {
			var $segment = $('.id_segmenttem').children().clone();
			var $list = $('.id_segmentlist');
			$list.append($segment);
		});

		function newSegment() {
			var $segment = $('.id_segmenttem').children().clone();
			var $list = $('.id_segmentlist');
			$list.empty();


			$segment.find('.removelink').click(function () {
				$segment.addClass('hidden');
			});

			var $segcondlist = $segment.find('.subconditionlist');
			$segment.find('.addcondlink').click(function () {
				var $cond = $('.id_condtemp').children().clone();

				$cond.find('.removecondlink').click(function () {
					$cond.addClass('hidden');
				});
				$segcondlist.append($cond);
			});

			$list.append($segment);
		}


		function segment_change(segment) {

		}
		;
		var $list = $('.id_segmentlist');
		$list.empty();
		newSegment();

	</script>
@endsection



@section('content')
	<div class="card row">
		<div class="header col-md-12">

			<div id="flyout">

			</div>


		</div>
		<div class="content col-sm-12">

			<div class="id_segmentlist">

			</div>


			<div class="hidden id_segmenttem">
				<div class="">

					{{--<a tabindex="0" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="flyout">--}}
					{{--<span class="ui-icon ui-icon-triangle-1-s"></span>flyout menu</a>--}}


					<div class="hidden id_condtemp">
						<div>
							<select class="joinselect form-control" style="width:80px; display: inline-block">
								<option value="and">and</option>
								<option value="or">or</option>
							</select>
							<select class="subfieldselect form-control" style="width:150px; display: inline-block"></select>
							<select class="operatorselect form-control" style="width:70px; display: inline-block">
								<option value="&gt;">&gt;</option>
								<option value="&gt;">&gt;</option>
								<option value="&lt;">&lt;</option>
								<option value="&#x2260;">&#x2260;</option>
							</select>
							<input type="text" class="value form-control" style="width:150px; display: inline-block"/>
							<a href='#' class="removecondlink"><i class="fa fa-remove"></i> </a>
						</div>
					</div>
				</div>
				<div class="col-md-12">

					<label>Filter by </label>
					<select class="fieldselect form-control " style="width:150px; display: inline-block">
						<option value="pid">Product ID</option>
						<option value="cid">Category ID</option>
					</select>
					<select class="fieldselect form-control " style="width:150px; display: inline-block">
						<option value="pid">Product ID</option>
						<option value="cid">Category ID</option>
					</select>
					<button>Excute</button>
					<button>Save</button>
				</div>

			</div>

			<div class="lists hidden">
				<ul class="">
					<li><a href="#">Breaking News</a>
						<ul>
							<li><a href="#">Entertainment</a></li>
							<li><a href="#">Politics</a></li>
							<li><a href="#">A&amp;E</a></li>
							<li><a href="#">Sports</a>
								<ul>
									<li><a href="#">Baseball</a></li>
									<li><a href="#">Basketball</a></li>
									<li><a href="#">A really long label would wrap nicely as you can see</a></li>
									<li><a href="#">Swimming</a>
										<ul>
											<li><a href="#">High School</a></li>
											<li><a href="#">College</a></li>
											<li><a href="#">Professional</a>
												<ul>
													<li><a href="#">Mens Swimming</a>
														<ul>
															<li><a href="#">News</a></li>
															<li><a href="#">Events</a></li>
															<li><a href="#">Awards</a></li>
															<li><a href="#">Schedule</a></li>
															<li><a href="#">Team Members</a></li>
															<li><a href="#">Fan Site</a></li>
														</ul>
													</li>
													<li><a href="#">Womens Swimming</a>
														<ul>
															<li><a href="#">News</a></li>
															<li><a href="#">Events</a></li>
															<li><a href="#">Awards</a></li>
															<li><a href="#">Schedule</a></li>
															<li><a href="#">Team Members</a></li>
															<li><a href="#">Fan Site</a></li>
														</ul>
													</li>
												</ul>
											</li>
											<li><a href="#">Adult Recreational</a></li>
											<li><a href="#">Youth Recreational</a></li>
											<li><a href="#">Senior Recreational</a></li>
										</ul>
									</li>
									<li><a href="#">Tennis</a></li>
									<li><a href="#">Ice Skating</a></li>
									<li><a href="#">Javascript Programming</a></li>
									<li><a href="#">Running</a></li>
									<li><a href="#">Walking</a></li>
								</ul>
							</li>
							<li><a href="#">Local</a></li>
							<li><a href="#">Health</a></li>
						</ul>
					</li>
					<li><a href="#">Entertainment</a>
						<ul>
							<li><a href="#">Celebrity news</a></li>
							<li><a href="#">Gossip</a></li>
							<li><a href="#">Movies</a></li>
							<li><a href="#">Music</a>
								<ul>
									<li><a href="#">Alternative</a></li>
									<li><a href="#">Country</a></li>
									<li><a href="#">Dance</a></li>
									<li><a href="#">Electronica</a></li>
									<li><a href="#">Metal</a></li>
									<li><a href="#">Pop</a></li>
									<li><a href="#">Rock</a>
										<ul>
											<li><a href="#">Bands</a>
												<ul>
													<li><a href="#">Dokken</a></li>
												</ul>
											</li>
											<li><a href="#">Fan Clubs</a></li>
											<li><a href="#">Songs</a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li><a href="#">Slide shows</a></li>
							<li><a href="#">Red carpet</a></li>
						</ul>
					</li>
					<li><a href="#">Finance</a>
						<ul>
							<li><a href="#">Personal</a>
								<ul>
									<li><a href="#">Loans</a></li>
									<li><a href="#">Savings</a></li>
									<li><a href="#">Mortgage</a></li>
									<li><a href="#">Debt</a></li>
								</ul>
							</li>
							<li><a href="#">Business</a></li>
						</ul>
					</li>
					<li><a href="#">Food &#38; Cooking</a>
						<ul>
							<li><a href="#">Breakfast</a></li>
							<li><a href="#">Lunch</a></li>
							<li><a href="#">Dinner</a></li>
							<li><a href="#">Dessert</a>
								<ul>
									<li><a href="#">Dump Cake</a></li>
									<li><a href="#">Doritos</a></li>
									<li><a href="#">Both please.</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li><a href="#">Lifestyle</a></li>
					<li><a href="#">News</a></li>
					<li><a href="#">Politics</a></li>
					<li><a href="#">Sports</a>
						<ul>
							<li><a href="#">Baseball</a></li>
							<li><a href="#">Basketball</a></li>
							<li><a href="#">Swimming</a>
								<ul>
									<li><a href="#">High School</a></li>
									<li><a href="#">College</a></li>
									<li><a href="#">Professional</a>
										<ul>
											<li><a href="#">Mens Swimming</a>
												<ul>
													<li><a href="#">News</a></li>
													<li><a href="#">Events</a></li>
													<li><a href="#">Awards</a></li>
													<li><a href="#">Schedule</a></li>
													<li><a href="#">Team Members</a></li>
													<li><a href="#">Fan Site</a></li>
												</ul>
											</li>
											<li><a href="#">Womens Swimming</a>
												<ul>
													<li><a href="#">News</a></li>
													<li><a href="#">Events</a></li>
													<li><a href="#">Awards</a></li>
													<li><a href="#">Schedule</a></li>
													<li><a href="#">Team Members</a></li>
													<li><a href="#">Fan Site</a></li>
												</ul>
											</li>
										</ul>
									</li>
									<li><a href="#">Adult Recreational</a></li>
									<li><a href="#">Youth Recreational</a></li>
									<li><a href="#">Senior Recreational</a></li>
								</ul>
							</li>
							<li><a href="#">Tennis</a></li>
							<li><a href="#">Ice Skating</a></li>
							<li><a href="#">Javascript Programming</a></li>
							<li><a href="#">Running</a></li>
							<li><a href="#">Walking</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<script></script>
@endsection

