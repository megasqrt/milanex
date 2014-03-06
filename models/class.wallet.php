<?php
require_once("funcs.general.php");
class Wallet
{
	public $ip;
	public $port;
	public $username;
	public $password;
	public $Client;
	public $Wallet_Id;
	function Wallet($Wallet_Id)
	{
		$wallet_sql = mysql_query("SELECT * FROM Wallets WHERE `Id`='$Wallet_Id'");
		$this->ip = mysql_result($wallet_sql,0,"Wallet_IP");
		$this->username = mysql_result($wallet_sql,0,"Wallet_Username");
		$this->password = mysql_result($wallet_sql,0,"Wallet_Password");
		$this->Wallet_Id = $Wallet_Id;
		$this->port = mysql_result($wallet_sql,0,"Wallet_Port");
		$this->Client = new jsonRPCClient((($this->port == 443)?"https":"http").'://' . $this->username . ':' .$this->password . '@' . $this->ip . ':' . $this->port);
	}
	public function GetDepositAddress($account)
	{
		return $this->Client->getaccountaddress($account);
	}
	public function Withdraw($address,$total,$user,$coin)
	{
		$address2 = mysql_real_escape_string($address);
		$total = mysql_real_escape_string($total);
		$user = mysql_real_escape_string($user);
		$time = mysql_real_escape_string(time());
		$coin2 = mysql_real_escape_string($coin);
		//$fee = $this->GetTxFee();
		mysql_query("INSERT INTO Withdraw_History (`Timestamp`,`User`,`Amount`,`Address`,`Coin`) VALUES ('$time','$user','$total','$address2','$coin2');");
		//echo("INSERT INTO Withdraw_History (`Timestamp`,`User`,`Amount`,`Address`) VALUES ('$time','$user','$total','$address2');");
		if ($total > 1000000) {
			return $this->Client->sendtoaddress($address, round($total));
		}else{
			return $this->Client->sendtoaddress($address, (double)sprintf("%.8f", $total));
		}
		
	}
	public function GetTxFee()
	{
		$info = $this->Client->getinfo();
		return $info["paytxfee"];
	}
	public function GetTransactions()
	{
		return $this->Client->listtransactions("*", 100);
	}
	public function GetTransactionsDeep()
	{
		return $this->Client->listtransactions("*", 1000);
	}
	public function GetTransactionsDeeper()
	{
		return $this->Client->listtransactions("*", 100000);
	}
	public function GetTransaction($id)
	{
		return $this->Client->gettransaction($id);
	}

	public function GetInfo($id,$pw,$usr,$requestkey,$id_check)
	{	
		$validkey = md5(hash('sha512', $id_check.$id.$usr.$pw));
		if($requestkey != $validkey){
			die("insufficient credentials");
		}else{
			if(isUserAdmin($id_check)) {
				return $this->Client->getinfo();
			}else{
				die("insufficient credentials");
			}
		}
	}
	
	public function GetStats($id,$pw,$usr)
	{
		/*
		$prepare = $this->Client->getinfo();
		
		foreach($prepare as $key => $value) {
			$info["hash"]  = $value["hashrate"];
			$info["diff"]  = $value["difficulty"];
			$info["block"] = $value["blocks"];
			return $info;
		}
		*/
	}
	
	public function ValidateAddress($address)
	{
		return $this->Client->validateaddress($address);
	}
}
?>
