<?php 
error_reporting(E_ALL);
require_once("models/config.php");
include("models/class.trade.php");
require_once ('system/csrfmagic/csrf-magic.php');
$id = mysql_real_escape_string($_GET["market"]);
$id2 = $loggedInUser->user_id;
$s_id = 993;
//this code is buggy and need fixed
//error message:
//Notice: Undefined variable: Trade_Data_C in /home/wwwroot/openex.pw/pages/trade.php on line 31

$time = time() - 86400;
$trade_sql = mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='$id' AND Timestamp > $time ORDER BY `Timestamp` ASC LIMIT 100");
$Last_Timestamp = "";
$Last_Price = "";
$Trades_Hour = "";
$Trade_Data = "";
while ($row = mysql_fetch_assoc($trade_sql)) {
	$Timestamp = $row["Timestamp"];
	$T3 = date("a",$Timestamp);
	$T2 = date("h",$Timestamp);
	if($T2 != $Last_Timestamp)
	{
		$Trade_Data=$Trade_Data_C;
		$Trades_Hour .= "$T2,";
		$Last_Price = $row["Price"];
		$Trade_Data_C .= $row["Price"] . ",";
	}
	else
	{
		$Last_Price = ($Last_Price + $row["Price"])/2;
		$Trade_Data_C = $Trade_Data . $Last_Price . ",";
	}
	$Last_Timestamp = $T2;
}
$Trade_Data=$Trade_Data_C;
$labels = $Trades_Hour;
$trade_data = $Trade_Data;




if (isUserLoggedIn()) 
{ 
$errors = array();
$error = false;
$successes = array();
$success = false;
$ip = getIP();
$token1 = getToken($id2,$ip);
$token2 = getToken($id2,$ip);
$getbal = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$id2' AND `Wallet_ID` = '$id'");
$curbal = mysql_result($getbal, 0, "Amount");
if($curbal == null){
	$curbal = "0.00000000";
}
$getbtc = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$id2' AND `Wallet_ID` = '1'");
$curbtc = mysql_result($getbtc, 0, "Amount");
if($curbtc == null){
	$curbtc = "0.00000000";
}
}

$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
$name = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$fullname = mysql_real_escape_string(mysql_result($result, 0, "Name"));
$disabled = mysql_real_escape_string(mysql_result($result, 0, "disabled"));

