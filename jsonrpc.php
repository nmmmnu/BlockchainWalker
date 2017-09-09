<?
class JSONRPC{
	private $url;
	private $user;
	private $pass;

	function __construct($url, $user, $pass){
		$this->url	= $url;
		$this->user	= $user;
		$this->pass	= $pass;
	}

	private static function post__($url, $user, $pass, $data){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/plain']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $pass);

		$r = curl_exec($ch);

		return $r;
	}

	private function post_($data){
		return self::post__($this->url, $this->user, $this->pass, $data);
	}

	private static function json_prepare__($method, $params){
		$m = [
			"jsonrpc"	=> "1.0"	,
			"id"		=> 1		,
			"method"	=> $method	,
			"params"	=> $params
		];

		return json_encode($m);
	}

	function exec($method, $params = []){
		$r =  json_decode($this->post_(self::json_prepare__($method, $params)), true);

		/*
			Array
			(
			    [result] => Array
			        (
			        )
			    [error] =>
			    [id] => 1
			)
		*/

		if ($r["error"])
			return false;

		return $r["result"];
	}
}

