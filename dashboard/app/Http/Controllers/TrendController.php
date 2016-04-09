<?php
namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class TrendController extends Controller
{


	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getSave(Request $request)
	{
		$data = array(
			'typeid' => $request->input('typeid'),
			'object' => $request->input('object'),
			'operation' => $request->input('operation'),
			'param' => $request->input('param'),
			'order' => intval($request->input('order')),
			'name' => $request->input('name'),
		);
		return MtHttp::post('trend/1', $data);
	}

	public function getList()
	{
		return json_encode(MtHttp::get('trend/' . '1'));
	}

	public function getDelete()
	{

	}

	public function getIndex()
	{
		$actiontypes = MtHttp::get('actiontype/' . '1');
		$trends = MtHttp::get('trend/' . '1');
		return view('trend/index', [
			'types' => json_encode($actiontypes),
			'trends' => json_encode($trends)
		]);
	}

	public function getQuery(Request $request)
	{
		if ($request->input('_id') != null) {
			$trendid = $request->input('_id');
		} else {

			$data = array(
				'typeid' => $request->input('typeid'),
				'object' => $request->input('object'),
				'operation' => $request->input('operation'),
				'param' => $request->input('param'),
				'order' => intval($request->input('order')),
				'name' => 'Draft',
				'_isDraft' => true
			);
			$trendid = MtHttp::post('trend/1', $data);
		}

		$result = MtHttp::get('trend/query/1/' . $trendid);
		return $result;
	}
        
	public function show()
	{
		return "";
	}
        
        public function getCreate(){
            $app_id = \Auth::user()->id;
            $actiontypes = MtHttp::get('actiontype/' . $app_id);
            return view('trend/create', [
                'app_id' => $app_id,
                'actiontypes' => $actiontypes,
            ]);
        }
        
        public function postWrite(Request $request){
            if(isset($_POST['Trend'])){
                $data_post = $_POST['Trend'];
                $validator = Validator::make(
                    $data_post,
                    [
                        'typeid' => 'required',
                        'object' => 'required',
                        'opration' => 'required',
                        'param' => 'required',
                        'order' => 'required',
                        'name' => 'required|min:2|max:4|email',
                    ]
                );
                if ($validator->fails()){
                    return redirect()->back()->withErrors($validator);
                }
                $data = array(
                    'typeid' => isset($data_post['typeid']) ? $data_post['typeid'] : '',
                    'object' => isset($data_post['object']) ? $data_post['object'] : '',
                    'operation' => isset($data_post['opration']) ? $data_post['opration'] : '',
                    'param' => isset($data_post['param']) ? $data_post['param'] : '',
                    'order' => isset($data_post['order']) ? $data_post['order'] : '',
                    'name' => isset($data_post['name']) ? $data_post['name'] : '',
                    '_isDraft' => true,
                );
                $app_id = \Auth::user()->id;
                $trendid = MtHttp::post('trend/' . $app_id, $data_post);
		return redirect('trend');
            }
            return false;
        }
        
        public function getMeotrics($app_id, $action_id){
            $result = ['success' => false];
            /*** get meotrics by appid and actionid ***/
            
            $meotrics = [];
            for ($i = 0; $i< 5; $i++){
                $meotric = new \stdClass();
                $meotric->name = "Number of pageview ".$i;
                $meotric->operation = "count ".$i;
                $meotric->param = "url ".$i;
                $meotrics[] = $meotric;
            }
            $result['success'] = true;
            $result['meotrics'] = $meotrics;
            return response()->json($result);
        }
}
