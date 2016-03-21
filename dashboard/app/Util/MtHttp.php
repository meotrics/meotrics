<?php namespace App\Util;

class MtHttp
{

	private static $port;
	private static $host;


	public static function get($url, $data = null)
	{
		$url = "http://" . MtHttp::$host . ":" . MtHttp::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'GET',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'POST'
			));
		$context = stream_context_create($options);
		return json_decode(file_get_contents($url, false, $context));
	}

	public static function post($url, $data = null)
	{
		$url = "http://" . MtHttp::$host . ":" . MtHttp::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'POST',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'POST'
			));
		$context = stream_context_create($options);
		return json_decode(file_get_contents($url, false, $context));
	}

	public static function put($url, $data)
	{
		$url = "http://" . MtHttp::$host . ":" . MtHttp::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'PUT',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'POST'
			));
		$context = stream_context_create($options);
		return json_decode(file_get_contents($url, false, $context));
	}

	public static function delete($url, $data)
	{
		$url = "http://" . MtHttp::$host . ":" . MtHttp::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'DELETE',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'POST'
			));
		$context = stream_context_create($options);
		return json_decode(file_get_contents($url, false, $context));
	}

	public static function init()
	{
		MtHttp::$host = env('CORE_HOST', '127.0.0.1');
		MtHttp::$port = env('CORE_PORT', 2108);
	}
}

MtHttp::init();