<?php namespace App\Http\Composers;

use \App\Util\MtHttp;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

//App\Http\Composers\View
class SegmentSegment
{

	public function compose(View $view)
	{
		$appid = \Auth::user()->id;
		$segments = MtHttp::get('segment/' . $appid);
		$view->with('segments', ($segments));
	}

}