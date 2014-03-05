<?php
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserLoggedIn()) 
{
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die(); 
}else{
	$user = $loggedInUser->user_id;
}
?>
<center>
<div class="top">
<center>Your Orders</center>
</div>
<div class="box">
<table id="page" class="data" style="width: 100%;">
<thead>
<tr>
	<th style="width: 15%;">Coin</th>
	<th style="width: 15%;">Type</th>
    <th style="width: 20%;">Price (MLC)</th>	
	<th style="width: 20%;">Quantity</th>	
	<th style="width: 20%;">Total (MLC)</th>	
	<th style="width: 10%;">Options</th>
</tr>
<?php
	$sql = mysql_query("SELECT * FROM trades WHERE `User_ID`='$user' ORDER BY `Value` ASC");

while ($row = mysql_fetch_assoc($sql)) {
	$ids = $row["Id"];
	$from = $row["From"];
	$currency = $row["Type"];
	if($from == $row["Type"])
	{
		$type = "Sell";
	}
	else
	{
		$type = "Buy";
	}
?>
<tr>
	<td style="width: 15%;"><?php echo $currency; ?></td>
	<td style="width: 15%;"><?php echo $type;?></td>
	<td style="width: 20%;"><?php echo sprintf('%.8f',$row["Value"]);?></td>
    <td style="width: 20%;"><?php echo $row["Amount"];?></td>
	<td style="width: 20%;"><?php echo sprintf('%.8f',$row["Amount"] * $row["Value"]);?></td>
	<td style="width: 10%;"><a href="index.php?page=open_orders_all&cancel=<?php echo $ids; ?>">Cancel</a></td>
</tr>
<?php
}
?>
</thead>
</table>
</div>
</center>
<?php
function completecancel($ids) {
	$cord = mysql_query("INSERT INTO Canceled_Trades SELECT * FROM trades WHERE `Id`='$ids'");
	$corq = mysql_query("DELETE FROM trades WHERE `Id`='$ids'");
	if (!$corq) {
	}else{
			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=open_orders_all">';
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
					completecancel($ids);
				}else{
					if($ids <= $s_id) {
						$Fees = ($o_fee + $Amount) * 0.005;
						AddMoney($Amount + $Fees,$owner,$from_id);
						completecancel($ids);
					}else{
						AddMoney($Amount,$owner,$from_id);
						completecancel($ids);
					}
				}	
			}
		}
	}
}

if (isset($_GET["cancel"])) {
	/*this is a more secure method of canceling. double checks to make sure the user hasn't completed the trade.*/
    $ids      = mysql_real_escape_string($_GET["cancel"]);
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=open_orders_all&trade='.$ids.'&clickedcancel=true">';
}