@extends('layout.master', ['sidebarselect' => 'trend'])
@section('title', 'Trend not found')

@section('action')
	<li>
		<a href="{{ URL::to('trend/'. $appcode . '/create') }}" class="button action blue button-radius"
		   style="margin-left: -14px;">
			<span class="label">New Trend</span>
		</a>
	</li>
@endsection

@section('content')
	<div class="row">
		<div class="card col-md-12">
			<div class="header row col-segment">
				Cannot found this trend, please go  <a href="/trend/{{$appcode}}">back</a>
			</div>
			<div class="content row">

			</div>
		</div>
	</div>
@endsection
