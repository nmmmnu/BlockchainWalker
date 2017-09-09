<?
require_once "writter.php";

class KeyValueWritter implements Writter{
	protected function write_($key, $value){
		printf("%s\t%s\n", $key, $value);
	}

	private static function f__($value){
		return sprintf("%.8f", $value);
	}

	private static function av__(TxPack $txp){
		return $txp->address . ":" . self::f__($txp->value);
	}

	function in($tx, TxPack $txp){
		$input = $txp->txno2($tx);

		$key = "t:" . $input . ":-:" . $txp->txno();

		$this->write_($key,	self::av__($txp));

		$key = "a:" . $txp->address . ":-:" . $input;

		$this->write_($key,	self::f__($txp->value));
	}

	function out(TxPack $txp){
		$output = $txp->txno();

		$key = "t:" . $output . ":+:";

		$this->write_($key,	self::av__($txp));

		$key = "a:" . $txp->address . ":+:" . $output;

		$this->write_($key,	self::f__($txp->value));
	}
}

