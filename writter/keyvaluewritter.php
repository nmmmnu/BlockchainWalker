<?
require_once "writter.php";

class KeyValueWritter implements Writter{
	protected function write_($key, $value){
		printf("%s\t%s\n", $key, $value);
	}

	function in($tx, $txoutput, $no){
		$key = "t:" . txno($tx) . ":i:" . txno($txoutput, $no);
		$val = "1";

		$this->write_($key,	$val);

		// --------------

		$key = "t:" . txno($txoutput, $no) . ":s";
		$val = $tx;

		$this->write_($key,	$val);
	}

	function out(TxPack $txp){
		$output = txno($txp->tx, $txp->no);
		$btc = format_value($txp->value);

		$key = "t:" . $output . ":o";
		$val = $txp->address . ":" . $btc;

		$this->write_($key,	$val);

		// --------------

		$key = "a:" . $txp->address . ":" . $output;
		$val = $btc;

		$this->write_($key,	$val);
	}
}

