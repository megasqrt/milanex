<?php
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserLoggedIn()) 
{
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die(); 
}
echo '<hr class="five" />';
echo '<hr class="five" />';
?>
<center>
<?
$id     = mysql_real_escape_string($_GET["market"]);
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`=$id");
$name   = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$type = $name;
$user = $loggedInUser->user_id;
if($user != 2)
{
$sql = mysql_query("SELECT * FROM trades WHERE `Type`='$type' AND `User_ID`='$user' ORDER BY `Value` ASC");
}
else
{
$sql = mysql_query("SELECT * FROM trades WHERE `Type`='$type' AND `User_ID`='$user' ORDER BY `Value` ASC");
}
$num_rows = mysql_num_rows($sql);
if($num_rows > 0) {
	?>
	<div class="top">
	<center>Your Orders</center>
	</div>
	<div class="box" id="page">
	<table id="page" class="data" style="width: 100%;">
	<thead>
	<tr>
		<th style="width: 20%;">Type</th>
		<th style="width: 20%;">Price (BTC)</th>	
		<th style="width: 20%;">Quantity(<?php echo $name;?>)</th>	
		<th style="width: 20%;">Total (BTC)</th>	
		<th style="width: 20%;">Options</th>
	</tr>
	<?php
	$g = 0;
	while ($row = mysql_fetch_assoc($sql)) {
		$g++;
		if($g & 1) {
			$color = "lightgray";
		} else {
			$color = "darkgray";
		}
		$marketid = $_GET["market"];
		$ids = $row["Id"];
		$from = $row["From"];
		if($from == $row["Type"])
		{
			$type = "Sell";
		}
		else
		{
			$type = "Buy";
		}
		?>
		<tr class="<?php echo $color; ?>">
			<td style="width: 20%;"><?php echo $type;?></td>
			<td style="width: 20%;"><?php echo sprintf('%.8f',$row["Value"]);?></td>
			<td style="width: 20%;"><?php echo $row["Amount"];?></td>
			<td style="width: 20%;"><?php echo sprintf('%.8f',$row["Amount"] * $row["Value"]);?></td>
			<td style="width: 20%;"><a href="index.php?page=trade&market=<?php echo $marketid; ?>&cancel=<?php echo $ids; ?>">Cancel</a></td>
		</tr>
		<?php
	}
	?>
	</thead>
	</table>
	</div>
<?php
}else{
	echo '<div class="top">
	<center>Your Orders</center>
	</div>
	<div class="box">';
	echo '<h3>No Open Orders</h3>';
	echo '</div>';
	echo '<hr class="five" />';
}
?>
</center>