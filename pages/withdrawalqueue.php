<?php
require_once("models/config.php");
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}
$getreq = mysql_query("SELECT * FROM Withdraw_Requests ORDER BY (id) ASC");
if(mysql_num_rows($getreq) > 0) {
?>
<h1>Pending Withdrawals</h1>
<table id="page">
<tr>
	<td>User</td>
	<td>Amount</td>
	<td>Coin</td>
	<td>Recipient</td>
	<td>Options</td>
</tr>
<?php
while ($row = mysql_fetch_assoc($getreq)) {
		$balance = $row["Amount"];
		echo
		'
		<tr>
			<td>'.$row["Account"].'</td>
			<td>'.sprintf("%.8f",$balance).'</td>
			<td>'.$row["CoinAcronymn"].'</td>
			<td>'.$row["Address"].'</td>
			<td><a href="index.php?page=withdrawalqueue&approve='.$row["Id"].'">Approve</a><a href="index.php?page=withdrawalqueue&cancel='.$row["Id"].'">Cancel</a></td>
		</tr>
		';
}
?>
</table>
<?php
}else{
	echo '<h3>No Pending withdrawals</h3>';
}

if(isset($_GET["approve"])) {
	$request = mysql_real_escape_string(strip_tags($_GET["approve"]));
	$vars = mysql_query("SELECT * FROM Withdraw_Requests WHERE `Id`='$request'");
	$address = mysql_result($vars, 0, "Address");
	$total = mysql_result($vars, 0, "Amount");
	$user = mysql_result($vars, 0, "User_ID");
	$w_id = mysql_result($vars, 0, "Wallet_Id");
	$coin = mysql_result($vars, 0, "CoinAcronymn");
	$wallet = new Wallet($w_id);
	echo $wallet->Withdraw($address,$total,$user,$coin);
	mysql_query("DELETE FROM Withdraw_Requests WHERE `Id`='$request'");
}

if(isset($_GET["cancel"])) {
	$request = mysql_real_escape_string(strip_tags($_GET["cancel"]));
	$vars = mysql_query("SELECT * FROM Withdraw_Requests WHERE `Id`='$request'");
	$total = mysql_result($vars, 0, "Amount");
	$user = mysql_result($vars, 0, "User_ID");
	$w_id = mysql_result($vars, 0, "Wallet_Id");
	$coin = mysql_result($vars, 0, "CoinAcronymn");
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
		mysql_query("DELETE FROM Withdraw_Requests WHERE `Id`='$request'");
	}else{
		echo 'problem with query :'. mysql_error($finish);
	}
}
?>	