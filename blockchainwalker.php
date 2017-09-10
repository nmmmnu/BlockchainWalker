<?
require_once "bitcoindrpc.php";
require_once "writter.php";

class BlockchainWalker{
	const BL0 = BitcoindRPC::BL0;

	private $rpc;
	private $writter;
	private $currentBlock;

	function __construct(BitcoindRPC $rpc, Writter $writter, $startBlock = BL0){
		$this->rpc		= $rpc;
		$this->writter		= $writter;
		$this->currentBlock	= $startBlock;
	}

	private $mt = 0;
	private function microtime__($message){
		list($usec, $sec) = explode(" ", microtime());
		$now = (float)$usec + (float)$sec;

		if($this->mt)
			$diff = $now - $this->mt;
		else
			$diff = 0;

		printf("MICROTIME: %.4f | %s\n", $diff, $message);

		$this->mt = $now;
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

		while(true){
			$this->currentBlock = $this->bl_($this->currentBlock);

		//	if ($count++ > 100)
		//		exit;
		}
	}

	private function bl_($bl){
		$this->microtime__("BL " . $bl);
		$data = $this->validate__($this->rpc->getBl($bl));

		$this->microtime__("TX'es " . count($data["tx"]));
		foreach($data["tx"] as $tx){
			$this->tx_($tx);
		}

		return $data["nextblockhash"];
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

		$address	= $vout["scriptPubKey"]["addresses"][0];

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

