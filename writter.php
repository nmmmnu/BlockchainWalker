<?
require_once "txpack.php";

interface Writter{
	function in($tx, TxPack $txp);
	function out(TxPack $txp);
}

