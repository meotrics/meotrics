@extends('layout.master')

@section('script')
	<script>
		function confirmDelete(acode){
			return confirm('Are you sure ? Detele `' + acode + '` action type !');
		}
	</script>
@endsection

@section('content')
<div class="row">
<div class="card col-md-12 vl-tab">
    <div class="row">
        <div class="app-manage">
            <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="">
                    <li role="presentation" class=""><a href="{{URL::to('app/manage/'.$appcode)}}">Home</a></li>
                    <li role="" class=""><a href="{{URL::to('app/edit/'.$appcode)}}" >Team member</a></li>
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Event management</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content vl-tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">
                        <div class="manage-area col-md-12">
                            <div class="title ">
                                <i class="fa fa-chevron-down" aria-hidden="true"></i> ACTION TYPE
                            </div>
                            <div class="header col-md-12">
                                <div class="row">
                                    <div class="description">
                                        <h6>Manage Action types or</h6>
                                    </div>
                                    <div class="track-new-app">
                                        <a href="/actiontype/{{$appcode}}/create" type="button" class="action button blue button-radius">
                                            <span class="label">Add new</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="content col-sm-12">
                                        <div class="content table-responsive table-full-width col-sm-12">

                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Properties</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($actiontypes as $type)
                                                    <tr>
                                                        <td><code class="fmonospaced">{{$type->codename}}</code></td>
                                                        <td>{{$type->name}}</td>
                                                        <td>{{$type->desc}}</td>
                                                        <td>
                                                            @foreach($type->fields as $field)
                                                            {{$field->pname}} (<code class="fmonospaced">{{$field->pcode}}</code>) <br/>
                                                            @endforeach
                                                        </td>
                                                        <td class="row">
                                                            <a class="btn btn-primary btn-sm btn-fill" href="/actiontype/{{$appcode}}/show/{{$type->_id}}"><i class="fa fa-edit"></i></a>
                                                            <form method="DELETE" action="/actiontype/{{$appcode}}/delete/{{$type->_id}}" style="display: inline-block" onsubmit="return confirmDelete('{{ $type->codename }}')">
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit" class="btn btn-danger  btn-sm btn-fill">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>

@endsection