<?
require_once "txfunctions.php";

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

	function asString(){
		return $this->asString_( txno($this->tx, $this->no) );
	}

	function asString2($tx){
		return $this->asString_( txno($tx) );
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

