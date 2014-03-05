<?php 
error_reporting(E_ALL);
require_once("models/config.php");
include("models/class.trade.php");
require_once ('system/csrfmagic/csrf-magic.php');
$id = mysql_real_escape_string(strip_tags((int)$_GET["market"]));
$s_id = 993;
if(!is_numeric($id)) {
	?>
		<meta http-equiv="refresh" content="0; URL=index.php?page=invalid_market">
	<?php
}
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
$name = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$fullname = mysql_real_escape_string(mysql_result($result, 0, "Name"));
$disabled = mysql_real_escape_string(mysql_result($result, 0, "disabled"));
$market_id = mysql_result($result, 0, "Market_Id");
$error = false;
if (isUserLoggedIn()) 
{ 
        $id2 = $loggedInUser->user_id;
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
	$getbal2 = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$id2' AND `Wallet_ID` = '$market_id'");
	$curbal2 = mysql_result($getbal2, 0, "Amount");
	if($curbal2 == null){
		$curbal2 = "0.00000000";
	}
}



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
$SQL2 = mysql_query("SELECT * FROM Wallets WHERE `Id`='$market_id'");
$Currency_1a = mysql_result($SQL2, 0, "Acronymn");
$Currency_1 = mysql_result($SQL2, 0, "Id");
if(isset($_POST["price2"])) {
	//Don't touch unless authorized!
	if ($_POST["price2"] > 0.000000009 && $_POST["Amount2"] > 0.000000009) {
		$postedToken = filter_input(INPUT_POST, 'token2');
		if(!empty($postedToken)) {
			if(isTokenValid($postedToken)) {
				$PricePer = mysql_real_escape_string($_POST["price2"]);
				$Amount = mysql_real_escape_string($_POST["Amount2"]);
				//$Total = $Amount  file_get_contents("http://www.milancoin.org/milanex/system/calculatefees2.php?P=" . $Amount);
				$Fees = $Amount * 0.005;
				$X = sprintf("%.8f",($Amount-$Fees)/$PricePer);
				$user_id = $loggedInUser->user_id; 
				if(TakeMoney($Amount,$user_id,$Currency_1) == true) {
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
				}else{
					$error = true;
					$errors[] = "You cannot afford that!<br/>";
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
function completecancel($ids,$id) {
	$cord = mysql_query("INSERT INTO Canceled_Trades SELECT * FROM trades WHERE `Id`='$ids'");
	$corq = mysql_query("DELETE FROM trades WHERE `Id`='$ids'");
	if (!$corq) {
	}else{
			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=trade&market='.$id.'">';
	}
}
/*the idea is to reload the page since completed orders don't update immediately on the trade page.*/
if(isset($_GET["trade"])) {
	if(isset($_GET["clickedcancel"])) {
		$clickedcancel = mysql_real_escape_string($_GET["clickedcancel"]);
		if($clickedcancel == "true") {
			$ids      = mysql_real_escape_string($_GET["trade"]);
			$tradesql = @mysql_query("SELECT * FROM trades WHERE `Id`='$ids'");
			$from     = @mysql_result($tradesql, 0, "From");
			$owner    = @mysql_result($tradesql, 0, "User_ID");
			$type     = @mysql_result($tradesql, 0, "Type");
			$o_fee    = @mysql_result($tradesql,0,"Fee");
			$Amount   = @mysql_result($tradesql, 0, "Amount");
			$Price    = @mysql_result($tradesql, 0, "Value");
			$sql      = @mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$from'");
			$from_id  = @mysql_result($sql,0,"Id");
			if($owner == $loggedInUser->user_id || isUserAdmin($id2)) {
				if($from != $type) {
					$Total = sprintf("%2.8f",$Amount * $Price);
					$Fees = ($Total + $o_fee) * 0.005;
					AddMoney($Total + $Fees,$owner,$from_id);
					if(isset($id)) {
						completecancel($ids,$id);
					}
				}else{
					if($ids <= $s_id) {
						$Fees = ($o_fee + $Amount) * 0.005;
						AddMoney($Amount + $Fees,$owner,$from_id);
						if(isset($id)) {
							completecancel($ids,$id);
						}
					}else{
						AddMoney($Amount,$owner,$from_id);
						if(isset($id)) {
							completecancel($ids,$id);
						}
					}
				}	
			}
		}
	}
}

if (isset($_GET["cancel"])) {
	/*this is a more secure method of canceling. double checks to make sure the user hasn't completed the trade.*/
    $ids      = mysql_real_escape_string($_GET["cancel"]);
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=trade&market='.$id.'&trade='.$ids.'&clickedcancel=true">';
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
				$error = true;
				$errors[] = "Invalid Token.";
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
<div class="tabber">
	<div class="tabbertab" title="Trade <?php echo $fullname; ?>">
		<div id="boxB">
			<div id="boxA">
				<div id="col1">
					<!-- Sell Form-->
					<?php if(isUserLoggedIn())
					{
					?>
					<div id="sellform">
						<center><h3 style="color:#000;">Sell: Avail. <?php echo $name; ?>: <span style="cursor:pointer; " id="secondaryBalance"><u><?php echo sprintf("%.8f",$curbal); ?></u></span></h3></center><br/>
						<form action="index.php?page=trade&market=<?php echo $id; ?>" method="POST" autocomplete="off" history="off" onsubmit="document.getElementById('#Sell').disabled = 1;"> 
							<input type="hidden" name="token1" value="<?php echo $token1;?>"/>
							<input class="fieldsmall" type="text" style="width:150px;" name="Amount" onKeyUp="calculateFees1(this)" id="Amount" placeholder="Amount(<?php echo $name; ?>)"/><br/>
							<input class="fieldsmall" type="text" style="width:150px;" name="price1" onKeyUp="calculateFees1(this)" id="price1" placeholder="Price(MLC)"/><br/>
							<input class="fieldsmall" type="text" style="width:150px;" onKeyUp="calculateFees4()" id="earn1"placeholder="Receive(MLC)" readonly /></br>
							<input class="miniblues" style="width:176px; height: 55px; padding: 5px 5px;" type="submit" name="Sell" value="Sell" id="Sell" onclick="this.disabled=true;this.value='Submitting trade...';this.form.submit();"/>
						</form>
					</div>
					<?php } ?>
					<!--Sell Order Book-->
					<div id="sellorders">
						<center><img height="80" src="../assets/img/ajax-loader.gif" alt="Loading..."></center>
					</div>
				</div>
				<div id="col2">
					<!--Buy Form-->
					<?php if (isUserLoggedIn()) { ?>
					<div id="buyform">
						<center><h3 style="color:#000;">Buy: Avail. MLC: <span style="cursor:pointer; " id="btcBalance"><u><?php echo sprintf("%.8f",$curbal2); ?></u></span></h3></center><br/>
						<form action="index.php?page=trade&market=<?php echo $id; ?>" method="POST" autocomplete="off" history="off" onsubmit="document.getElementById('#Buy').disabled = 1;">
							<input type="hidden" name="token2" value="<?php echo $token2;?>"/>
							<input class="fieldsmall" type="text" style="width:150px;" onKeyUp="calculateFees2()" name="Amount2" id="Amount2" placeholder="Amount(MLC)"/><br/>
							<input class="fieldsmall" type="text" style="width:150px;" id="price2" onKeyUp="calculateFees2()" onKeyUp="calculateFees2()" name="price2" placeholder="Price(MLC)"/><br/>
							<input class="fieldsmall" type="text" style="width:150px;" onKeyUp="calculateFees3()" id="fee2" placeholder="Receive (<?php echo $name;?>)" readonly /><br/>
							<input class="miniblues" style="width:176px; height: 55px; padding: 5px 5px;" type="submit" name="Buy" id="Buy" value="Buy" onclick="this.disabled=true;this.value='Submitting trade...';this.form.submit();"/>
						</form>
					</div>
					<?php  } ?>
					
					<!--Buy Order Book-->
					<div id="buyorders">
						<center><img height="80" src="../assets/img/ajax-loader.gif" alt="Loading..."></center>
					</div>
				</div>
			</div>
		</div>
		
		<?php if (isUserLoggedIn()) { ?>
		<div id="user-orders">
		<?php include("open_orders.php"); ?>
		</div>
		<div id="user-orders">
			<div id="personal-history">
					<?php include("trade_history.php");?>
			</div>
		</div>
		<?php }  ?>
	</div>
	
	<div class="tabbertab" id="charttab" title="View Charts">
		<div id="chart">
			<button class="toggle" id="chartshow" onclick="this.disabled=true;this.value='Fetching Trade Data..';">Load Data</button>
			<canvas id='canvas1' width='0px' height='0px'></canvas>
		</div>
	</div>
	<div class="tabbertab" title="Market History">
		<div id="user-orders">
			<div id="all-history">
					<?php include("trade_hist_all.php");?>
			</div>
		</div>
	</div>
</div>