if($id == 1) {
?>
<meta http-equiv="refresh" content="0; URL=index.php?page=account">
<?php
	die();
	
}
if($disabled == 1) {
?>
<meta http-equiv="refresh" content="0; URL=index.php?page=invalid_market">
<?php
}
if($name == NULL) {
?>

<?php
	die();
}
$market_id = mysql_result($result, 0, "Market_Id");
$SQL2 = mysql_query("SELECT * FROM Wallets WHERE `Id`='$market_id'");
$Currency_1a = mysql_result($SQL2, 0, "Acronymn");
$Currency_1 = mysql_result($SQL2, 0, "Id");
if(isset($_POST["price2"]))
{
//Don't touch unless authorized!
	if ($_POST["price2"] > 0.000000009 && $_POST["Amount2"] > 0.000000009) 
	{
		$postedToken = filter_input(INPUT_POST, 'token2');
		if(!empty($postedToken)){
			if(isTokenValid($postedToken)){
				$PricePer = mysql_real_escape_string($_POST["price2"]);
				$Amount = mysql_real_escape_string($_POST["Amount2"]);
				//$Total = $Amount  file_get_contents("http://openex.pw/openex.pw/system/calculatefees2.php?P=" . $Amount);
				$Fees = $Amount * 0.005;
				$X = sprintf("%.8f",($Amount-$Fees)/$PricePer);
				$user_id = $loggedInUser->user_id; 
				
				if(TakeMoney($Amount,$user_id,$Currency_1) == true)
				{
					$New_Trade = new Trade();
					$New_Trade->trade_to = $name;
					$New_Trade->trade_from = $Currency_1a;
					$New_Trade->trade_amount = $X;
					$New_Trade->trade_value = $PricePer;
					$New_Trade->trade_owner = $user_id;
					$New_Trade->trade_type = $name;
					$New_Trade->trade_fees = $Fees;
					$New_Trade->trade_total = $Amount;
					$New_Trade->trade_type = $name;
					$New_Trade->standard = $X;
					//$New_Trade->GetEquivalentTrade();
					//$New_Trade->ExecuteTrade();
					$New_Trade->UpdateTrade();
					echo '<meta http-equiv="refresh" content="0; URL=index.php?page=trade&market='.$id.'">';
				}
				else
				{
					$error = true;
					$errors[] = "You cannot afford that!<br/>";
				}
			}else{
				echo "invalid token";
				die();
			}
		}

	}
	else
	{
		$error = true;
		$errors[] = "Please fill in all the forms!!<br/>";
	}
}
if (isset($_GET["cancel"])) {

	//Don't touch unless authorized!
    $ids      = mysql_real_escape_string($_GET["cancel"]);
    $tradesql = @mysql_query("SELECT * FROM trades WHERE `Id`='$ids'");
    $from     = @mysql_result($tradesql, 0, "From");
    $owner    = @mysql_result($tradesql, 0, "User_ID");
	$type     = @mysql_result($tradesql, 0, "Type");
	$o_fee = @mysql_result($tradesql,0,"Fee");
	$Amount = @mysql_result($tradesql, 0, "Amount");
	$Price = @mysql_result($tradesql, 0, "Value");
	$sql = @mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$from'");
	$from_id = @mysql_result($sql,0,"Id");
	if($owner == $loggedInUser->user_id || $loggedInUser->user_id == 2)
	{
	if($from != $type)
	{
		$Total = sprintf("%2.8f",$Amount * $Price);
		//echo $Total;
		$Fees = ($Total + $o_fee) * 0.005;
		AddMoney($Total + $Fees,$owner,$from_id);
	}
	else
	{
		if($ids <= $s_id)
		{
			$Fees = ($o_fee + $Amount) * 0.005;
			AddMoney($Amount + $Fees,$owner,$from_id);
		}
		else
		{
			AddMoney($Amount,$owner,$from_id);
		}
	}
	//a simple way to refresh the page was added but making sure the query completed first.
	$cord = mysql_query("INSERT INTO Canceled_Trades SELECT * FROM trades WHERE `Id`='$ids'");
	$corq = mysql_query("DELETE FROM trades WHERE `Id`='$ids'");
	if (!$corq ) {
	}else{
		echo '<meta http-equiv="refresh" content="0; URL=index.php?page=trade&market='.$id.'">';
	}
    }
}
//--------------------------------------
if(isset($_POST["Amount"]))
{
//Don't touch unless authorized!
	if ($_POST["price1"] > 0.000000009 && $_POST["Amount"] > 0.000000009) 
	{
		$postedToken = filter_input(INPUT_POST, 'token1');
		if(!empty($postedToken)){
			if(isTokenValid($postedToken)){
				$PricePer = mysql_real_escape_string($_POST["price1"]);
				$Amount = mysql_real_escape_string($_POST["Amount"]);
				$user_id = $loggedInUser->user_id; 
				if(TakeMoney($Amount,$user_id,$id) == true)
				{	
					$New_Trade = new Trade();
					$New_Trade->trade_to = $Currency_1a;
					$New_Trade->trade_from = $name;
					$New_Trade->trade_amount = $Amount;
					$New_Trade->trade_value = $PricePer;
					$New_Trade->trade_owner = $user_id;
					$New_Trade->trade_type = $name;
					$New_Trade->trade_fees = 0;
					$New_Trade->trade_total = $Amount;
					$New_Trade->trade_type = $name;
					$New_Trade->standard = $Amount;
					//$New_Trade->GetEquivalentTrade();
					//$New_Trade->ExecuteTrade();
					$New_Trade->UpdateTrade();
					echo '<meta http-equiv="refresh" content="0; URL=index.php?page=trade&market='.$id.'">';
				}
				else
				{
					$error = true;
					$errors[] = "You cannot afford that!</br>";
				}
				
			}else{
				echo "invalid token";
				die();
			}
		}	
	}else{
		$error = true;
		$errors[] = "Please fill in all the forms!!<br/>";
		
	}
}

