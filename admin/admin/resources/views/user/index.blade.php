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
    <div class="box" id="tabeluser">
        <div class="box-header with-border">
            <h3 class="box-title">List User</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div >
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Ban</th>
                        <th>Created time</th>
                        <th>Expired</th>
                        <td>ListApp</td>
                    </tr>
                    <tr v-for="(value,index) in listUser">
                        <td>@{{index}}</td>
                        <td>@{{ value.name }}</td>
                        <td>@{{value.email}}</td>
                        <td>@{{value.phone}}</td>
                        <td v-on:click="changeban(value.email,index,value.banned)">
                            <div v-if="!value.banned"><i class="fa fa-fw fa-check alert-success"></i></div>
                            <div v-else><i class="fa fa-fw fa-ban alert-danger"></i></div>
                        </td>
                        <td>@{{ (new Date(value.created_at * 1000)).toLocaleString()}}</td>
                        <td>@{{ value.expired }}</td>
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
        <div class="modal fade" tabindex="-1" role="dialog" id="myModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Ban</h4>
                    </div>
                    <div class="modal-body">
                        <p>Email: @{{ban.email}}</p>
                        <input type="text" class="form-control" placeholder="reason" v-model="ban.message">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" v-on:click="saveban">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>

    <script src="/js/vendor.js"></script>
    <script src="/js/user/index.js"></script>
@endsection


