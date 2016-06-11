<?php namespace App\Providers;

use DB;
use Illuminate\Support\ServiceProvider;
use Request;
use Route;
use Session;
use View;

class AppServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//view()->composer('segment.select', 'App\Http\Composers\SegmentSegment');

		View::composer('*', function ($view) {
			if (\Auth::user() == null) return;
			$userid = \Auth::user()->id;

			//abort(400, Request::route()->currentRouteName() );

			$param = Route::current()->parameters();

			if ($param == null)
				$appcode = null;
			else
				if(array_key_exists( 'appcode',$param))
				$appcode = $param['appcode'];
			else
				$appcode = '';
			/*
						if ($appcode == null || $appcode == '') // first time with no app
						{
							abort(505, 'appcode not found');
						}*/

			$view->with('userid', $userid);
			$view->with('appcode', $appcode);
		});

		View::composer('layout.master', function ($view) {
			if (\Auth::user() == null) return;
			$userid = \Auth::user()->id;
			$apps = DB::table('apps')->join('user_app', 'apps.id', '=', 'user_app.appid')
				->where('user_app.userid', $userid)->get();

			$view->with('apps', $apps);


			//abort(400, Request::route()->currentRouteName() );

			$param = Route::current()->parameters();

			if ($param == null)
				abort(505, 'route not found');
			else
				$appcode = $param['appcode'];

			if ($appcode == null || $appcode == '') // first time with no app
			{
				abort(505, 'appcode not found');
			} else {

				session(['appcode' => $appcode]);
				//get first app
				$ap = DB::table('apps')->where('code', $appcode)->first();

				if ($ap == null) {
					abort(404, 'appcode not found');
				}

				$view->with('appid', $ap->id);
				$view->with('appname', $ap->name);
				$view->with('appcode', $appcode);

			}
		});
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);
	}

}
