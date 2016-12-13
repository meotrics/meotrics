<?php
/**
 * Created by PhpStorm.
 * User: vietle
 * Date: 12/11/16
 * Time: 6:24 PM
 */
?>
@extends('layouts.main')
@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">List User</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div id="tabeluser">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Verified</th>
                        <th>Created time</th>
                        <td>ListApp</td>
                    </tr>
                    <tr v-for="(value,index) in listUser">
                        <td>@{{index}}</td>
                        <td>@{{ value.name }}</td>
                        <td>@{{value.email}}</td>
                        <td>@{{value.phone}}</td>
                        <td>@{{value.verified}}</td>
                        <td>@{{ (new Date(value.created_at * 1000)).toLocaleString()}}</td>
                        <td>
                            <a v-on:click="showApp(value.email,index)">Show</a>
                            <div  v-if="value.render">
                                <p v-for="item in value.listemail">@{{ item.name }}</p>
                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <script src="/js/vendor.js"></script>
    <script src="/js/user/index.js"></script>
@endsection


