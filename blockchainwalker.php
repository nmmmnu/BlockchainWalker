<?
require_once "bitcoindrpc.php";
require_once "writter.php";

class BlockchainWalker{
	private $rpc;
	private $writter;
	private $currentBlock;

	function __construct(BitcoindRPC $rpc, Writter $writter, $startBlock = BitcoindRPC::BL0){
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

		while(true){
			echo "BL:" . $this->currentBlock . "\n";

			$this->currentBlock = $this->bl_($this->currentBlock);

			if ($count++ > 100)
				exit;
		}
	}

	private function bl_($bl){
		$data = $this->validate__($this->rpc->getBl($bl));

		foreach($data["tx"] as & $tx){
			$this->tx_($tx);
		}

		return $data["nextblockhash"];
	}

	private function tx_(& $tx){
		$data = $this->validate__($this->rpc->getTx($tx));

		foreach($data["vin"] as & $vin)
			$this->decodeTxIn_($tx, $vin);

		foreach($data["vout"] as & $vout)
			$this->decodeTxOut_($tx, $vout);
	}

	private function findTxOutput_($tx, $no){
		$data = $this->validate__($this->rpc->getTx($tx));

		// First, lets check directly
		{
			$direct_vout = $data["vout"][$no];

			if ($direct_vout["n"] == $no){
				//echo "Direct hit ;)\n";

				return $this->findTxOutput2_($tx, $no, $direct_vout);
			}

			unset($direct_vout);
		}

		// ...then, because we can not be sure this is sorted,
		// lets try linear search
		foreach($data["vout"] as & $vout)
			if ($vout["n"] == $no)
				return $this->findTxOutput2_($tx, $no, $vout);

		self::error__("Can not find output $tx : $no");
	}

	private function findTxOutput2_($tx, $no, & $vout){
		if ($this->decodeTxVOut_($tx, $vout) == false)
			return false;

		return new TxPack(
			$tx,
			$no,
			$vout["scriptPubKey"]["addresses"][0],
			$vout["value"]
		);
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

		$prev_output	= $this->findTxOutput_($txoutput, $no);

		if ($prev_output === false){
			// probably segwit
			return;
		}

		$this->writter->in($tx, $prev_output);
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

		default:
			print_r($this->rpc->getTx($tx));
			self::error__("No idea what this is...");
		}
	}
}
