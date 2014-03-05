<?php
require_once("models/config.php");
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}
?>
<h1>Pending Withdrawals</h1>
<div id="page" style="width: 95%; height: 300px; overflow: hidden; overflow-y: scroll; background: #005798; border: 1px solid #000;">
<table id="page" style="width: 100%;">
<tr>
	<th style="width: 10%">User</td>
	<th style="width: 15%;">Amount</th>
	<th style="width: 10%;">Coin</th>
	<th style="width: 40%;">Recipient</th>	
	<th style="width: 25%;">Options</td>
</tr>
<?php
$getreq = mysql_query("SELECT * FROM Withdraw_Requests ORDER BY (id) ASC");
if(mysql_num_rows($getreq) > 0) {
while ($row = mysql_fetch_assoc($getreq)) {
		if($row["Confirmed"] == 1 || $row["Confirmed"] == -1 || $id == 2 || $id == 1) //If you add your in r3wt it will allow you to approve withdrawls for gmail members.
		{
			$balance = $row["Amount"];
			$coinid = $row["CoinAcronymn"];
			$getcoin = mysql_query("SELECT * FROM Wallets WHERE `id`='$coinid' OR `Acronymn`='$coinid'");
			$coinacr = mysql_result($getcoin,0,"Acronymn");
			if($row["Confirmed"] == 1 or $id == 2 or $id == 1) //Same goes with this!
			{
				$appurl = "<a href=\"index.php?page=withdrawalqueue&approve=".$row["Id"]."\">Approve</a> | ";
			}
			else
			{
				$appurl = "";
			}
			echo
			'
			<tr>
				<td style="width: 10%;"><center>'.$row["Account"].'</center></td>
				<td style="width: 15%;"><center>'.sprintf("%.8f",$balance).'</center></td>
				<td style="width: 10%;"><center>'.$coinacr.'</center></td>
				<td style="width: 40%;"><center>'.$row["Address"].'</center></td>
				<td style="width: 25%;"><center>'.$appurl.'<a href="index.php?page=withdrawalqueue&cancel='.$row["Id"].'">Cancel</a></center></td>
			</tr>
			';
		}
	}
}else{
	echo '<tr><td style="width: 100%;" colspan="5"><center>No Pending withdrawals</center></td></tr>';
}
?>
</table>
</div>
<h1>Check User History</h1>
<form action="index.php?page=withdrawalqueue" method="POST" name="checkvalid">
<table>
	<tr>
		<td><input type="text" name="username" class="field"  placeholder="username" /></td>
		<td><input type="text" name="coinname" class="field" placeholder="coin" /></td>
		<td><input type="submit"  class="blues" value="check" name="checkvalid" /></td>
	</tr>
