<?
require_once "bitcoindrpc.php";
require_once "writter.php";

class BlockchainWalker{
	const BL0 = BitcoindRPC::BL0;

	private $rpc;
	private $writter;
	private $currentBlock;

	function __construct(BitcoindRPC $rpc, Writter $writter, $startBlock = self::BL0){
		$this->rpc		= $rpc;
		$this->writter		= $writter;
		$this->currentBlock	= $startBlock;
	}

	private static function error__($message){
		echo "ERROR: $message\n";
		exit;
	}

	private function validate__($data){
		if ($data === false)
			self::error__("Invalid data for block " . $this->currentBlock);

		return $data;
	}

	function walk(){
		$count = 0;

		while($this->currentBlock){
			$this->currentBlock = $this->bl_($this->currentBlock);
		}

		echo "No more blocks. Finished!\n";
	}

	private function bl_($bl){
		$data = $this->validate__($this->rpc->getBl($bl));

		$this->writter->bl($bl, $data);

		foreach($data["tx"] as $tx){
			$this->tx_($tx);
		}

		if (isset($data["nextblockhash"]))
			return $data["nextblockhash"];
		else
			return false;
	}

	private function tx_($tx){
		$data = $this->validate__($this->rpc->getTx($tx));

		foreach($data["vin"] as & $vin){
			$this->decodeTxIn_($tx, $vin);
		}

		foreach($data["vout"] as & $vout){
			$this->decodeTxOut_($tx, $vout);
		}
	}

	private function decodeTxIn_($tx, & $vin){
		if (isset($vin["coinbase"])){
			// Newly Generated Coins
			// Skip
			return;
		}

		if (! isset($vin["txid"])){
			print_r($this->rpc->getTx($tx));
			self::error__("No idea what this is...");
			exit;
		}

		$txoutput	= $vin["txid"];
		$no		= $vin["vout"];

		$this->writter->in($tx, $txoutput, $no);
	}

	private function decodeTxOut_($tx, & $vout){
		$value		= $vout["value"];
		$no		= $vout["n"];

		if ($this->decodeTxVOut_($tx, $vout) == false)
			return;

		if (isset($vout["scriptPubKey"]["addresses"][0])){
			$address	= $vout["scriptPubKey"]["addresses"][0];
		}else{
			// strange case with invalid address, that can not be spent.
			// 71bbaef28e09d8d6fadd41f053db7768dbb5fa4570f06b961dfc29db3dc00b1d
			// https://github.com/MetacoSA/NBitcoin/issues/47

			$address	= "_invalid_address_";
		}

		$output		= new TxPack($tx, $no, $address, $value);

		$this->writter->out($output);
	}

	private function decodeTxVOut_($tx, & $vout){
		switch($vout["scriptPubKey"]["type"]){
		case "nulldata":
			// Unable to decode output address
			// Skip over
			// 70369c2c68d4d942fe3180c4e2a0b3d7add7b7b2cee6a27c09737482fa3b3ac2
			return false;

		case "nonstandard":
			// Skip over
			// e411dbebd2f7d64dafeef9b14b5c59ec60c36779d43f850e5e347abee1e1a455
			return false;

		case "witness_v0_keyhash":
		case "witness_v0_scripthash":
			// segwit
			// Skip over
			// 54096590c435012962ac5cbc1a73deefb824c648c0fd8c738c6f12988d13e9f4
			return false;

		case "scripthash":
		case "pubkeyhash":
		case "pubkey":
			// normal address
			return true;

		case "multisig":
			// Multi Signature
			// We will wrongly assume that all funds go to first address
			// 60a20bd93aa49ab4b28d514ec10b06e1829ce6818ec06cd3aabd013ebcdc4bb1
			return true;

		default:
			print_r($this->rpc->getTx($tx));
			self::error__("No idea what this is...");
		}
	}
}

