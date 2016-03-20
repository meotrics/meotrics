<?php if (isset($segments) == false) $segments = []; ?>
<li class="dropdown mr">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<i class="pe-7s-user" style="vertical-align: middle; font-size: 28px"></i>
		<span class="segment-label" style="vertical-align: middle"> All users</span>
		<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
		<li><a href="#" onclick="segmentselect('All users','0')"> All Users</a></li>
		@foreach ($segments as $seg )
			<li><a href="#" data-id="{{$seg->id}}" onclick="segmentselect('{{$seg->name}}','{{$seg->id}}')">{{$seg->name}}</a>
			</li>
		@endforeach
	</ul>
</li>

<script>
	$(function () {
		function segmentselect(name, id) {
			$('.segment-label').html(name);
			setsegment(name, id)
		}

		function initsegmentselect() {
			if (localStorage.segmentid == undefined) //first time user in this browser
			{
				setsegment('All users', 0);
			}

			setsegment(localStorage.segmentname, localStorage.segmentid);
			$('.segment-label').html(localStorage.segmentname);

		}

		function setsegment(name, id) {
			localStorage.segmentid = id;
			localStorage.segmentname = name;
		}

		initsegmentselect();
	});
</script>
