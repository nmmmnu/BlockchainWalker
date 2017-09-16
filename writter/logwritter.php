<?
require_once "writter.php";

class LogWritter implements Writter{
	private $w;

	function __construct(Writter $w){
		$this->w = $w
	}

	function bl($bl){
		echo "Block: $bl\n";

		$this->w->bl($bl);
	}

	function in($tx, $txoutput, $no){
		$this->w->in($tx, $txoutput, $no);
	}

	function out(TxPack $txp){
		$this->w->out($txp));
	}
}

