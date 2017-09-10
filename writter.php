<?
require_once "txpack.php";

interface Writter{
	function in($tx, $txoutput, $no);
	function out(TxPack $txp);
}

