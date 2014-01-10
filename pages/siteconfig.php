<?php

require_once("models/config.php");

if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}


?>
<h1>Site Configuration</h1>
<h4>enable and disable settings</h4>
<hr class="five">
<hr class="five">
<hr class="five">
<?php
$siteconfig = mysql_query("SELECT * FROM config");
while($row = mysql_fetch_assoc($siteconfig)){

	if($row['setting'] == 1){
		$status = "<font color='red'>Disabled</font>";
	}else{
		$status = "<font color='green'>Enabled</font>";
	}
?>
<h2><?php echo strtoupper($row["name"]); ?></h2>
<h3>Status: <?php echo $status; ?></h3>
<center>
	<form action="index.php?page=siteconfig" name="change" method="POST" onsubmit="document.getElementById('#change').disabled = 1;">
		<input type="hidden" value="<?php echo $row["id"]; ?>" name="s_id" />
		<input type="submit" value="Change Setting" class="blues" id="change" />
	</form>
</center>
<?php
}



if(isset($_POST["change"])){

$s_id = mysql_real_escape_string($_POST["s_id"]);
$action1 = mysql_query("SELECT * FROM config WHERE `id`='$s_id'");
	while($row2 = mysql_fetch_assoc($action1)) {
		$setting = $row2["setting"];

		if($setting == 1) {
			$outputted = 0;
		}else{
			$outputted = 1;
		}
		$action2 = mysql_query("UPDATE `config` SET  `setting` = '$outputted' WHERE `id` ='$variable' ");
	}
	
}

