<?php

$segments = isset($segments) ? $segments : [];
$props = isset($props) ? $props : [];
?>
@extends('layout.master', ['sidebarselect' => 'segment'])
@section('title', 'Segment')
@section('script')
	<script type="text/javascript">
		var segments = {};
		<?php
						if($segments):
						$segment_first = null;

						if($segmentid != null)
						$segment_first = $segmentid;
						else
						foreach ($segments as $key => $segment):
						if ($key == 0) {
							$segment_first = $segment;
						}
						?>
						segments['{{ $segment->_id }}'] = {
			name: '{{ property_exists($segment, 'name') ? $segment->name : '' }}',
			description: '{{property_exists($segment, 'description') ? $segment->description : '' }}',
			count: '{{ property_exists($segment, 'count') ? $segment->count : '' }}'
		};
		<?php
		endforeach;
		endif;
		?>
	</script>
@endsection

@section('style')
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
@endsection

@section('action')
	<li>
		<a href="{{ URL::to('segment/'. $appcode . '/create') }}" class="button action blue button-radius"
		   style="margin-left: -14px;">
			<span class="label">New Segmentation</span>
		</a>
	</li>
@endsection

@section('content')
	<div class="row">
		<div class="card col-md-12">
			<div class="header row col-segment">
				Cannot found this segmentation, please go  <a href="/segment/{{$appcode}}">back</a>
			</div>
			<div class="content row">

			</div>
		</div>
	</div>
@endsection
