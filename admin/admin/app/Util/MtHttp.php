<?php namespace App\Util;

class MtHttp
{

	private static $port;
	private static $host;

	public static function json_decodeEx($data)
	{
		//unction isJson($string) {
		$ret = json_decode($data);
		return (json_last_error() != JSON_ERROR_NONE) ? $data : $ret;
		//}

	}

	public static function getRaw($url, $data = null)
	{
		$url = "http://" . self::$host . ":" . self::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'GET',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'GET'
			));
		$context = stream_context_create($options);
		return file_get_contents($url, false, $context);
	}

	public static function get($url, $data = null)
	{
		return self::json_decodeEx(self::getRaw($url, $data));
	}

	public static function post($url, $data = null)
	{
		$url = "http://" . self::$host . ":" . self::$port . '/' . $url;
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
		return self::json_decodeEx(file_get_contents($url, false, $context));
	}

	public static function put($url, $data)
	{
		$url = "http://" . self::$host . ":" . self::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'PUT',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'PUT'
			));
		$context = stream_context_create($options);
		return self::json_decodeEx(file_get_contents($url, false, $context));
	}

	public static function delete($url, $data)
	{
		$url = "http://" . self::$host . ":" . self::$port . '/' . $url;
		if ($data !== null)
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'DELETE',
				'content' => json_encode($data)
			));
		else
			$options = array('http' => array(
				'header' => "Content-type: application/json\r\n",
				'method' => 'DELETE'
			));
		$context = stream_context_create($options);
		return self::json_decodeEx(file_get_contents($url, false, $context));
	}

	public static function init()
	{
		self::$host = env('CORE_HOST', '127.0.0.1');
		self::$port = env('CORE_PORT', 2108);
	}
}

MtHttp::init();