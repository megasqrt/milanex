<?php
require_once ('system/csrfmagic/csrf-magic.php');
if (isDepositDisabled()) {
	echo 'Deposits are currently disabled.';
}else{
	if(!isUserLoggedIn()) { 
		echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	}else{
		accountMenu();
		$account  = $loggedInUser->display_username;
		$id2      = mysql_real_escape_string($_GET["id"]);
		if($id2 == '0') {
			echo "<h3>deposits of this coin are disabled. it is scheduled to be removed from openex.pw</h3>";
		}else{
			$query    = mysql_query("SELECT `Acronymn` FROM `Wallets` WHERE `Id`='$id2'");
			$acronymn = mysql_result($query,0,"Acronymn");
			$userq    = @mysql_query("SELECT * FROM `User_Addresses` WHERE `Wallet_ID`='$id2' AND `User_ID`='$id'");
			$addy     = @mysql_result($userq,0,"Address");
			if(isset($_POST["Regenerate"])) {
				$addy = "REGEN";
			}
			if($addy == null) {
				$wallet = new Wallet($id2);
				$addy=$wallet->GetDepositAddress($account);
				@mysql_query("INSERT INTO `User_Addresses` VALUES ('$id','$addy','$id2')");
			}
			if($addy == "REGEN") {
				$wallet = new Wallet($id2);
				$addy=$wallet->Client->getnewaddress($account);
				@mysql_query("UPDATE `User_Addresses` SET `Address`='$addy' WHERE `Wallet_ID`='$id2' AND `User_ID`='$id'");
			}
			echo "<h3>Your ".$acronymn." deposit address is: </h3>";
			echo '<br/><input type="text" name="wallet address" class="field stdsize" style="width: 400px;" value="'.$addy.'" readonly/>';
			echo '<br/><form action="index.php?page=deposit&id='.$id2.'" method="POST" >
				  <input type="submit" name="Regenerate" value="Get New Address" class="blues stdsize"/>
				  </form></div>';	
		}	
	}
}
?>
