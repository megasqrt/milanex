<?php

require_once ('system/csrfmagic/csrf-magic.php');

if(!isUserLoggedIn()) {
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';
}
$user_id = addslashes(strip_tags($loggedInUser->user_id));
$account = addslashes(strip_tags($loggedInUser->display_username));

?>
<h1>Check for a missing deposit</h1>
<?php

echo '
<form method="POST" action="index.php?page=fchk">
	<input type="text" class="fieldsmall" name="Transaction_Id" placeholder="Transaction Id"/>
	<input type="text" class="fieldsmall" name="Coin" placeholder="Coin (Example: BTC)"/>
	<input type="submit" class="miniblues"/>
</form>
<hr class="five">';

if($_POST["Transaction_Id"] != NULL && $_POST["Coin"] != NULL) {

	$tid2 = explode("-",mysql_real_escape_string(trim($_POST["Transaction_Id"])));
	$tid = $tid2[0];
	$coin = mysql_real_escape_string(trim($_POST["Coin"]));
	$sql = mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$coin'");
	$id = @mysql_result($sql,0,"Id");
	$bitcoin = new Wallet($id);
	$sql2 = @mysql_query("SELECT * FROM deposits WHERE `Transaction_Id`='$tid' AND `Coin`='$coin'");
	$id2 = @mysql_result($sql2,0,"id");
	$paid = @mysql_result($sql2,0,"Paid");
	$trans = $bitcoin->Client->gettransaction($tid);
	if($trans != null) {
		$account = $trans["details"][0]["account"];
		$category = $trans["details"][0]["category"];
		$confirms = $trans["confirmations"];
		$amount = $trans["amount"];
		if($id2 != NULL) {
		//echo $amount;
			if($paid == 0)
			{
				if($category == "receive" && $confirms > 5 && $account != "")
				{
					mysql_query("UPDATE deposits SET `Paid`='1' WHERE `id`='$id2'");
					AddMoney($amount, $account, $coin);
					echo $amount." ".$coin." was credited to your account";
				}
			}else{
				echo $amount." ".$coin." was already credited to your account. contact support if you need further assistance.";
			}
		}else{
			if($category == "receive" && $account != "") {
				if($confirms > 5) {
					mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$coin','1','$account');");
					AddMoney($amount, $account, $coin);
					echo $amount." ".$coin." was credited to your account";
				}else{
					mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$coin','0','$account');");
					echo "This Deposit is unconfirmed. Current confirmations:" . $confirms .". Required : 6.";
				}
			}else{
				echo "transaction is not a deposit or account is invalid.";
			}
		}
	}else{
	
		echo "We can't find any information about this deposit. contact support.";
		
	}
	
}else{
 echo"<h4>Here you can search for missing deposits. if this doesn't resolve your issue, please contact support.";
}