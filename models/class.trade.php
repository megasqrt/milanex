<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1'); 
class Trade
{
	public $trade_value;
	public $trade_amount;
	public $trade_type;
	public $trade_id = 0;
	public $trade_to;
	public $trade_from;
	public $trade_fees;
	public $trade_owner;
	public $trade_total;
	public $etrade_owner;
	public $etrade_type;
	public $etrade_id = 0;
	public $etrade_value;
	public $etrade_amount;//
	public $standard;
	public $standarde;
	public $sold;
	public function UpdateTrade()
	{
		$id = $this->trade_id;
		$amount = sprintf("%.8f",$this->trade_amount);
		if($id == 0 && $amount >= 0.00000001)
		{
			$trade_to = $this->trade_to;
			$trade_from = $this->trade_from;
			$trade_value = $this->trade_value;
			$trade_owner = $this->trade_owner;
			$trade_type = $this->trade_type;
			$trade_fees = $this->trade_fees;
			$trade_total = $this->trade_total;
			mysql_query("INSERT INTO trades (`To`,`From`,`Amount`,`Value`,`User_ID`,`Type`,`Fee`,`Total`)VALUES ('$trade_to','$trade_from','$amount','$trade_value','$trade_owner','$trade_type','$trade_fees','$trade_total');");
		}
		else
		{
			if($this->trade_amount <= 0.00000001)
			{

				mysql_query("DELETE FROM trades WHERE `Id`='$id'");
			}
			else
			{
				mysql_query("UPDATE trades SET `Amount`='$amount' WHERE `Id`='$id'");
			}
		}

	}
	public function UpdateETrade()
	{
		$id = $this->etrade_id;
		if($id != 0)
		{
		$amount = sprintf("%.8f",$this->etrade_amount);
		if($this->etrade_amount <= 0.00000001)
		{		
			mysql_query("DELETE FROM trades WHERE `Id`='$id'");
		}
		else
		{
			mysql_query("UPDATE trades SET `Amount`='$amount' WHERE `Id`='$id'");
		}
		}
	}
	function Trade($id)
	{
		$tradesql = mysql_query("SELECT * FROM trades WHERE `Id`='$id'");
		$this->trade_value = mysql_result($tradesql,0,"Value");
		$this->trade_amount = mysql_result($tradesql,0,"Amount");
		$this->standard = mysql_result($tradesql,0,"Amount");
		$this->trade_id = $id;
		$this->trade_from = mysql_result($tradesql,0,"From");
		$this->trade_to = mysql_result($tradesql,0,"To");
		$this->trade_fees = mysql_result($tradesql,0,"Fee");
		$this->trade_owner = mysql_result($tradesql,0,"User_ID");
		$temp = mysql_result($tradesql,0,"Type");
		$this->trade_type = $temp;
	}
	public function GetEquivalentTrade()
	{
		if($this->trade_from == $this->trade_type)
		{
			echo "Yup";
			$from = $this->trade_from;
			$value = $this->trade_value;
			$type = $this->trade_type;
			$tradesql2 = mysql_query("SELECT * FROM trades WHERE `To` = '$from' AND `Value` >= '$value' AND `Type`='$type' ORDER BY `Value` ASC");
			$this->etrade_id = mysql_result($tradesql2,0,"Id");
			$this->etrade_value = mysql_result($tradesql2,0,"Value");
			$this->etrade_amount = mysql_result($tradesql2,0,"Amount");
			$this->standarde = mysql_result($tradesql2,0,"Amount");
			$this->etrade_owner = mysql_result($tradesql2,0,"User_ID");
			$this->etrade_to = mysql_result($tradesql2,0,"To");
			$this->etrade_from = mysql_result($tradesql2,0,"From");
			$temp = mysql_result($tradesql2,0,"Type");
			$this->etrade_type = $temp;
			$this->etrade_fees = mysql_result($tradesql2,0,"Fee");
			//Sell
		}
		else
		{
			//Buy
			echo "Nope";
			echo "<br/>" . $this->trade_value;
			$from = $this->trade_from;
			$value = $this->trade_value;
			$type = $this->trade_type;
			$tradesql2 = mysql_query("SELECT * FROM trades WHERE `To` = '$from' AND `Value` <= '$value' AND `Type`='$type'");
			$this->etrade_id = mysql_result($tradesql2,0,"Id");
			$this->etrade_value = mysql_result($tradesql2,0,"Value");

			$this->etrade_amount = mysql_result($tradesql2,0,"Amount");
			$this->standarde = mysql_result($tradesql2,0,"Amount");
			$this->etrade_owner = mysql_result($tradesql2,0,"User_ID");
			$this->etrade_to = mysql_result($tradesql2,0,"To");
			$this->etrade_from = mysql_result($tradesql2,0,"From");
			$this->etrade_fees = mysql_result($tradesql2,0,"Fee");
			$temp = mysql_result($tradesql2,0,"Type");

			$this->etrade_type = $temp;
		}

	}
	public function ExecuteTrade()
	{
		$buyer = "";
		$seller = "";
		$buyfee = 0;
		$s_id = 993;
		$fee = 0;
		$sellcoin = "";
		if($this->etrade_id != NULL)
		{
			if($this->trade_from == "MLC")
			{
				$buyer = $this->trade_owner;
				$seller = $this->etrade_owner;
				$buyfee = $this->trade_fees;
				$sellcoin = $this->etrade_type;
			}
			else
			{
				$buyer = $this->etrade_owner;
				$seller = $this->trade_owner;
				$buyfee = $this->etrade_fees;
				$sellcoin = $this->trade_type;
				$buyorder = 1;
			}
			$price = 0;
			$price2 = 0;
			if($this->trade_from == $this->trade_type) //Sell Order ---------------------------------------------------
			{
				$price2 = $this->etrade_value;
				$price = $this->trade_value;
				$difference = $this->trade_amount - $this->etrade_amount;
				if($difference < 0.000000009)
				{
					$newamount = 0 - $difference;
					$this->sold = $this->etrade_amount - $newamount;
				}
				if($difference > 0.000000009)
				{
					$this->sold = $this->etrade_amount;
				}
				if($difference == 0)
				{
					$this->sold = $this->etrade_amount;
				}
			}
			else //Buy Order ---------------------------------------------------
			{

				$price = $this->etrade_value;
				$price2 = $this->trade_value;
				$difference = $this->etrade_amount - $this->trade_amount;
					if($difference < 0.000000009)
					{
						$newamount = 0 - $difference;
						$this->sold = $this->trade_amount - $newamount;

					}
					if($difference > 0)
					{
						$this->sold = $this->trade_amount;
					}
					if($difference == 0)
					{
						$this->sold = $this->trade_amount;
					}
			}
                                $type = $this->trade_type;
                                $sql_112 = mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$type'");
                                $type_id = mysql_result($sql_112,0,"Id");
                                $feecost = mysql_result($sql_112,0,"Fee");
                                $fees = sprintf("%.6f",($this->sold * $price) * $feecost);
                                if ($fees < 0.000001){
                                        $fees=0;
                                        $feecost=0;
                                }else{
                                        AddMoney($fees * 2,-12,"MLC");
                                }
                               	if($price2 > $price)
				{
					$refund = (($this->sold * $price2) + $buyfee) - ($this->sold * $price * (1 + $feecost));
					$refund2 = ($refund) * (1-$feecost);
					AddMoney($refund2,$buyer,"MLC");
					echo "Refunded $refund2";
				}
                                AddMoney(($this->sold * $price) - $fees,$seller,"MLC");
				AddMoney($this->sold,$buyer,$sellcoin);
				$amtsr = ($this->sold * $price) - $fees;

				$quantity = $this->sold;
				$time = time();
				@mysql_query("INSERT INTO Trade_History (`Market_Id`,`Price`,`Quantity`,`Timestamp`,`Buyer`,`Seller`,`S_Receive`,`B_Receive`)VALUES ('$type_id','$price','$quantity','$time','$buyer','$seller','$amtsr','$quantity');");
				if(($this->etrade_amount - $this->sold) > 0.000000009)
					$this->etrade_amount = $this->etrade_amount - $this->sold;
				else
					$this->etrade_amount = 0;
				if(($this->trade_amount - $this->sold) > 0.000000009)
					$this->trade_amount = $this->trade_amount - $this->sold;
				else
					$this->trade_amount = 0;
		}
			$this->UpdateTrade();
			$this->UpdateETrade();
	}
}
?>
