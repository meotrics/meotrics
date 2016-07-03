@extends('layout.master')
@section('script')
	<script>
		onPageLoad(function () {
			$('.id_grant').click(function () {
				var email = prompt('Please type user email');
				$.post('/perm/{{$appcode}}/add', {email: email}, function () {
					location.reload();
				}).fail(function () {
					alert('something went wrong, maybe server has disconnection or access denied');
				});
			});
		});
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
                    <li role="" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab" >Team member</a></li>
                    <li role="presentation"><a href="{{URL::to('actiontype/'.$appcode)}}">Action type</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content vl-tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">
                        <div class="manage-area col-md-12">
                            <div class="title ">
                                <i class="fa fa-chevron-down" aria-hidden="true"></i> ACCESS CONTROL
                            </div>
                            <div class="header col-md-12">
                                <table class="table table-hover" style="margin-bottom: 0">
                                    <thead>
                                        <tr class="title-table">
                                            <th style="padding-left: 0px">Email</th>
                                            <th>Full control</th>
                                            <th>Edit app structure</th>
                                            <th>Create or edit reports</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ap->agencies as $ag)
                                        <tr id="tr{{$ag->userid}}">
                                            <td style="padding-left: 0px">{{$ag->email}}</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" class="onoffswitch-checkbox"
                                                           id="of_{{$ag->userid}}_1" {{$ag->can_perm == 1 ? 'checked':''}} >
                                                    <label class="onoffswitch-label" for="of_{{$ag->userid}}_1"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" class="onoffswitch-checkbox"
                                                           id="of_{{$ag->userid}}_2" {{$ag->can_struct == 1 ? 'checked':''}}>
                                                    <label class="onoffswitch-label" for="of_{{$ag->userid}}_2"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" class="onoffswitch-checkbox"
                                                           id="of_{{$ag->userid}}_3" {{$ag->can_report == 1 ? 'checked':''}}>
                                                    <label class="onoffswitch-label" for="of_{{$ag->userid}}_3"></label>
                                                </div>
                                            </td>
                                            <td><a href="#" id="rm{{$ag->userid}}" data-appid={{$appcode}} data-userid="{{$ag->userid}}">Remove
                                                    access</a></td>
                                        </tr>

                                    <script class="hidden">
                                            onPageLoad(function () {
                                            $('#rm{{$ag->userid}}').click(function () {
                                                var ok = confirm("are you sure want to delete access from user {{$ag->name}}?");
                                                if (ok) {
                                                    $.post('/perm/{{$appcode}}/delete/{{$ag->userid}}', function () {
                                                        $('#tr{{$ag->userid}}').addClass('hidden');
                                                    }).fail(function () {
                                                        alert('something went wrong, maybe server has disconnection or access denied');
                                                    });
                                                }
                                            });

                                            $('#of_{{$ag->userid}}_1').change(function () {
                                                var $this = $(this);
                                                $.post('/perm/{{$appcode}}/set/{{$ag->userid}}', {can_perm: $this.prop('checked') == true ? 1 : 0}, function () {
                                                }).fail(function () {
                                                    $this.prop('checked', !$this.prop('checked'));
                                                    alert('something went wrong, maybe server has disconnection or access denied');
                                                });
                                            });
                                            $('#of_{{$ag->userid}}_2').change(function () {
                                                var $this = $(this);
                                                $.post('/perm/{{$appcode}}/set/{{$ag->userid}}', {can_struct: $this.prop('checked') == true ? 1 : 0}, function () {
                                                }).fail(function () {
                                                    $this.prop('checked', !$this.prop('checked'));
                                                    alert('something went wrong, maybe server has disconnection or access denied');
                                                });
                                            });
                                            $('#of_{{$ag->userid}}_3').change(function () {
                                                var $this = $(this);
                                                $.post('/perm/{{$appcode}}/set/{{$ag->userid}}', {can_report: $this.prop('checked') == true ? 1 : 0}, function () {
                                                }).fail(function () {
                                                    $this.prop('checked', !$this.prop('checked'));
                                                    alert('something went wrong, maybe server has disconnection or access denied');
                                                });
                                            });
                                        });
                                    </script>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-12" style="padding-top: 0">
                                        <a class="id_grant" href="#">Grant access for other user</a>
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