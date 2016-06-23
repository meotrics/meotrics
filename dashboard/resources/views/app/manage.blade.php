@extends('layout.master')

@section('content')
<div class="row">
    <div class="card col-md-12">
        <div class="row">
            <!--<div class="header col-md-12">-->
            <div class="app-manage">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
                    <li role=""><a href="{{URL::to('app/edit/'.$appcode)}}" >Team member</a></li>
                    <li role="presentation"><a href="{{URL::to('actiontype/'.$appcode)}}" >Event management</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">
                        <div class="manage-area col-md-9">
                            <div class="title ">
                                <i class="fa fa-chevron-down" aria-hidden="true"></i> APP MANAGAMENT
                            </div>
                            <div class="header col-md-12">
                                <form class="form-horizontal pb10" method="post" action="{{URL::to('app/postadd/'.$appcode)}}">
                                    <div class="row">
                                        <div class="description">
                                            <h6>Manage App or</h6>
                                        </div>
                                        <div class="track-new-app">
                                            <button type="button" class="action button blue button-radius" data-toggle="modal" data-target="#addModal">
                                                <span class="label">Track new app</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h6>Name</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 style="color: black">{{$ap->name}}</h6>
                                            <input type="text" class="form-control " name="name" required="" value="{{$ap->name}}" style="display: none">
                                        </div>
                                        <div class="btn-group">
                                            <a type="button" class="a-edit-obj" data-href="https://app.meotrics.com/trend/meotrics/update" href="javascript:void(0)">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a type="button" class="a-save-obj" data-href="https://app.meotrics.com/trend/meotrics/update" href="javascript:void(0)">
                                                <i class="fa fa-save" style="margin-left: 8px"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h6>URL</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 style="color: black">{{$ap->url}}</h6>
                                            <input type="text" class="form-control " name="url" required="" value="{{$ap->url}}" style="display: none">
                                        </div>
                                        <div class="btn-group">
                                            <a type="button" class="a-edit-obj" data-href="https://app.meotrics.com/trend/meotrics/update" href="javascript:void(0)">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a type="button" class="a-save-obj" data-href="https://app.meotrics.com/trend/meotrics/update" href="javascript:void(0)">
                                                <i class="fa fa-save" style="margin-left: 8px"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h6>Traffic</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 style="color: black">{{$traffic}}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h6>Status</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 style="color: black">{{$status}}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h6>Your role</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 style="color: black">{{$your_role}}</h6>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="manage-area col-md-9">
                            <div class="title ">
                                <i class="fa fa-chevron-down" aria-hidden="true"></i> CODE SETTING
                            </div>
                            <div class="header col-md-12">
                                <form class="form-horizontal pb10" method="post" action="/trend/meotrics/write">
                                    <div class="row">
                                        <div class="description">
                                            <h6>Javascript Tracking code</h6>
                                            <h6><span>Paste this snippet just before the&nbsp;<b>&lt;/head&gt;</b>&nbsp;tag of your page:</span></h6>
                                        </div>
                                    </div>
                                    <div class="row" style="padding-left: 25px">
                                        <pre class="pre_client_setup ng-binding" style="border-left: 4px solid #4B8DF8;">&lt;script type="text/javascript"&gt; 
                                        var mtxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
                                        document.write(unescape("%3Cscript src='" + mtxJsHost + "www.metrixa.com/ConvTrackNew/Scripts/metrixa_search_tracker.js' type='text/javascript'%3E%3C/script%3E"));
                                    &lt;/script&gt;
                                    &lt;script type="text/javascript"&gt;
                                        try {
                                            var tracker = new Tracker('MTX-1085');
                                            tracker.request();
                                        } catch (err) { }
                                    &lt;/script&gt;</pre>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div role="tabpanel" class="tab-pane inactive" id="profile">profile</div>
                    <div role="tabpanel" class="tab-pane inactive" id="messages">massages</div>
                </div>

            </div>
            <!--</div>-->
        </div>
    </div>
</div>
@endsection

@section('additional')
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Track new app</h4>
            </div>
            <div class="modal-body">
                <div class="row pt pb10">
                    <div class="col-sm-4 ">
                        <h6 class="pull-right">name of the app</h6>
                    </div>
                    <div class="col-sm-7">
                        <input type="text" class="form-control id_name" placeholder="App Name" required>
                    </div>
                </div>

                <div class="row pt pb10">
                    <div class="col-sm-4 ">
                        <h6 class="pull-right">url of the app</h6>
                    </div>
                    <div class="col-sm-7">
                        <input type="text" class="form-control id_url" placeholder="App URL" >
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="button action ">
                    <span class="label">Cancel</span></button>
                <button type="button" data-dismiss="modal" class="button action blue id_add">
                    <span class="label">Next step</span></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('.a-edit-obj').click(function(){
    var that = $(this);
    that.parent().prev().find('h6').hide();
    that.parent().prev().find('input').show();
});

$('.a-save-obj').click(function(){
    var that = $(this);
    var form = that.closest('form');
    $.ajax({
        url: form.attr('action'),
        data: form.serialize(),
        dataType: 'JSON',
        type: 'POST',
        success: function(data){
            if(data.success){
                that.parent().prev().find('h6').show();
                that.parent().prev().find('input').hide();
            }
        }
    });
});

$('.id_add').click(function () {
    $.post('/app/create', {name: $('.id_name').val(), url:$('.id_url').val()}, function (appcode) {
        showCodeDialog(appcode, function () {
            //redirect to new app code
            var url_new = location.href.replace("/<?= $appcode ?>", "/"+appcode);
            location.href = url_new;
        });
    }).fail(function () {
        alert('cannot create app');
    });
    $('.id_name').val("");
    $('.id_url').val("");
});
function getIntegrationCode(appcode) {
    $.get('/track.html', function (response) {
        var html = $('<div/>').text(response).html();
        $('.pre_client_setup').html(html.replace(/\$APPID\$/g, appcode));
    });
}
getIntegrationCode('<?= $appcode ?>');
</script>
@endsection