@extends('layout.main')
@section('header-script')
	<script>
		var userid = '{{$userid}}';
		var appcode = '{{$appcode}}';
		function onPageLoad(fn) {
			if (window.addEventListener)
				window.addEventListener('load', fn, false);
			else if (window.attachEvent)
				window.attachEvent('onload', fn);
		}

		function throttle(fn, delay) {
			var timer = null;
			return function () {
				var context = this, args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					fn.apply(context, args);
				}, delay);
			};
		}

		var _helper = {
			notification: {
				error: function (err, options) {

					options = options || {};
					options.type = 'danger';
					options.timer = 3000;
					options.placement = {
						from: 'top',
						align: 'right'
					};

					$.notify({
						icon: "pe-7s-attention",
						message: err
					}, options);
				},
				success: function (message, options) {
					options = options || {};
					options.type = 'success';
					options.timer = 3000;
					options.placement = {
						from: 'top',
						align: 'right'
					};

					$.notify({
						icon: "pe-7s-check",
						message: message
					}, options);
				}
			}
		}
	</script>

	<script>
		window.odometerOptions = {
			duration: 350
		};

		function update_action_count() {
			var data = websock.data[appcode].action_count;
			if (odometer.length !== undefined) {
				for (var i in odometer) if (odometer.hasOwnProperty(i))odometer[i].innerHTML = data;
			}
			else odometer.innerHTML = parseInt(data);
		}

		//websock.appChange(appcode, 'action_count', update_action_count);
		//update_action_count();
	</script>
@endsection
@section('body')

<div class="wrapper">
    <div class="sidebar main_sidebar" data-color="blue" data-image="/img/sidebar-4.jpg">
        <!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->
        <div class="sidebar-wrapper">
            <div class="logo">
                <a class="simple-text" href="{{ URL::to('/') }}">
                    <img src="{{ asset('img/meotrics_logo.png') }}"    style="width: 150px; margin-left: 14px;"/>
                    {{--<span class="logo-text"></span>--}}
                </a>
            </div>
            <ul class="nav">
                <li class="{{ Route::getCurrentRoute()->getPath() == 'home' ? 'active' : '' }}">
                    <a href="/dashboard/{{ $appcode }}">
                        <i class="pe-7s-graph"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="{{ Route::getCurrentRoute()->getPath() == 'trend' ? 'active' : '' }}">
                    <a href="/trend/{{$appcode}}">
                        <i class="pe-7s-graph1"></i>
                        <p>Trend</p>
                    </a>
                </li>
                <li class="{{ Route::getCurrentRoute()->getPath() == 'segment' ? 'active' : '' }}">
                    <a href="/segment/{{$appcode}}">
                        <i class="pe-7s-users"></i>
                        <p>Segmentation</p>
                    </a>
                </li>
                <li class="{{ Route::getCurrentRoute()->getPath() == 'funnel' ? 'active' : '' }}">
                    <a href="/funnel/{{$appcode}}">
                        <i class="pe-7s-filter"></i>
                        <p>Funnel</p>
                    </a>
                </li>
                {{--<li class="{{ Route::getCurrentRoute()->getPath() == 'revenue' ? 'active' : '' }}">--}}
                    {{--<a href="/revenue/{{$appcode}}">--}}
                        {{--<i class="pe-7s-cash"></i>--}}
                        {{--<p>Revenue</p>--}}
                    {{--</a>--}}
                {{--</li>--}}
                <li class="{{ Route::getCurrentRoute()->getPath() == 'segment' ? 'active' : '' }}">
                    <a href="/segment/{{$appcode}}">
                        <i class="pe-7s-graph3"></i>
                        <p>Marketing Campaign</p>
                    </a>
                </li>
                <li class="{{ Route::getCurrentRoute()->getPath() == 'insight' ? 'active' : '' }}">
                    <a href="/userprofile/{{$appcode}}">
                        <i class="pe-7s-user"></i>
                        <p>User Profile</p>
                    </a>
                </li>

                <!-- <li class="user-area">
                        <ul class="media-list" style="margin-left: 10px; margin-top: 10px;">
                                <li class="media">
                                        <div class="media-left">
                                                <a href="#">
                                                        <img class="media-object" width="40px" src="/img/user.png" alt="">
                                                </a>
                                        </div>
                                        <div class="media-body">
                                                <h5 style="color:white;" class="media-heading">thanhpk</h5>
                                                <a class="small" href="/auth/default/view?id=4">profile</a>
                                                &nbsp; &nbsp;
                                                <a class="small" href="/auth/logout" data-method="get">logout</a>
                                                <a class="small" href="/actiontype" data-method="get">action type</a>
                                        </div>
                                </li>
                        </ul>
                </li> -->
            </ul>
        </div>
    </div>

    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" style="padding-left: 0">
                    <ul class="nav navbar-nav navbar-left">
                        @yield('action')
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="hidden">
                            <a href="#">
                                <span class="vam">Action count:</span>
                                <span id="odometer" class="vam id_counter odometer"></span>
                            </a>
                        </li>
                        
                        <li class="dropdown" style="margin-top:6px;margin-bottom: 6px">
                            <a href="#" class="button action blue button-radius-mini"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="label limit-char font-mini current-appname" style="padding: 0px!important;margin-top:5px">{{ $appname }}</span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" style="min-width: 137px;">
                                @if(count($apps) !=0)
                                @foreach($apps as $ap)
                                @if($ap->code != $appcode)
                                <li><a href="/dashboard/{{$ap->code }}"> {{$ap->name}}</a></li>
                                @endif
                                @endforeach
                                @endif
                                <li  >
                                    {{--<a href="#" class="blue" data-toggle="modal" data-target="#addModal">Track new app</a>--}}
                                    <button type="button" class="action button blue" data-toggle="modal" data-target="#addModal" style="width: 100%">
                                        <span class="label">Track new app</span>
                                    </button>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="/img/meotrics_user.png" class="user-image" alt="User Image">
                                <span class="hidden-xs font-mini">{{ Auth::user()->email }}</span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" style="min-width: 262px">
