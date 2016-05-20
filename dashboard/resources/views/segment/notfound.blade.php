@extends('layout.master', ['sidebarselect' => 'segment'])
@section('title', 'Segment')

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
