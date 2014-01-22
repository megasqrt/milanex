<?php
require_once ('system/csrfmagic/csrf-magic.php');
if (isDepositDisabled()) {
	echo 'Deposits are currently disabled.';
}else{
	if(!isUserLoggedIn()) { 
		echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	}else{
	$account = $loggedInUser->display_username;
	$id = mysql_real_escape_string($_GET["id"]);
	$query = mysql_query("SELECT `Acronymn` FROM `Wallets` WHERE `Id`='$id'");
	$acronymn = mysql_result($query,0,"Acronymn");
	$wallet = new Wallet($id);
	
	echo "<hr class='five'><br/><h4 style='color: red;'>From time to time your wallet addresses may change.</h4><br/>";
		
	echo "<h3>Your ".$acronymn." deposit address is: </h3>";
	
	$address=$wallet->GetDepositAddress($account);
	echo '<input type="text" name="wallet address" class="field" style="width: 400px;" value="'.$address.'" readonly></div>';
	
	}
}
?>
