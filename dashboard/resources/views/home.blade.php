@extends('layout.master')
@section('title', 'Meotrics')
@section('style')

@endsection
@section('script')
@endsection

@section('content')
	THIS IS HOME {{cookie('laravel_session')}}
@endsection


@section('additional')
  @include('partials/install_guide')
@endsection
