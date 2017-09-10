<?
error_reporting(E_ALL);

require_once "blockchainwalker.php";

require_once "writter/blackholewritter.php";
require_once "writter/commentwritter.php";
require_once "writter/keyvaluewritter.php";
require_once "writter/zusewritter.php";

//$writter = new BlackHoleWritter();
//$writter = new CommentWritter(true, true);
//$writter = new KeyValueWritter();
$writter = new ZuseWritter("127.0.0.1:2000");

$host = "192.168.0.3";

$bl = BlockchainWalker::BL0; // genesis block
$bl = "00000000009c516fac28019301e50b478f335e2fce14856f0b487f1cbb547da2"; // early block
$bl = "00000000000000000010c02d14b215b83f1466812101d52971116fe3419aaf73"; // 2017

$walker = new BlockchainWalker(
			new BitcoindRPC("http://$host:8332/", "niki", "123"),
			$writter,
			$bl
);

$walker->walk();