<!--                                <li><a href="#" class="id_trackingcode">Get tracking code</a></li>
                                <li><a href="/actiontype/{{$appcode}}">Manage action types</a></li>
                                @if(count($apps) !=0)
                                <li class="divider"></li>
                                @foreach($apps as $ap)
                                @if($ap->code != $appcode)
                                <li><a href="/dashboard/{{$ap->code }}">Switch to {{$ap->name}}</a></li>
                                @endif
                                @endforeach-->
                                <li><a href="/app/manage/{{$appcode}}">App management</a></li>
                                <!--@endif-->
                                <li class="divider"></li>
                                <li><a href="/user/profile/{{$appcode}}">Profile & Billing</a></li>
                                <li><a href="/auth/signout">Sign out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-link pull-right">
                <a href="{{App\Enum\CommonEnum::LINK_DOCUMENTATION}}">Documentation</a>
                <a href="{{App\Enum\CommonEnum::LINK_API_DOC}}">API doc</a>
                <a href="javascript:void(0)">Support: {{App\Enum\CommonEnum::SUPPORT_PHONE}}</a>
            </div>
        </div>
    </div>
</div>

    {{--add new app--}}
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
@endsection

@section('footer-script')
	<script>
		onPageLoad(function () {
			$('.id_trackingcode').click(function () {
				showCodeDialog('{{$appcode}}');
			});
		});
		var config = {
			customOpenAnimation: function (cb) {
				$(this).fadeIn(300, cb);
			},
			customCloseAnimation: function (cb) {
				$(this).fadeOut(300, cb);
			}
		};


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

	@yield('additional')
	@yield('script')
	@include('partials.install_guide')
@endsection