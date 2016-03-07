<?php namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TypeController extends Controller
{
	public function index(Request $request)
	{
		$options = array(
			'http' => array(
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'GET',

			),
		);
		$context = stream_context_create($options);
		$result = json_decode(file_get_contents('http://127.0.0.1:2108/actiontype/1', false, $context));

		return $result;
		return view('actiontype\index', ['actiontypes' => $result]);
	}

	public function edit(Request $request)
	{
		if($request->isMethod('post')){

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode(array(
							'event' => $request->input('typeid') ,
							'object' => $request->input('object'),
							'operation' => $request->input('operation'),
							'param' => $request->input('param')
						)
					),
				),
			);
			$context  = stream_context_create($options);
			$result = json_decode(file_get_contents('http://127.0.0.1:2108/trend/1', false, $context));
			return Redirect::action('TrendController@index');
		}
		else{
			return view('actiontype/edit');
		}
	}
	public function create(Request $request)
	{
		if($request->isMethod('post')){

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode(array(
							'appid' => 1 ,
							'codename' => $request->input('codename'),
							'name' => $request->input('name'),
							'desc' => $request->input('desc'),
							'fields' => $request->input('fields')
						)
					),
				),
			);
			$context  = stream_context_create($options);
			$result = json_decode(file_get_contents('http://127.0.0.1:2108/actiontype/1', false, $context));
			return Redirect::action('TrendController@index');
		}
		else{
			return view('actiontype/create');
		}
	}

}
