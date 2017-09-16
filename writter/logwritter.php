<?
require_once "writter.php";

class LogWritter implements Writter{
	private $w;

	function __construct(Writter $w){
		$this->w = $w;
	}

	function bl($bl, & $data){
	//	print_r($data);

		printf("BL: %s @ %s | TX: %4d\n", $bl, date("Y-m-d H:i:s", $data["time"]), count($data["tx"]));

		$this->w->bl($bl, $data);
	}

	function in($tx, $txoutput, $no){
		$this->w->in($tx, $txoutput, $no);
	}

	function out(TxPack $txp){
		$this->w->out($txp);
	}
}

