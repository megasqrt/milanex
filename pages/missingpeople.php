<?php

require_once("models/config.php");
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}


echo '<h1>Missing People</h1>';

$sql = mysql_query("SELECT * FROM deposits");
for($i=0;$i<mysql_num_rows($sql);$i++)
{
	$Account = mysql_result($sql,$i,"Account");
	$transaction_id = mysql_result($sql,$i,"Transaction_Id");
	$sql2 = @mysql_query("SELECT * FROM userCake_Users WHERE `Username_Clean`='$Account'");
	$ac = @mysql_result($sql2,0,"Username_Clean");
	if($ac != "")
	{
	}
	else
	{
		echo "Account: $Account Transaction: $transaction_id <br/>";
	}
}
?>