</table>
</form>
<?php
if(isset($_POST["checkvalid"])) {

	$user = mysql_real_escape_string(strip_tags($_POST["username"]));
	$coin = mysql_real_escape_string(strip_tags($_POST["coinname"]));
	
	if($coin == null) {
		$getu = mysql_query("SELECT `User_ID` FROM `userCake_Users` WHERE `Username` = '$user'");
		$u_id = mysql_result($getu, 0, "User_ID");
		$getc = mysql_query("SELECT `Wallet_ID` FROM `Wallets` WHERE `Acronymn` = '$coin'");
		$wa_id = mysql_result($getc, 0, "Wallet_ID");
		$depq = mysql_query("SELECT * FROM `deposits` WHERE `Account`='$user' AND `Paid`='1'");
		$tradeq = mysql_query("SELECT * FROM `Trade_History` WHERE `Buyer` = '$u_id' OR `Seller`='$u_id'");
		$ctrades = mysql_query("SELECT * FROM `trades` WHERE `User_ID`='$u_id'");
		$canceled = mysql_query("SELECT * FROM `Canceled_Trades` WHERE `User_ID`='$u_id'");
		$wdhist = mysql_query("SELECT * FROM `Withdraw_History` WHERE `User`='$u_id'");
		$balq = mysql_query("SELECT * FROM `balances` WHERE `User_ID`='$u_id'");
	}else{
		$getu = mysql_query("SELECT `User_ID` FROM `userCake_Users` WHERE `Username` = '$user'");
		$u_id = mysql_result($getu, 0, "User_ID");
		$ctrades = mysql_query("SELECT * FROM `trades` WHERE `User_ID`='$u_id' AND `Type`='$coin'");
		$canceled = mysql_query("SELECT * FROM `Canceled_Trades` WHERE `User_ID`='$u_id' AND `Type`='$coin'");
		$wdhist = mysql_query("SELECT * FROM `Withdraw_History` WHERE `User`='$u_id' AND `Coin`='$coin'");
		$getc = mysql_query("SELECT `Wallet_ID` FROM `Wallets` WHERE `Acronymn` = '$coin'");
		$wa_id = mysql_result($getc, 0, "Wallet_ID");
		$depq = mysql_query("SELECT * FROM `deposits` WHERE `Account`='$user' AND `Coin`='$coin' AND `Paid`='1'");
		$tradeq = mysql_query("SELECT * FROM `Trade_History` WHERE `Buyer` = '$u_id' OR `Seller`='$u_id' AND `Market_Id`='$wa_id'");
		$balq = mysql_query("SELECT * FROM `balances` WHERE `User_ID`='$u_id' AND `Wallet_ID`='$wa_id'");

	}
	
	echo '<h3>Balance, Deposit, and Transaction Information For '.$user.' :</h3>';
	echo '<table id="page">
	<tr>
			<td><center>Deposits</center></td>
	</tr>
	';
	while($row = mysql_fetch_assoc($depq)) {
		echo'
		
		<tr>
			<td>Wallet : '.$row["Coin"].' | Amount : '.sprintf("%.8f",$row["Amount"]).' | Tx : '.$row["Transaction_Id"].'</td>
		</tr>
		';
	}
	echo'
	<tr>
		<td><center>Pending Trades</center></td>
	</tr>
	';
	while($row = mysql_fetch_assoc($ctrades)) {
		echo'
		
		<tr>
			<td>Wallet : '.$row["Type"].' | Price: '.sprintf("%.8f",$row["Value"]).' | Quantity :'.$row["Amount"].'</td>
		</tr>
		';
	}
	echo'
	<tr>
		<td><center>Canceled Trades</center></td>
	</tr>
	';
	while($row = mysql_fetch_assoc($canceled)) {
		echo'
		
		<tr>
			<td>Wallet : '.$row["Type"].' | Price: '.sprintf("%.8f",$row["Value"]).' | Quantity :'.$row["Amount"].'</td>
		</tr>
		';
	}
	echo'
	<tr>
		<td><center>Trade History</center></td>
	</tr>
	';
	while($row = mysql_fetch_assoc($tradeq)) {
		echo'
		
		<tr>
			<td>Wallet : '.$row["Market_Id"].' | Price: '.sprintf("%.8f",$row["Price"]).' | Quantity :'.$row["Quantity"].'</td>
		</tr>
		';
	}
	echo'
	<tr>
			<td><center>Balance</center></td>
	</tr>
	';
	while($row = mysql_fetch_assoc($balq)) {
		echo'
		
		<tr>
			<td>Wallet: '.$row["Wallet_ID"].' | Coin : '.$row["Coin"].' | Amount : '.sprintf("%.8f",$row["Amount"]).'</td>
		</tr>
		';
	}
	echo'
	<tr>
			<td><center>Withdrawal History</center></td>
	</tr>
	';
	while($row = mysql_fetch_assoc($wdhist)) {
		echo'
		
		<tr>
			<td>Coin : '.$row["Coin"].' | Amount : '.sprintf("%.8f",$row["Amount"]).' | Address : '.$row["Address"].'</td>
		</tr>
		';
	}
	echo '</table>';
}
if(isset($_GET["approve"])) {
	$request = mysql_real_escape_string(strip_tags($_GET["approve"]));
	$vars = mysql_query("SELECT * FROM `Withdraw_Requests` WHERE `Id`='$request'");
	$confirmed = mysql_result($vars,0,"Confirmed");
	if($confirmed == 1 || $id == 2 || $id== 1) //Same here Sorry bra for the manual labor :(
	{
		$address = mysql_result($vars, 0, "Address");
		$total = mysql_result($vars, 0, "Amount");
		$user = mysql_result($vars, 0, "User_ID");
		$w_id = mysql_result($vars, 0, "Wallet_Id");
		$wallets = mysql_query("SELECT `Fee` FROM `Wallets` WHERE `Id` = 'w_id'");
		$fee = mysql_result($wallets,0,"Fee");
		$fee2add = $fee - .0001;
		$coin = mysql_result($vars, 0, "CoinAcronymn");
		$wallet = new Wallet($w_id);
		echo $wallet->Withdraw($address,$total,$user,$coin);
		mysql_query("DELETE FROM Withdraw_Requests WHERE `Id`='$request'");
		AddMoney($fee2add,-5,$w_id);
		sleep(2);
		echo '<meta http-equiv="refresh" content="0; URL=index.php?page=withdrawalqueue">';
	}
}

if(isset($_GET["cancel"])) {
	$request = mysql_real_escape_string(strip_tags($_GET["cancel"]));
	$vars = mysql_query("SELECT * FROM Withdraw_Requests WHERE `Id`='$request'");
	$canceled = mysql_result($vars, 0, "Amount");
	$user = mysql_result($vars, 0, "User_ID");
	$w_id = mysql_result($vars, 0, "Wallet_Id");
	$coin = mysql_result($vars, 0, "CoinAcronymn");
	$wallets1 = mysql_query("SELECT `Fee` FROM `Wallets` WHERE `Id` = '$w_id'");
	$fee = mysql_result($wallets1, 0,"Fee");
	echo $fee;
	if($fee == null){ die("no fee set");}
	$total = $canceled + $fee;
	
	$sqlget = mysql_query("SELECT * FROM balances WHERE `User_Id`='$user' AND `Wallet_ID`='$w_id' AND `Coin`='$coin'");
	if(mysql_num_rows($sqlget) > 0) {
		$oldbal = mysql_result($sqlget,0,"Amount");
		$newbal = $oldbal + $total;
		$request_id = mysql_result($sqlget,0,"id");
		$finish = mysql_query("UPDATE balances SET `Amount`='$newbal' WHERE `id`='$request_id'");
	}else{ 
		$finish = mysql_query("INSERT INTO balances (`Amount`,`User_ID`,`Coin`,`Pending`,`Wallet_Id`) VALUES ('$total','$user','$coin','0','$w_id')");	
	}
	if($finish != null){
		$delete = mysql_query("DELETE FROM Withdraw_Requests WHERE `Id`='$request'");
		if($delete != null){
			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=withdrawalqueue">';
		}else{
			echo 'problem with query :'. mysql_error($delete);
		}
	}else{
		echo 'problem with query :'. mysql_error($finish);
	}
}
?>	