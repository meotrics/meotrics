<?php namespace App\Http\Composers;

use Illuminate\View\View;
use \App\Util\MtHttp;

class MasterComposer
{

	public function compose(View $view)
	{
		$appid = 1;// $view->getData()['appid'];
		$segment = MtHttp::get(':2108/segment/' . $appid);
		$view->with('segment', json_encode($segment));
	}

}