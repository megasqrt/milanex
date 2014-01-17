<?php
require_once("models/config.php");
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}

$access = mysql_query("SELECT * FROM access_violations");
?>
<table id="page">
<tr>
	<th>Username</th>
	<th>Ip</th>
	<th>User Agent</th>
	<th>Timestamp</th>
	<th>Ban Ip</th>
</tr>
<?php

while($row = mysql_fetch_assoc($access)) {
	echo
	'
	<tr>
		<td>'.$row["username"].'</td>
		<td>'.$row["ip"].'</td>
		<td>'.$row["user_agent"].'</td>
		<td>'.$row["time"].'</td>
		<td><a href="index.php?page=access_violations&ban='.$row["ip"].'">Ban</a></td>
	</tr>
	';
}
?>
</table>
<?php

if(isset($_GET["ban"])) {
	$ipis = mysql_real_escape_string(strip_tags($_GET["ban"]));
	$sql2 = mysql_query("SELECT ip FROM bantables_ip WHERE ip = '$ipis';");
	$number_of_rows = mysql_num_rows($sql2);
		
	if ($number_of_rows > 0) {	
	
	}else {
		$time = mysql_real_escape_string(gettime());
		$sqlxz = mysql_query("INSERT INTO bantables_ip (ip, date) VALUES ( '$ipis', '$time');");	
	}
}
?>