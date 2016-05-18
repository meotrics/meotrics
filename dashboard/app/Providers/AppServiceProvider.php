<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;
use Route;
use Request;
use DB;

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
		View::composer('*', function( $view){
			$userid = \Auth::user()->id;

			$currentRoute = Route::current();
			$appid = $currentRoute->parameters('appid');

			if ($appid == null) {
				$appid = Request::cookie('currentappid');
			}

			if ($appid == null) // first time with no app
			{
				//get first app
				$ua = DB::table('user_app')->where('userid', $userid)->first();
				if ($ua == null) {
					$view->with('curappname', '<code>Setting</code>');
					$view->with('curappid', '-1');
					return;
				}
				$appid = $ua->appid;
			}

			$ap = \App\App::find($appid);

			$view->with('curappname', $ap->name);
			$view->with('curappid', $appid);
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
