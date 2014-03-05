<?php
/**~2014 MilanEx.pw Developers. All Rights Reserved.~*
 *               http://www.milancoin.org/milanex/
 *Licensed Under the MIT License : http://www.opensource.org/licenses/mit-license.php
 *
 *WARRANTY INFORMATION:
 *THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *THE SOFTWARE. 
 ***************************************/
require_once ('system/csrfmagic/csrf-magic.php');
header('Content-type: text/json');
require_once("models/config.php");
error_reporting(E_ALL);
ini_set('display_errors', '1'); 
if(isset($_GET["Pub"]) && isset($_GET["Priv"]))
{
$pub = mysql_real_escape_string($_GET["Pub"]);
$priv = mysql_real_escape_string($_GET["Priv"]);
}
else
{
$pub = null;
$priv = null;
}
$method = $_GET["Method"];
$user_id = 0; //Store user id. If a user does not give their api information they can still access public api functions.
$status = false;
$errors = array();
$results = array();
if($method == NULL)
{
	$errors[count($errors)] = "Method not defined";
}

if(isset($pub) && isset($priv))
{
	$sql = @mysql_query("SELECT * FROM Api_Keys WHERE `Public_Key`='$pub' AND `Authentication_Key` ='$priv' ");
	$user_id = @mysql_result($sql,0,"User_ID") or 0;
	if($user_id == 0)
	{
		$errors[count($errors)] = "The provided API key was invalid";
	}
}

if($user_id != 0)//Allow only verified users to use the following api functions.
{
	if($method == "GetPrivTradeHistory")
	{
		$mid = @mysql_real_escape_string($_GET["MarketId"]) or null;
		$limit = @mysql_real_escape_string($_GET["Limit"]) or 10;
		if($mid == NULL)
		{
			$errors[count($errors)] = "MarketId not defined";
		}
		if($mid != NULL)
		{
			$trade_array = array();
			
			$sql = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='" . intval($mid) . "' AND (`Buyer` = '". intval($user_id) . "' OR `Seller`='" . intval($user_id) ."') ORDER BY Timestamp DESC LIMIT " . intval($limit));
			$num = @mysql_num_rows($sql);
			for($i = 0;$i<$num;$i++)
			{
			
				$amount = @mysql_result($sql,$i,"Quantity");
				$price = @mysql_result($sql,$i,"Price");
				$total = $amount * $price;
				$timestamp = @mysql_result($sql,$i,"Timestamp");
				$buyer = @mysql_result($sql,$i,"Buyer");
				$type = "";
				if($buyer == $user_id)
				{
					$type = "BUY";
				}
				else
				{
					$type = "SELL";
				}
				$status = true;
				$results[count($results)] = array("Quantity" => sprintf("%.8f",$amount),"PricePer" => sprintf("%.8f",$price),"Total" => sprintf("%.8f",$total),"Timestamp" => $timestamp,"Type" => $type);
			}
		}
		else
		{
			$status = false;
		}
	}
	if($method == "GetBalance")
	{
		$mid = mysql_real_escape_string($_GET["MarketId"]);
		if($mid == NULL)
		$errors[count($errors)] = "MarketId not defined";
		$b_sql = @mysql_query("SELECT SUM(Amount) as `Amount` FROM balances WHERE User_ID='" . intval($user_id) . "' AND `Wallet_ID` = '" . intval($mid) . "'");
		$b_amt = mysql_result($b_sql,0,"Amount");
		if($b_amt > 0.000000009)
		{
			$status = true;
			$results[count($results)] = array("MarketId" => $mid, "Balance" => sprintf("%0.8f",$b_amt));
		}
		
	}
	if($method == "CreateNewTrade")
	{
		if($mid == 104)
		{
			$errors[count($errors)] = "This Market Disabled.";
		}
		$price = @mysql_real_escape_string($_GET["Price"]);
		$amount = @mysql_real_escape_string($_GET["Amount"]);
		$type = @mysql_real_escape_string($_GET["Type"]);
		$mid = @mysql_real_escape_string($_GET["MarketId"]);
		if($price == NULL)
		$errors[count($errors)] = "Price not defined";
		if($amount == NULL)
		$errors[count($errors)] = "Amount not defined";
		if($mid == NULL)
		$errors[count($errors)] = "MarketId not defined";
		if($type == NULL)
		$errors[count($errors)] = "Type not defined";
		
		if(count($errors) == 0)
		{
			$sql = @mysql_query("SELECT * FROM Wallets WHERE `Id`='" . intval($mid) . "'");
			$w_name = mysql_result($sql,0,"Acronymn");
			$s_trade_to = "";
			$s_trade_from = "";
			$s_trade_fee = $amount * $price * 0.005;
			$status = true;
			if($type == "BUY")
			{
				$s_price = $amount * $price * 1.005;
				$s_trade_to = $w_name;
				$s_trade_from = "MLC";
			}
			elseif($type == "SELL")
			{
				$s_price = $amount;
				$s_trade_from = $w_name;
				$s_trade_to = "MLC";
			}
			if(TakeMoney($s_price,$user_id,$s_trade_from) == true)
			{
				$New_Trade = new Trade();
				$New_Trade->trade_to = $s_trade_to;
				$New_Trade->trade_from = $s_trade_from;
				$New_Trade->trade_amount = $amount;
				$New_Trade->trade_value = $price;
				$New_Trade->trade_owner = $user_id;
				$New_Trade->trade_type = $w_name;
				$New_Trade->trade_fees = $s_trade_fee;
				$New_Trade->trade_total = $s_price;
				$New_Trade->trade_type = $w_name;
				$New_Trade->UpdateTrade();
				$status = true;
				$results[count($results)] = array("Fee" => $s_trade_fee,"Total" => $s_price);
			}
			else
			{
				$errors[count($errors)] = "You must have atleast $s_price $s_trade_from";
			}
		}
	}
}

