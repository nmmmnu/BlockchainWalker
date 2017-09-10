<?
class TxPack{
	public $tx;
	public $no;
	public $address;
	public $value;

	function __construct($tx, $no, $address, $value){
		$this->tx	= $tx		;
		$this->no	= $no		;
		$this->address	= $address	;
		$this->value	= $value	;
	}

	function txno(){
		return $this->tx . "." . $this->no;
	}

	static function txno2($tx){
		return $tx . "." . "-";
	}

	function asString(){
		return $this->asString_($this->txno());
	}

	function asString2($tx){
		return $this->asString_(self::txno2($tx));
	}

	private function asString_($txno){
		$del = "|";

		return sprintf(
			"TX: %-69s"	. $del .
			"Addr: %-34s"	. $del .
			"BTC: %.8f",
			$txno, $this->address, $this->value
		);
	}
};

