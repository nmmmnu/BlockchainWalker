<?
require_once "writter.php";

class CommentWritter implements Writter{
	private $in;
	private $out;

	function __construct($in = true, $out = true){
		$this->in  = $in;
		$this->out = $out;
	}

	function in($tx, TxPack $txp){
		if ($this->in){
			echo $txp->asString2($tx);
			echo "\n";
		}
	}

	function out(TxPack $txp){
		if ($this->out){
			echo $txp->asString();
			echo "\n";
		}
	}
}

