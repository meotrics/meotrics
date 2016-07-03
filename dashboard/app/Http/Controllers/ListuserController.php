<?php
/**
 * Created by PhpStorm.
 * User: vietle
 * Date: 7/3/16
 * Time: 1:55 AM
 */
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;

class ListuserController extends Controller{
    private $request;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('userprofile/index');
    }
}