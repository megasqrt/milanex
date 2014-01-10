<?php
require_once("models/config.php");
if(!isUserLoggedIn()) {
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die();
}
if(!isUserAdmin($id)) {
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die();
}
?>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<h1>Site Monitor</h1>
<div class="serverload">
	<form action="" name="serverload" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="serverload" value="Show Server Load" class="blues" id="submit" />
	</form>	
</div>
<hr class="five" />
<div class="mysqlload">
	<form action="" name="mysqlload" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="mysqlload" value="Show SQL Status" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="users">
	<form action="" name="userstats" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="userstats" value="Show User Statistics" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="coinstatus">
	<form action="" name="coinstatus" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="coinstatus" value="Check Coin Stats" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="results" id="result">
<?php
if (isset($_POST["serverload"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
	echo'<b class="title-point">Server Load(main):</b></br>
		<table id="page">';
	
		$var_load = sys_getloadavg();
		
		foreach($var_load as $key => $value) {
			echo "<tr><td>".$key." : ".$value."</tr><td>";
		}
	echo'</table>';			

}

if (isset($_POST["mysqlload"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
		echo'<b class="title-point">MySQL Load:</b></br>
		<table id="page">';
			$mysqlload = explode("  ", mysql_stat());
				foreach ($mysqlload as $key => $value){
					echo "<tr><td>".$key." : ".$value."<td></tr>";
				}
		echo'</table>';

}

if (isset($_POST["userstats"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
	echo '
	<b class="title-point">Users Online</b></br>
		<table id="page">';
			
			$getusers = mysql_query("SELECT * FROM usersactive WHERE `id`=1");
			$loggedin = mysql_result($getusers, 0, "users_logged_in");
			$total = mysql_result($getusers, 0, "total_users");
			$upda = mysql_result($getusers, 0, "last_update");
				echo "
				<tr>
					<td>Logged In : ".$loggedin."<td>
				</tr>
				<tr>
					<td>Total Users: ".$total."<td>
				</tr>
				<tr>
					<td>Last Updated : ".$upda."<td>
				</tr>
				
			
			
		</table>";	
}

if (isset($_POST["coinstatus"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
	echo'<b class="title-point">Coin Status:</b></br>
		<table id="page">
			<center>
				<tr>
					<th>Name</th>
					<th>Balance</th>
					<th>Blocks</th>
					<th>Connections</th>
					<th>errors</th>
				</tr>
				';

				$result = mysql_query("SELECT * FROM `Wallets` ORDER BY `Wallets`.`Acronymn` ASC");
				
				while($row = mysql_fetch_array($result))
				{
				$id = $row["Id"];
				$pw = $row["Wallet_Password"];
				$usr = $row["Wallet_Username"];
				$wallet = new Wallet($id);
				$id_check = $loggedInUser->user_id;
				$requestkey = md5( hash('sha512', $id_check.$id.$usr.$pw));
				$info = $wallet->GetInfo($id,$pw,$usr,$requestkey,$id_check);

				echo "<tr>";
				echo "<td>" . $row['Acronymn'] . "</td>";
				foreach($info as $key => $value) {
					if($key == "balance"){
						echo '<td>'.$value.'</td>';
					}
					elseif($key == "blocks"){
						echo '<td>'.$value.'</td>';
					}
					elseif($key == "connections"){
						echo '<td>'.$value.'</td>';
					}
					elseif($key == "errors"){
						echo '<td>'.$value.'</td>';
					}else{
						
					}
				}
				echo "</tr>";
				
				}
			
		echo'</center></table>';
}
?>
</div>
