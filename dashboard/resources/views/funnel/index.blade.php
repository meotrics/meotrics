@extends('layout.master', ['sidebarselect' => 'funnel'])
@section('title', 'Funnel')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row" id="managerfunnel">
                <div class="card col-md-12">
                    <div class="header row col-segment">
                        <div class="col-md-4">
                            <select class="form-control" id="selectsegment">
                                <option v-for="item in data_segment" :value="item._id" >@{{item.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="action button blue button-radius" v-on:click="addSegment">
                                <span class="label">Add Segment</span>
                            </button>
                        </div>
                    </div>
                    <div class="content row">
                        <div class="col-md-12" id="listsegment">
                            <div class="col-md-2" v-for="segment_item in list_choice">
                                {{--<label>@{{ segment_item.name }}</label>--}}
                                {{--<button type="button" rel="tooltip" title="" class="btn btn-danger btn-simple btn-xs" v-on:click="removeSegment(segment_item)">--}}
                                    {{--<i class="fa fa-times"></i>--}}
                                {{--</button>--}}
                                {{--<button type="button" aria-hidden="true" class="close" v-on:click="removeSegment(segment_item)">×</button>--}}
                                <span class="tag tag-pill tag-success" style="border: 1px solid #968d8d;padding: 6px;border-radius: 14px;">
                                <label>@{{ segment_item.name }}</label>
                                <button type="button" rel="tooltip" title="" class="btn btn-danger btn-simple btn-xs" v-on:click="removeSegment(segment_item)">
                                    <i class="fa fa-times"></i>
                                </button>
                            </span>
                            </div>
                        </div>
                        <div class="col-md-12" id="canvas-chart" style="margin-top:20px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/vendor.js"></script>
    <script src="/js/funnel/index.js"></script>
    <script src="{{asset('js/Chart.js')}}"></script>
    <script>
        funnelApp.data_segment = {!! json_encode($segments) !!};
    </script>
@endsection


