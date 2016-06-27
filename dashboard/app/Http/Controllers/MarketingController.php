<?php
/**
 * Created by PhpStorm.
 * User: vietle
 * Date: 6/26/16
 * Time: 10:19 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;

class MarketingController extends Controller{
    private $request;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('marketing/index');
    }
}