//Public methods that do not require API key go below!
if($method == "GetTradeHistory")
{
	$mid = @mysql_real_escape_string($_GET["MarketId"]) or null;
	$limit = @mysql_real_escape_string($_GET["Limit"]);
	if($mid == NULL)
	{
		$errors[count($errors)] = "MarketId not defined";
	}

	if($mid != NULL)
	{
		$trade_array = array();
		
		$sql = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='$mid' ORDER BY Timestamp DESC LIMIT " . intval($limit));
		$num = @mysql_num_rows($sql);
		for($i = 0;$i<$num;$i++)
		{
		
			$amount = @mysql_result($sql,$i,"Quantity");
			$price = @mysql_result($sql,$i,"Price");
			$total = $amount * $price;
			$timestamp = @mysql_result($sql,$i,"Timestamp");
		
		$results[count($results)] = array("Quantity" => sprintf("%.8f",$amount),"PricePer" => sprintf("%.8f",$price),"Total" => sprintf("%.8f",$total),"Timestamp" => $timestamp);
		}
		$status = true;
	}
	else
	{
		//$status = false;
	}
}
if($method == "GetMarketData")
{
	$mid = mysql_real_escape_string($_GET["MarketId"]);
	if($mid == NULL)
	{
		$errors[count($errors)] = "MarketId not defined";
	}
	else
	{
		$sql = @mysql_query("SELECT * FROM Wallets WHERE `Id`='$mid'");
		$name = @mysql_result($sql,0,"Name");
		$acronymn = @mysql_result($sql,0,"Acronymn");
		$sql = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='$mid' ORDER BY Timestamp DESC LIMIT 1");
		$last_trade = @mysql_result($sql,0,"Price");
		$sql = @mysql_query("SELECT * FROM trades WHERE `From`='$acronymn' AND `Type`='$acronymn' ORDER BY `Value` ASC limit 10");//Grab Sell Orders
		$num = @mysql_num_rows($sql);
		$sell_array = array();
		for($i = 0;$i<$num;$i++)
		{
			$amount = mysql_result($sql,$i,"Amount");
			$price = mysql_result($sql,$i,"Value");
			$total = $amount * $price;
			$sell_array[$i] = array("Quantity" => sprintf("%.8f",$amount),"PricePer" => sprintf("%.8f",$price),"Total" => sprintf("%.8f",$total));
		}
		$sql = @mysql_query("SELECT * FROM trades WHERE `To`='$acronymn' AND `Type`='$acronymn' ORDER BY `Value` ASC limit 10");//Grab Sell Orders
		$num = @mysql_num_rows($sql);
		$buy_array = array();
		for($i = 0;$i<$num;$i++)
		{
			$amount = @mysql_result($sql,$i,"Amount");
			$price = @mysql_result($sql,$i,"Value");
			$total = $amount * $price;
			$buy_array[$i] = array("Quantity" => sprintf("%.8f",$amount),"PricePer" => sprintf("%.8f",$price),"Total" => sprintf("%.8f",$total));
		}
		$status = true;
		$results[count($results)] = array("MarketId" => $mid, "Name" => $name, "Acronymn" => $acronymn, "LastTradePrice" => sprintf("%.8f",$last_trade),"SellOrders" => $sell_array,"BuyOrders" => $buy_array);
	}
}

if($method == "GetConversion")
{
	$mid = mysql_real_escape_string($_GET["MarketId"]);
	$amt = mysql_real_escape_string($_GET["Amount"]);
	$sql = @mysql_query("SELECT * FROM Wallets WHERE `Id`='$mid'");
	$acronymn = @mysql_result($sql,0,"Acronymn");
	if($mid == NULL)
	$errors[count($errors)] = "MarketId not defined";
	if($amt == NULL)
	$errors[count($errors)] = "Amount not defined";

	if($mid != NULL && $amt != NULL)
	{
		$sql = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='$mid' ORDER BY Timestamp DESC LIMIT 1");
		$last_trade = @mysql_result($sql,0,"Price");
		$total = sprintf("%.8f",$last_trade * $amt);
		$status = true;
		$results[count($results)] = array('Total' => $total,'Acronymn' => "MLC");
	}
}





//This makes everything where it prints out in a single json string in order for there to be no confusion on software that is interpreting the output.
if($status == false)
{
	echo json_encode(array('Status' => 'Failed','Errors' => $errors));
}
else
{
	echo json_encode(array('Status' => 'Success','Results' => $results));
}
?>
