<?php
$id = $loggedInUser->user_id;
if(isset($_POST["Clearall"])) {
	if(!isUserAdmin($id)) { die; } else {
		$setupsql = mysql_query("SELECT * FROM `trades`");
		$norows = mysql_num_rows($setupsql);
		for($g = 1; $g < $norows;$g++) {
			$ids      = mysql_result($setupsql,$g,"Id");
			$tradesql = mysql_query("SELECT * FROM trades WHERE `Id`='$ids'");
			if(mysql_num_rows($tradesql) > 0) {
				$from     = mysql_result($tradesql,0, "From");
				$owner    = mysql_result($tradesql,0, "User_ID");
				$type     = mysql_result($tradesql,0, "Type");
				$o_fee    = mysql_result($tradesql,0,"Fee");
				$Amount   = mysql_result($tradesql,0, "Amount");
				$Price    = mysql_result($tradesql,0, "Value");
				$sql      = mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$from'");
				$from_id  = mysql_result($sql,0,"Id");
				if($owner == $loggedInUser->user_id || isUserAdmin($id2)) {
					if($from != $type) {
						$Total = sprintf("%2.8f",$Amount * $Price);
						$Fees  = ($Total + $o_fee) * 0.005;
						AddMoney($Total + $Fees,$owner,$from_id);
						if(isset($id)) { completecancel($ids,$id); }
					}else{
						if($ids <= $s_id) {
							$Fees = ($o_fee + $Amount) * 0.005;
							AddMoney($Amount + $Fees,$owner,$from_id);
							if(isset($id)) { completecancel($ids,$id);}
						}else{
							AddMoney($Amount,$owner,$from_id);
							if(isset($id)) { completecancel($ids,$id);}
						}
					}	
				}
			}else{
				$g++;
			}
		}
	}
}