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
		$mutex = fopen("./appcodegen.lock", "w");
		if (flock($mutex, LOCK_EX)) {
			// this make sure to excute this one time in a row only

			// remove all unicode non-letter characters
			$appname = preg_replace('/[[:^print:]]/', '', $appname);
			$appcode = preg_replace('/[\x00-\x2F\x3A-\x40\x5B-\x60\x7B-\xFF]/', '', $appname);

			// no more than 30 character
			$appcode = strtolower(substr($appcode, 0, 30));
			$i = "";
			//check if appname is existed
			while (DB::table('apps')->where('code', $appcode . $i)->count() != 0) {
				if ($i == "") $i = 0;
				$i++;
			}

			$appcode = $appcode . $i;

			return ['code' => $appcode, 'lock' => $mutex];

		} else {
			throw new Exception('Unable to gain lock!');
		}
	}
}