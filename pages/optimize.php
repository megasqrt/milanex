<?
/**(c)2013-14 Garrett Morris, OpenEx.pw -- All Rights Reserved.**/
require_once('models/config.php');
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}

mysql_query("OPTIMIZE TABLE TicketReplies");
mysql_query("OPTIMIZE TABLE Trade_History");
mysql_query("OPTIMIZE TABLE Wallets");
mysql_query("OPTIMIZE TABLE Withdraw_History");
mysql_query("OPTIMIZE TABLE Withdraw_Requests");
mysql_query("OPTIMIZE TABLE access_violations");


mysql_query("OPTIMIZE TABLE balances");
mysql_query("OPTIMIZE TABLE bantables_ip");
mysql_query("OPTIMIZE TABLE config");
mysql_query("OPTIMIZE TABLE deposits");
mysql_query("OPTIMIZE TABLE messages");
mysql_query("OPTIMIZE TABLE trades");
mysql_query("OPTIMIZE TABLE userCake_Groups");
mysql_query("OPTIMIZE TABLE userCake_Users");
mysql_query("OPTIMIZE TABLE usersactive");

?>