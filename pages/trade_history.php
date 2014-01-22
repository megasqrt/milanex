<?php
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserLoggedIn()) 
{
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die(); 
}
?>
<center>
<?
$id     = mysql_real_escape_string($_GET["market"]);
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
$name   = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$type = $name;
$user = $loggedInUser->user_id;
?>
<div class="top">
<center>Your Trade History</center>
</div>
<div class="box">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr>
    <th style="width: 25%;">Type</th>
	<th style="width: 25%;">Price</th>
	<th style="width: 25%;">Quantity(<?php echo $name;?>)</th>	
	<th style="width: 25%;">Total (BTC)</th>	
</tr>
<?php
$sqlz = mysql_query("SELECT * FROM Trade_History WHERE (`Buyer`='$user' OR `Seller`='$user') AND `Market_Id`='$id'");
while ($row = mysql_fetch_assoc($sqlz)) {
if($row["Buyer"] == $user)
{
	$info = "Bought";
}
else
{
	$info = "Sold";
}
?>
<tr>
	<td style="width: 25%;"><?php echo $info;?></td>
	<td style="width: 25%;"><?php echo sprintf('%.8f',$row["Price"]);?></td>
    <td style="width: 25%;"><?php echo $row["Quantity"];?></td>
	<td style="width: 25%;"><?php echo sprintf('%.8f',$row["Quantity"] * $row["Price"]);?></td></tr>
	<?php
	}
	?>
</thead>
</table>
</div>
</center>