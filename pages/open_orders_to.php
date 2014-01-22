<?php
include("../models/config.php");
$id     = mysql_real_escape_string($_GET["market"]);
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
$name   = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$type = $name;
?>
<div class="top">
<center>Buy Orders</center>
</div>
<div class="box">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr>
    <th style="width: 33%;">Price</th>
    <th style="width: 33%;">Quantity</th>
	<th style="width: 33%;">Total</th>
</tr>
<?php
$sql = mysql_query("SELECT * FROM trades WHERE `Type`='$type' ORDER BY `Value` DESC");
$previous = "";
$amount = 0;
while ($row = mysql_fetch_assoc($sql)) {
if($row["From"] == "BTC") { 
if(sprintf("%.8f",$row["Value"]) != sprintf("%.8f",$previous))
{
$value = sprintf("%.8f",$row["Value"]);
$amount = 0;
$sqls = mysql_query("SELECT * FROM trades WHERE `Value`='$value' AND `From`='BTC' AND `Type`='$type'");
$num = mysql_num_rows($sqls);
while ($rows = mysql_fetch_assoc($sqls))
{
	$amount += $rows["Amount"];
}


?>
<tr>
	<td style="width: 33%;"><p onclick="document.getElementById('Amount').value = '<?php echo sprintf('%.8f',$amount); ?>'; document.getElementById('price1').value = '<?php echo sprintf('%.8f',$value); ?>'; "><?php echo sprintf('%.8f',$row["Value"]);?></p></td>
    <td style="width: 33%;"><?php echo sprintf('%.8f',$amount);?></td>
	<td style="width: 33%;"><?php echo sprintf('%.8f',$amount * $value);?></td>
</tr>
<?php
$previous = sprintf("%.8f",$row["Value"]);
}
}
}
?>
</thead>
</table>
</div>
