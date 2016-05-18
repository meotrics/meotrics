<?php

namespace App\Util;


use Illuminate\Support\Facades\DB;

class AppCodeGen
{
	public static function used($mutex)
	{
		flock($mutex, LOCK_UN);
		fclose($mutex);
	}

	public static function alloc($appname)
	{
		$mutex = fopen("./appcodegen.lock", "r+");
		if (flock($mutex, LOCK_EX)) {
			// this make sure to excute this one time in a row only

			// remove all unicode character
			$appname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $appname);
			// remove all whitespace
			$appcode = preg_replace('/\s+/', '', $appname);

			// no more than 20 character
			$appcode = strtoupper(substr($appcode, 0, 20));
			$i = "";
			//check if appname is existed
			while (DB::table('apps')->where('code', $appcode . $i)->count() != 0) {
				if ($i == "") $i = 0;
				$i++;
			}

			$appcode = $appcode . $i;

			return ['code'=>$appcode, 'lock'=>$mutex];

		} else {
			throw new Exception('Unable to gain lock!');
		}

	}

}