<?php
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserLoggedIn()) 
{
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die(); 
}
?>
<center>
<?php
$id     = mysql_real_escape_string($_GET["market"]);
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
$name   = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$type = $name;
$user = $loggedInUser->user_id;
?>
<div class="top">
<center>Your Trade History(MOST RECENT FIRST)</center>
</div>
<div class="box" id="page">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr>
	<th style="width:20%;">Date</th>
    <th style="width: 20%;">Type</th>
	<th style="width: 20%;">Price</th>
	<th style="width: 20%;">Quantity(<?php echo $name;?>)</th>	
	<th style="width: 20%;">Total (MLC)</th>	
</tr>
<?php
$sqlz = mysql_query("SELECT * FROM Trade_History WHERE (`Buyer`='$user' OR `Seller`='$user') AND `Market_Id`='$id' ORDER BY `Timestamp` DESC");
$g = 0;
while ($row = mysql_fetch_assoc($sqlz)) {
$g++;
if($g & 1) {
	$color = "lightgray";
} else {
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
$time = date("Y-m-d H:i:s", $row["Timestamp"]);
?>
<tr class="<?php echo $color; ?>">
	<td style="width: 20%;"><?php echo $time;?></td>
	<td style="width: 20%;"><?php echo $info;?></td>
	<td style="width: 20%;"><?php echo sprintf('%.8f',$row["Price"]);?></td>
    <td style="width: 20%;"><?php echo $row["Quantity"];?></td>
	<td style="width: 20%;"><?php echo sprintf('%.8f',$row["Quantity"] * $row["Price"]);?></td>
</tr>
<?php
}
?>
</thead>
</table>
</div>
</center>
