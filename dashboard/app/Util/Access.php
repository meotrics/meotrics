<?php

namespace App\Util;

use Illuminate\Support\Facades\DB;

class Access
{

	// list all perm
	// if the returned array is empty then user dont have permission to list the perms
	public static function listPerm($userid, $appcode)
	{
		if (self::can_editPerm($userid, $appcode) == false) return [];

		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;


		return DB::table('user_app')
			->join('users', 'user_app.userid', '=', 'users.id')
			->where('user_app.appid', $app->id)->get();
	}

	// used to delete user from app
	// return
	// -1 acess deny
	// -3 appid doesn't exist
	// -4 cannot delte owner
	//
	public static function deletePerm($userid, $otherid, $appcode)
	{
		// get owner
		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;

		if ($otherid == $app->ownerid) return -4;
		if (self::can_editPerm($userid, $app->id)) {
			DB::table('user_app')->where('appid', $app->id)->where('userid', $otherid)->delete();
			return 0;
		}
		return -1;
	}

	// used to add new user to app
	// or $userid set perm for $otheruserid,
	// if $can_perm is differ than null, then its value is valid
	// if $can_struct is differ than null, then its value is valid
	// if $can_reportis differ than null, then its value is valid
	// 0 means unset, 1 means set
	// return 0 if sucecss
	// -1: access deny
	// -2 other user not exist in app, must add first
	// -3 appid doesn't exist
	// -4 cannot set perm for owner
	// -5 if user doesn't exist
	public static function setPerm($userid, $otheruser, $appcode, $can_perm, $can_struct, $can_report)
	{
		//check if user existed
		if (DB::table('users')->where('id', $otheruser)->count() + DB::table('users')->where('id', $userid)->count() != 2)
			return -5;

		// get owner
		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;

		if (self::can_editPerm($userid, $appcode)) {

			$perm = DB::table('user_app')->where('appid', $app->id)->where('userid', $otheruser)->first();
			if ($perm == null) {
				if ($app->ownerid == $otheruser)
					DB::table('user_app')->insert(
						['appid' => $app->id, 'userid' => $otheruser, 'can_perm' => 1, 'can_struct' => 1, 'can_report' => 1]
					);
				else
					DB::table('user_app')->insert(
						['appid' => $app->id, 'userid' => $otheruser, 'can_perm' => 0, 'can_struct' => 0, 'can_report' => 1]
					);
			} else {
				if ($app->ownerid == $otheruser) {
					return -4;
				}
			}

			$permrecord = [];
			if ($can_perm != null) $permrecord['can_perm'] = $can_perm;

			if ($can_struct != null) $permrecord['can_struct'] = $can_struct;
			if ($can_report != null) $permrecord['can_report'] = $can_report;
			if (count($permrecord) != 0) {
				DB::table('user_app')->where('appid', $app->id)->where('userid', $otheruser)->update($permrecord);
			}
			return 0;
		}
		abort(500, "sdfdf");
		return -1;
	}

	public static function isBanned($id){
		$user = DB::table('users')->where('id',$id)->first();
		if($user == null) return true;
		if(!$user->banned)
			return false;
		return true;
	}

	public static function can_editPerm($userid, $appcode)
	{
		// full access for app owner
		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;
		if(self::isBanned($app->ownerid)) return false;
		if ($app->ownerid == $userid) return true;

		$perm = DB::table('user_app')->where('appid', $app->id)->where('userid', $userid)->first();
		if ($perm == null) return false;
		if ($perm->can_perm == 1) return true;
		return false;
	}

	public static function can_editStruct($userid, $appcode)
	{
		// full access for app owner
		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;
		if(self::isBanned($app->ownerid)) return false;
		if ($app->ownerid == $userid) return true;

		$perm = DB::table('user_app')->where('appid', $app->id)->where('userid', $userid)->first();
		if ($perm == null) return false;
		if ($perm->can_struct == 1) return true;
		return false;
	}

	public static function can_view($userid, $appcode)
	{
		// full access for app owner
		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;

		if(self::isBanned($app->ownerid)) return false;
		if ($app->ownerid == $userid) return true;

		$perm = DB::table('user_app')->where('appid', $app->id)->where('userid', $userid)->first();
		if ($perm == null) return false;
		return true;
	}

	public static function can_editReport($userid, $appcode)
	{
		// full access for app owner
		$app = DB::table('apps')->where('code', $appcode)->first();
		if ($app == null) return -3;

		if(self::isBanned($app->ownerid)) return false;
		if ($app->ownerid == $userid) return true;

		$perm = DB::table('user_app')->where('appid', $app->id)->where('userid', $userid)->first();
		if ($perm == null) return false;
		if ($perm->can_report == 1) return true;
		return false;
	}
}
