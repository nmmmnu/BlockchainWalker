<?
require_once "writter/keyvaluewritter.php";
require_once "vendor/TinyRedisClient.php";

class ZuseWritter extends KeyValueWritter{
	private $r;

	function __construct($host){
		$this->r = new TinyRedisClient($host);
	}

	protected function write_($key, $value){
		$this->r->set($key, $value);
	}
}

