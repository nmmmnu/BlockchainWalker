<?
require_once "txpack.php";

interface Writter{
	function bl($bl);
	function in($tx, $txoutput, $no);
	function out(TxPack $txp);
}

