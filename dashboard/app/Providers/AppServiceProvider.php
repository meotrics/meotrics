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
				$appcode = Request::cookie('currentappcode');
			 else
				$appcode = $param['appcode'];

			if ($appcode == null || $appcode== '' ) // first time with no app
			{
				//get first app
				$ua = DB::table('user_app')->where('userid', $userid)->first();

				if ($ua == null) {
					$view->with('curappname', 'Setting');
					$view->with('curappid', '-1');
					return;
				}
				$view->with('curappid', $ua->appid);
			} else {
				$ap = DB::table('apps')->where('code', $appcode)->first();
				if ($ap == null)
					abort(500, 'app doesn\'t exist: ' . $appcode . '\/');
				$view->with('curappid', $ap->id);
			}






			$view->with('curappname', "\$ap->name");

			$view->with('curappcode', $appcode);
			$view->with('userid', $userid);

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
