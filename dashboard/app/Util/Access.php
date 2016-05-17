<?php

namespace App\Util;


class Access
{

	public static function getPerm($appid){

	}

	// used to add new user to app
	// or $userid set perm for $otheruserid,
	// if $can_perm is differ than null, then its value is valid
	// if $can_struct is differ than null, then its value is valid
	// if $can_reportis differ than null, then its value is valid
	// 0 means unset, 1 means set
	// return 0 if sucecss
	// -1: access deny
	// -2 other iser not exist in app, must add first
	public static function setPerm($userid, $otheruser, $appid, $can_perm, $can_struct, $can_report)
	{
		if (self::can_editPerm($userid, $appid)) {
			
			$perm = DB::table('user_app')->where('appid', $appid)->where('userid', $otheruser)->last();
			if ($perm == null) {
				DB::table('user_app')->insert(
					['appid' => $appid, 'userid' => $otheruser, 'can_perm' => 0, 'can_struct' => 0, 'can_report' => 1]
				);

			}
		 $permrecord = [];
			if ($can_perm != null) $permrecord['can_perm'] = $can_perm;
			if ($can_struct != null) $permrecord['can_struct'] = $can_struct;
			if ($can_report != null) $permrecord['can_report'] = $can_report;
			DB::table('user_app')->where('appid', $appid)->where('userid', $otheruser)->update($permrecord);
			return 0;
		}
		return -1;
	}

	public static function can_editPerm($userid, $appid)
	{
		$perm = DB::table('apps')->where('appid', $appid)->where('userid', $userid)->last();
		if ($perm == null) return false;
		if ($perm->can_perm == 1) return true;
		return false;
	}

	public static function can_editStruct($userid, $appid)
	{
		$perm = DB::table('apps')->where('appid', $appid)->where('userid', $userid)->last();
		if ($perm == null) return false;
		if ($perm->can_struct == 1) return true;
		return false;
	}

	public static function can_editReport($userid, $appid)
	{
		$perm = DB::table('apps')->where('appid', $appid)->where('userid', $userid)->last();
		if ($perm == null) return false;
		if ($perm->can_report == 1) return true;
		return false;
	}


	public static function can($userid, $appid, $action)
	{

	}

	public static function init()
	{

	}

}

Access::init();