<?php
require_once ('system/csrfmagic/csrf-magic.php');
require_once("models/config.php");
if(!isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';
die();
}
$user = addslashes(strip_tags($loggedInUser->user_id));
$act_name = addslashes(strip_tags($loggedInUser->display_username));
?>
<div style="margin: 0 auto;">
<h1>All History for <?php echo $act_name; ?></h1>
<div class="top">
<center>Your Trade History</center>
</div>
<div class="box" id="page">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr class="blue">
	<th style="width: 15%;">Date</th>
	<th style="width: 15%;">Coin</th>
    <th style="width: 10%;">Type</th>
	<th style="width: 20%;">Price</th>
	<th style="width: 20%;">Quantity</th>	
	<th style="width: 20%;">Total(BTC)</th>	
</tr>
<?php
$sqlz = mysql_query("SELECT * FROM Trade_History WHERE (`Buyer`='$user' OR `Seller`='$user')");
$f = 0;
while($row = mysql_fetch_assoc($sqlz)) {
	$f++;
	if($f & 1) {
		$color = "lightgray";
	}else{
		$color = "darkgray";
	}
	if($row["Buyer"] == $user)
	{
		$info = "Bought";
	}
	else
	{
		$info = "Sold";
	}
	$mid = mysql_real_escape_string($row["Market_Id"]);
	$getcn = mysql_query("SELECT * FROM Wallets WHERE `Id`='$mid'");
	$cname = mysql_result($getcn, 0, "Acronymn");
	$time = date("Y-m-d H:i:s", $row["Timestamp"]);
?>
<tr class="<?php echo $color; ?>">
	<td style="width: 15%;"><?php echo $time; ?></td>
	<td style="width: 15%;"><?php echo $cname; ?></td>
	<td style="width: 10%;"><?php echo $info; ?></td>
	<td style="width: 20%;"><?php echo sprintf('%.9f',$row["Price"]);?></td>
    <td style="width: 20%;"><?php echo $row["Quantity"];?></td>
	<td style="width: 20%;"><?php echo sprintf('%.9f',$row["Quantity"] * $row["Price"]);?></td>
</tr>
<?php
}
?>
</thead>
</table>
</div>
<div class="top">
<center>Your Deposit History</center>
</div>
<div class="box" id="page">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr class="blue">
	<th style="width: 25%;">Coin</th>
	<th style="width: 25%;">Quantity</th>	
	<th style="width: 25%;">Tx ID</th>	
</tr>
<?php
$sqlz = mysql_query("SELECT * FROM deposits WHERE `Account`='$act_name'");
$h = 0;
while ($row = mysql_fetch_assoc($sqlz)) {
$h++;
if($h & 1) {
	$color2 = "lightgray";
}else{
	$color2 = "darkgray";
}
?>
<tr class="<?php echo $color2; ?>">
	<td style="width: 25%;"><?php echo $row["Coin"];?></td>
    <td style="width: 25%;"><?php echo sprintf('%.9f',$row["Amount"]);?></td>
	<td style="width: 25%;"><?php echo $row["Transaction_Id"];?></td>
</tr>
<?php
}
?>
</thead>
</table>
</div>

<div class="top">
<center>Your Pending Withdrawals</center>
</div>
<div class="box" id="page">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr class="blue">
	<th style="width: 25%;">Coin</th>
	<th style="width: 25%;">Quantity</th>	
	<th style="width: 25%;">Address</th>	
</tr>
<?php
$sqlza = mysql_query("SELECT * FROM Withdraw_Requests WHERE `User_Id`='$user'");
$l = 0;
while ($row = mysql_fetch_assoc($sqlza)) {
$l++;
if($l & 1) {
	$color3 = "lightgray";
}else{
	$color3 = "darkgray";
}
?>
<tr class="<?php echo $color3; ?>">
	<td style="width: 25%;"><?php echo $row["CoinAcronymn"];?></td>
    <td style="width: 25%;"><?php echo sprintf('%.9f',$row["Amount"]);?></td>
	<td style="width: 25%;"><?php echo $row["Address"];?></td>
</tr>
<?php
}
?>
</thead>
</table>
</div>

<div class="top">
<center>Your Withdraw History</center>
</div>
<div class="box" id="page">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr class="blue">
	<th style="width: 25%;">Coin</th>
	<th style="width: 25%;">Quantity</th>	
	<th style="width: 25%;">Address</th>	
</tr>
<?php
$sqlz = mysql_query("SELECT * FROM Withdraw_History WHERE `User`='$user'");
$m = 0;
while ($row = mysql_fetch_assoc($sqlz)) {
$m++;
if($m & 1) {
	$color4 = "lightgray";
}else{
	$color4 = "darkgray";
}
?>
<tr class="<?php echo $color4; ?>">
	<td style="width: 25%;"><?php echo $row["Coin"];?></td>
    <td style="width: 25%;"><?php echo sprintf('%.9f',$row["Amount"]);?></td>
	<td style="width: 25%;"><?php echo $row["Address"];?></td>
</tr>
<?php
}
?>
</thead>
</table>
</div>


</div>