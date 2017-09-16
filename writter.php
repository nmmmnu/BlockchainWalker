<?
require_once "txpack.php";

interface Writter{
	function bl($bl, & $data);
	function in($tx, $txoutput, $no);
	function out(TxPack $txp);
}

