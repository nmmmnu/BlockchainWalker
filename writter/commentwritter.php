<?
require_once "writter.php";

class CommentWritter implements Writter{
	private $in;
	private $out;

	function __construct($in = true, $out = true){
		$this->in  = $in;
		$this->out = $out;
	}

	private static function txno__($tx, $no){
		return $tx . "." . $no;
	}

	function in($tx, $txoutput, $no){
		if ($this->in){
			$del = "|";

			printf(
				"TX: %-69s"	. $del .
				"TXOUT: %-69s" . "\n",
				$tx, self::txno__($txoutput, $no)
			);
		}
	}

	function out(TxPack $txp){
		if ($this->out){
			echo $txp->asString();
			echo "\n";
		}
	}
}

