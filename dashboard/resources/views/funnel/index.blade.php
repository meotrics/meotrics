@extends('layout.master', ['sidebarselect' => 'funnel'])
@section('title', 'Funnel')
@section('content')
    <div style="    position: absolute;
    background-color: #000;
    opacity: .3;
    top: 60px;
    bottom: -300px;
    left: 0;
    right: 0;
    ">
    </div>
    <div style=" margin: 150px auto;position: absolute;    width: 100%;
    text-align: center;" role="document">
        {{--<div class="modal-content">--}}
                <img src="/img/meo-05.png">
        {{--</div>--}}
    </div>
    <div>
        <img src="/img/Funnel_Final.png" style="width: 100%">
    </div>
@endsection


