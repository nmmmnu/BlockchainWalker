<?
require_once "jsonrpc.php";

class BitcoindRPC{
	const BL0	= "000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f";
	const TX0	= "4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b";

	const DEC	= 8;

	private $jrpc;

	function __construct($url, $user, $pass){
		$this->jrpc = new JSONRPC($url, $user, $pass);
	}

	private function json_exec_($method, $params = []){
		return $this->jrpc->exec($method, $params);
	}

	function info(){
		return $this->json_exec_("getinfo");
	}

	function lastBl(){
		return $this->json_exec_("getbestblockhash");
	}

	function getBl($hash){
		return $this->json_exec_("getblock", [ $hash ]);
	}

	function getBl0(){
		return $this->getBl(self::BL0);
	}

	function getTx($hash){
		if ($hash == self::TX0){
			// bitcoind can not find TX0
			return $this->getTx0();
		}

		$r = $this->json_exec_("getrawtransaction", [ $hash, 1 ]);

		if ($r === false)
			return false;

		return $r;
	}

	function getTx0(){
		return $this->decodeTx(self::getTX0_Raw());
	}

	function decodeTx($large_hash){
		return $this->json_exec_("decoderawtransaction", [ $large_hash ]);
	}

	private static function getTX0_Raw(){
		return	"01000000010000000000000000000000000000000000000000000000000000000000000000" .
			"ffffffff4d04ffff001d0104455468652054696d65732030332f4a616e2f32303039204368" .
			"616e63656c6c6f72206f6e206272696e6b206f66207365636f6e64206261696c6f75742066" .
			"6f722062616e6b73ffffffff0100f2052a01000000434104678afdb0fe5548271967f1a671" .
			"30b7105cd6a828e03909a67962e0ea1f61deb649f6bc3f4cef38c4f35504e51ec112de5c38" .
			"4df7ba0b8d578a4c702b6bf11d5fac00000000";
	}
}

