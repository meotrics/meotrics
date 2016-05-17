<?php

namespace App\Util;


use Illuminate\Support\Facades\DB;
use Mutex;

class AppCodeGen
{
	public static $mutex;
	public  static function used()
	{
		Mutex::unlock( AppCodeGen::$mutex);
	}

	public static function alloc($appname)
	{
		// this make sure to excute this one time in a row only
		Mutex::lock(AppCodeGen::$mutex);

		// remove all unicode character
		$appname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $appname);
		// remove all whitespace
		$appcode = preg_replace('/\s+/', '', $appname);

		// no more than 20 character
		$appcode = substr($appcode, 0, 20);
		$i = "";

		//check if appname is existed
		while (DB::table('apps')->count('code', $appcode . $i) != 0) {
			if ($i == "") $i = 0;
			$i++;
		}

		$appcode = $appcode . $i;
		
		return $appcode;
	}
}

AppCodeGen::$mutex = Mutex::create();