<?php
namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use stdClass;
use function redirect;
use function response;
use function view;
use App\Enum\TrendEnum;

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
            $app_id = \Auth::user()->id;
            $actiontypes = MtHttp::get('actiontype/' . $app_id);
            $trends = MtHttp::get('trend/' . $app_id);
            $trend = reset($trends);
            $outputs = MtHttp::get('trend/query/' . $app_id .'/'. $trend->_id);
            
            return view('trend/index', [
                'types' => json_encode($actiontypes),
                'trends' => $trends,
                'outputs' => $outputs,
                'actiontypes' => $actiontypes,
            ]);
	}

	public function getQuery(Request $request)
	{
            $result = ['success' => false];
            if ($request->input('_id') != null) {
                $trendid = $request->input('_id');
                $app_id = \Auth::user()->id;
                $outputs = MtHttp::get('trend/query/'.$app_id.'/' . $trendid);    
                $result['success'] = true;
                $result['outputs'] = $outputs;
            } 
            else {
//			$data = array(
//				'typeid' => $request->input('typeid'),
//				'object' => $request->input('object'),
//				'operation' => $request->input('operation'),
//				'param' => $request->input('param'),
//				'order' => intval($request->input('order')),
//				'name' => 'Draft',
//				'_isDraft' => true
//			);
//			$trendid = MtHttp::post('trend/1', $data);
                    $result['success'] = false;
            }
            return $result;
	}
        
	public function show()
	{
		return "";
	}
        
        public function getCreate(){
            $app_id = \Auth::user()->id;
            $actiontypes = MtHttp::get('actiontype/' . $app_id);
            $actiontype_first = $actiontypes && $actiontypes[0] ? $actiontypes[0] : (object)[
                'name' => '',
                'codename' => '',
                'fields' => [],
            ];
            $trend = (object)[
                '_id' => '',
                'name' => '',
                'typeid' => $actiontype_first->codename,
                'object' => '',
                'operation' => '',
                'param' => '',
                'order' => TrendEnum::DEFAULT_ORDER,
            ];
            return view('trend/c_u', [
                'app_id' => $app_id,
                'actiontypes' => $actiontypes,
                'trend' => $trend,
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
                        'operation' => 'required',
                        'param' => 'required',
                        'order' => 'required',
                        'name' => 'required',
                    ]
                );
                if ($validator->fails()){
                    return redirect()->back()->withErrors($validator);
                }
                $data = array(
                    'typeid' => isset($data_post['typeid']) ? $data_post['typeid'] : '',
                    'object' => isset($data_post['object']) ? $data_post['object'] : '',
                    'operation' => isset($data_post['operation']) ? $data_post['operation'] : '',
                    'param' => 'price',
                    'order' => isset($data_post['order']) ? (int)$data_post['order'] : (int)TrendEnum::DEFAULT_ORDER,
                    'name' => isset($data_post['name']) ? $data_post['name'] : '',
                    '_isDraft' => false,
                );
                $app_id = \Auth::user()->id;
                if(isset($data_post['_id']) && $data_post['_id']){
                    $trendid = MtHttp::put('trend/' . $app_id .'/'.$data_post['_id'] , $data);
                }
                else{
                    $trendid = MtHttp::post('trend/' . $app_id, $data);
                }
		return redirect('trend');
            }
            return false;
        }
        
        public function getMeotrics($app_id, $action_id){
            $result = ['success' => false];
            /*** get meotrics by appid and actionid ***/
            
            $meotrics = [];
            for ($i = 0; $i< 5; $i++){
                $meotric = new stdClass();
                $meotric->name = "Number of pageview ".$i;
                $meotric->operation = TrendEnum::DEFAULT_OPERATION;
                $meotric->param = TrendEnum::DEFAULT_PARAM;
                $meotrics[] = $meotric;
            }
            $result['success'] = true;
            $result['meotrics'] = $meotrics;
            return response()->json($result);
        }
        
    public function getHtmloutputs(Request $request)
    {
        $result = ['success' => false];
        if ($request->input('_id') != null) {
            $trendid = $request->input('_id');
            $app_id = \Auth::user()->id;
            $outputs = MtHttp::get('trend/query/'.$app_id.'/' . $trendid);    
            $view = View::make('trend.partials.outputs', [
                'outputs' => $outputs,
            ]);
            $contents = $view->render();
            $result['success'] = true;
            $result['html_outputs'] = $contents;
        } 
        else {
                $result['success'] = false;
        }
        return $result;
    }
    
    public function getUpdate($id){
        $trend = $this->loadTrend($id);
        $app_id = \Auth::user()->id;
        $actiontypes = MtHttp::get('actiontype/' . $app_id);
        $actiontype_first = $actiontypes && $actiontypes[0] ? $actiontypes[0] : (object)[
            'name' => '',
            'codename' => '',
            'fields' => [],
        ];
        return view('trend/c_u', [
            'app_id' => $app_id,
            'actiontypes' => $actiontypes,
            'trend' => $trend,
        ]);
    }
    
    public function loadTrend($id){
        $app_id = \Auth::user()->id;
        $trend = MtHttp::get('trend/'.$app_id.'/' . $id);  
        if($trend){
            return $trend;
        }
        else{
            App::abort(404, 'Trend not found');
        }
    }
    
    public function deleteRemove($id){
        $result = ['success' => false];
        $app_id = \Auth::user()->id;
        $trend = $this->loadTrend($id);
        $result = MtHttp::delete('trend/'.$app_id.'/' . $id, null);  
        //check result overhere
        
        $result['success'] = true;
        return $result;
    }
}
