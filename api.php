<?php
header('Content-type: text/json');
require_once("models/config.php");
$pub = mysql_real_escape_string($_GET["Pub"]);
$priv = mysql_real_escape_string($_GET["Priv"]);
$method = $_GET["Method"];
$user_id = 0; //Store user id. If a user does not give their api information they can still access public api functions.
$status = false;
$errors = array();
$results = array();
if($method == NULL)
{
	$errors[count($errors)] = "Method not defined";
}
if($pub != NULL && $priv != NULL)
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
	if($method = "CreateNewTrade")
	{
		$price = mysql_real_escape_string($_GET["Price"]);
		$amount = mysql_real_escape_string($_GET["Amount"]);
		$mid = mysql_real_escape_string($_GET["MarketId"]);
		if($price == NULL)
		$errors[count($errors)] = "Price not defined";
		if($amount == NULL)
		$errors[count($errors)] = "Amount not defined";
		if($mid == NULL)
		$errors[count($errors)] = "MarketId not defined";
	}
}

//Public methods that do not require API key go below!
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
			$amount = mysql_result($sql,$i,"Amount");
			$price = mysql_result($sql,$i,"Value");
			$total = $amount * $price;
			$buy_array[$i] = array("Quantity" => sprintf("%.8f",$amount),"PricePer" => sprintf("%.8f",$price),"Total" => sprintf("%.8f",$total));
		}
		$status = true;
		$results[count($results)] = array("MarketId" => $mid, "Name" => $name, "Acronymn" => $acronymn, "LastTradePrice" => $last_trade,"SellOrders" => $sell_array,"BuyOrders" => $buy_array);
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
		$results[count($results)] = array('Total' => $total,'Acronymn' => "BTC");
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