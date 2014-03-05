<?php
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}
$history= @mysql_query("SELECT * FROM Withdraw_History ORDER BY `Timestamp` DESC");
echo '<table id="page">
		<tr>
			<th>Coin</th>
			<th>Amount</th>
			<th>Address</th>
			<th>User</th>
		</tr>
	';
while($row = mysql_fetch_assoc($history)) {
	$toget = $row["User"];
	$getuser = mysql_query("SELECT `Username` FROM userCake_Users WHERE `User_ID`='$toget'");
	$uname = mysql_result($getuser, 0, "Username");
	$balance = $row["Amount"];
	echo 
	'
	<tr>
		<td>'.$row["Coin"].'</td>
		<td>'.sprintf("%.8f",$balance).'</td>
		<td>'.$row["Address"].'</td>
		<td>'.$uname.'</td>
	</tr>
	';
}
echo '</table>';
?>