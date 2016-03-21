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

		return view('actiontype/index', ['actiontypes' => $result]);
	}

	public function edit(Request $request)
	{
		if ($request->isMethod('post')) {

			$options = array(
				'http' => array(
					'header' => "Content-type: application/json\r\n",
					'method' => 'POST',
					'content' => json_encode(array(
							'event' => $request->input('typeid'),
							'object' => $request->input('object'),
							'operation' => $request->input('operation'),
							'param' => $request->input('param')
						)
					),
				),
			);
			$context = stream_context_create($options);
			$result = json_decode(file_get_contents('http://127.0.0.1:2108/trend/1', false, $context));
			return Redirect::action('TrendController@index');
		} else {
			return view('actiontype/edit');
		}
	}

	public function remove(Request $request, $a, $b)
	{
		var_dump($request, $a, $b);
		die;
	}

	public function create(Request $request)
	{
		if ($request->isMethod('post')) {

			$pcodes = $request->input('pcodes');
			$pnames = $request->input('pnames');
			//build array
			$fields = [];
			for ($i = 0; $i < count($pcodes); $i++) {
				array_push($fields, array("pname" => $pnames[$i], "pcode" => strtolower($pcodes[$i])));
			}

			$data = json_encode(array(
				'codename' => strtolower($request->input('codename')),
				'name' => $request->input('name'),
				'desc' => $request->input('desc'),
				'fields' => $fields
			));

			$options = array(
				'http' => array(
					'header' => "Content-type: application/json\r\n",
					'method' => 'POST',
					'content' => $data
				),
			);


			$context = stream_context_create($options);
			$result = json_decode(file_get_contents('http://127.0.0.1:2108/actiontype/1', false, $context));

			return Redirect::action('TrendController@index');
		} else {
			return view('actiontype/create');
		}
	}

}
