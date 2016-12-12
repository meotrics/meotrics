@extends('layouts.main')
@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">List App</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div id="tabelapp">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Name</th>
                        <th>Core</th>
                        <th>URL</th>
                        <td>Count user</td>
                    </tr>
                    <tr v-for="(value,index) in listApp">
                        <td>@{{index}}</td>
                        <td>@{{value.name}}</td>
                        <td>@{{value.core}}</td>
                        <td>@{{value.url}}</td>
                        <td>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <script src="/js/vendor.js"></script>
    <script src="/js/app/index.js"></script>
@endsection
