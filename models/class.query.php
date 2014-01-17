<?php
 /*2014 OpenEx*/
/*query class*/

	class query {
			
		public function getbtc($userid) {
		
			$btc = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$userid' AND `Wallet_ID` = '1'");
			$curbtc = mysql_result($getbtc, 0, "Amount");
			if($curbtc == null){
				$curbtc = "0.00000000";
			}
			return $curbtc;
		}
		
		public function getbal($userid, $coin) {
		
			$bal = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$userid' AND `Wallet_ID` = '$coin'");
			$curbal = mysql_result($getbal, 0, "Amount");
			if($curbal == null){
				$curbal = "0.00000000";
			}
			return $curbal;
		}
	}
	




}