if($error == false) {
	
}else{
	errorBlock($errors);
}
?>
<center><h1>Trade <?php echo $fullname; ?></h1></center>
<hr class="five" />
<a id="refresh" href="index.php?page=trade&market=<?php echo $id; ?>">Click Here to Refresh</a>
<hr class="five" />
<div id="boxB">
	<div id="boxA">
		<div id="col1">
			<!-- Sell Form-->
			<?php if (isUserLoggedIn()) { ?>
			<script>
			function fillSellAmount() {
				$("#Amount").val(<?php echo sprintf("%.8f",$curbal); ?>);
			}
			</script>
			<div class="top">
			<center>Sell <?php echo $name; ?></center>
			</div>
			<div id="sellform" class="color3">
				<center><h3>Available(<?php echo $name; ?>): <span onclick="fillSellAmount();" style="cursor:pointer; "><u><?php echo sprintf("%.8f",$curbal); ?></u></span></h3></center><br/>
				<form action="index.php?page=trade&market=<?php echo $id; ?>" method="POST" autocomplete="off" history="off" onsubmit="document.getElementById('#Sell').disabled = 1;"> 
					<input type="hidden" name="token1" value="<?php echo $token1;?>"/>
					<input class="fieldsmall" type="text" style="width:150px;" name="Amount" onKeyUp="calculateFees1(this)" id="Amount" placeholder="Amount(<?php echo $name; ?>)"/><br/>
					<input class="fieldsmall" type="text" style="width:150px;" name="price1" onKeyUp="calculateFees1(this)" id="price1" placeholder="Price(BTC)"/><br/>
					<input class="fieldsmall" type="text" style="width:150px;" onKeyUp="calculateFees4()" id="earn1"placeholder="Receive(BTC)" readonly /></br>
					<input class="miniblues" style="width:150px; height: 25px;" type="submit" name="Sell" value="Sell" id="Sell" onclick="this.disabled=true;this.value='Submitting trade...';this.form.submit();"/>
				</form>
			</div>
			<?php } ?>
			<!--Sell Order Book-->
			<div id="sellorders">

			</div>
		</div>
		<div id="col2">
			<!--Buy Form-->
			<?php if (isUserLoggedIn()) { ?>
			<script>
			function fillBuyAmount() {
				$("#Amount2").val(<?php echo sprintf("%.8f",$curbtc); ?>);
			}
			</script>
			<div class="top">
			<center>Buy <?php echo $name; ?></center>
			</div>
			<div id="buyform" class="color3">
				<center><h3>Available(BTC): <span onclick="fillBuyAmount();" style="cursor:pointer; "><u><?php echo sprintf("%.8f",$curbtc); ?></u></span></h3></center><br/>
				<form action="index.php?page=trade&market=<?php echo $id; ?>" method="POST" autocomplete="off" history="off" onsubmit="document.getElementById('#Buy').disabled = 1;">
					<input type="hidden" name="token2" value="<?php echo $token2;?>"/>
					<input class="fieldsmall" type="text" style="width:150px;" onKeyUp="calculateFees2()" name="Amount2" id="Amount2" placeholder="Amount(BTC)"/><br/>
					<input class="fieldsmall" type="text" style="width:150px;" id="price2" onKeyUp="calculateFees2()" onKeyUp="calculateFees2()" name="price2" placeholder="Price(BTC)"/><br/>
					<input class="fieldsmall" type="text" style="width:150px;" onKeyUp="calculateFees3()" id="fee2" placeholder="Receive (<?php echo $name;?>)" readonly /><br/>
					<input class="miniblues" style="width:150px; height: 25px;" type="submit" name="Buy" id="Buy" value="Buy" onclick="this.disabled=true;this.value='Submitting trade...';this.form.submit();"/>
				</form>
			</div>
			<?php  } ?>
			<!--Buy Order Book-->
			<div id="buyorders">

		</div>
		</div>
	</div>
</div>
<?php if (isUserLoggedIn()) { ?>
<div id="user-orders">
<?php include("open_orders.php"); ?>
</div>
<div id="user-orders">
			<?php include("trade_history.php");?>
</div>
<?php }  ?>
<div id="user-orders">
			<?php include("trade_hist_all.php");?>
</div>



