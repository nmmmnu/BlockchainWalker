<?
require_once "bitcoindrpc.php";

$rpc = new BitcoindRPC("http://192.168.0.3:8332/", "niki", "123");

var_dump($rpc->info());

//print_r($rpc->lastBl());

$bl = $rpc->getBl0();
//print_r($bl);

//$bl = $rpc->getBl($bl["nextblockhash"]);

$txhash = $bl["tx"][0];
echo $txhash;
$tx = $rpc->getTx($txhash);

//$tx = $rpc->getTx("d5e53b84d6fdb7db91b946061bf692a999a89327fd6238e79bd831d154c51fd3");

print_r($tx);

print_r($rpc->getTx0());

