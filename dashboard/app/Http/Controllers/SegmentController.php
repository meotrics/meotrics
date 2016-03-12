<?php namespace App\Http\Controllers;

class SegmentController extends Controller
{
	public function index()
	{

		$options = array(
			'http' => array(
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'GET',

			),
		);
		$context = stream_context_create($options);
		$actions = json_decode(file_get_contents('http://127.0.0.1:2108/actiontype/1', false, $context));
		$props = array(
			0 => array(
				'name' => 'gender',
				'dpname' => 'Gender'
			),
			1 => array(
				'name' => 'age',
				'dpname' => 'Age'
			),
			2 => array(
				'browser' => 'browser',
				'dpname' => 'Browser'
			)
		);

    return view('segment/index', ['actions' => json_encode($actions),'props' => json_encode($props)]);
  }
}
