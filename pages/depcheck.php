<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once ('system/csrfmagic/csrf-magic.php');
require_once("models/config.php");
$id00 = addslashes(strip_tags($loggedInUser->user_id));
if(!isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}
if(!isUserAdmin($id00))
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}else{
?>
	<h1>Check for a missing deposit</h1>

	<form method="POST" action="index.php?page=depositchecker">
		<input type="text" class="fieldsmall" name="Transaction_Id" placeholder="Transaction Id"/>
		<input type="text" class="fieldsmall" name="Coin" placeholder="Coin (Example: MLC)"/>
		<input type="submit" class="miniblues" name="fchk"/>
	</form>
	<hr class="five">
<?php
	if(isset($_POST["fchk"])) {
		if(isUserAdmin($id00)) {
			if($_POST["Transaction_Id"] != NULL && $_POST["Coin"] != NULL) {
				$tid = mysql_real_escape_string(trim($_POST["Transaction_Id"]));
				$coin = mysql_real_escape_string(trim($_POST["Coin"]));
				$sql = mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$coin'");
				$id = @mysql_result($sql,0,"Id");
				
				$sql2 = @mysql_query("SELECT * FROM deposits WHERE `Transaction_Id`='$tid' AND `Coin`='$coin'");
				$id2 = @mysql_result($sql2,0,"id");
				$paid = @mysql_result($sql2,0,"Paid");
				$wallet = new Wallet($id);
				$trans = @$wallet->gettransaction($tid);
				//echo '<pre>';
				//print_r($trans);
				//echo '</pre>';
				if($trans != null) {
					if(is_array($trans)) {
						if(in_array("Invalid or non-wallet transaction id", $trans,true)) {
							
							echo "non wallet transaction id or invalid tx";
						}else{
							$account = $trans["details"][0]["account"];
							$category = $trans["details"][0]["category"];
							$confirms = $trans["confirmations"];
							$amount = $trans["amount"];
							if($id2 != NULL) {
								if($paid == 0) {
									if($category == "receive" && $confirms > 3 && $account != "")
									{
										mysql_query("UPDATE deposits SET `Paid`='1' WHERE `id`='$id2'");
										AddMoney($amount, $account, $coin);
										echo $amount." ".$coin." was credited to your account";
									}
								}else{
									echo $amount." ".$coin." was already credited to the account.";
								}
							}else{
								if($category == "receive" && $account != "") {
									if($confirms > 5) {
										mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$coin','1','$account');");
										AddMoney($amount, $account, $coin);
										echo $amount." ".$coin." was successfully credited to the account";
									}else{
										mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$coin','0','$account');");
										echo "This Deposit is unconfirmed. Current confirmations:" . $confirms .". Required : 6.";
									}
								}else{
									echo "transaction is not a deposit or account is invalid.";
								}
							}	
						}
					}else{
						echo "Contact the admin. Error Code: 35-1a"; 
						/* ERROR CODE INFORMATION 
							
							Error Code 35-la
							the result wasn't an array. so its probably invalid. inform customer to disregard.
						 */
					}
				}
			}	
		}	
	}
}
