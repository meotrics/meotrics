<?php namespace App\Http\Composers;

use App\Http\Requests\Request;
use \App\Util\MtHttp;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

//App\Http\Composers\View
class MasterComposer
{

	public function compose(View $view)
	{
		$userid = \Auth::user()->id;

		$currentRoute = Route::current();
		$appid = $currentRoute->parameters('appid');
		if ($appid == null)
		{
			$appid = Request::cookie('currentappid');

		}
		if($appid == null) // first time with no app
		{
			//get first app
			$ua = DB::table('user_app')->where('userid', $userid)->first();
			if($ua == null)
			{
				$view->with('curappname', '<code>Setting</code>');
				$view->with('curappid', -1);
				return;
			}
			$appid = $ua->appid;
		}

		$ap = \App\App::find($appid);

		$view->with('curappname', $ap->name);
		$view->with('curappid', $appid);



	}

}