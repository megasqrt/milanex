<?php
include("../models/config.php");
$id     = mysql_real_escape_string($_GET["market"]);
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`=$id");
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
$sql = mysql_query("SELECT * FROM trades WHERE `Type`='$type' ORDER BY Value DESC");
$previous = "";
$amount = 0;
while ($row = mysql_fetch_assoc($sql)) {
if($row["Value"] != sprintf("%.8f",$previous))
{
$previous = sprintf("%.8f",$row["Value"]);
$value = sprintf("%.8f",$row["Value"]);
$sqls = mysql_query("SELECT * FROM trades WHERE `Type`='$type' AND `Value`='$value' AND `To`='$type'");
$amount = 0;
while ($rows = mysql_fetch_assoc($sqls))
{
$amount = $amount + $rows["Amount"];
}
if($row["To"] == $name) { 
?>
<tr>
	<td style="width: 33%;"><?php echo sprintf('%.8f',$row["Value"]);?></td>
    <td style="width: 33%;"><?php echo sprintf('%.8f',$amount); ?></td>
	<td style="width: 33%;"><?php echo sprintf('%.8f',$amount * $value); ?></td>
</tr>
<?php
}
}
}
?>
</thead>
</table>
</div>
