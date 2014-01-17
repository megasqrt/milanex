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
echo'
<h2>'.strtoupper($row["name"]).'</h2>
<h3>Status: '.$status.'</h3>
<a class="button blues" href="index.php?page=siteconfig&s_id='.$row["id"].'" name="s_id" />Change Setting</a>
';
}



if(isset($_GET["s_id"])) {
	$confid = mysql_real_escape_string($_GET["s_id"]);
	$action1 = mysql_query("SELECT * FROM config WHERE `id`='$confid'");
	if(!$action1) {
	
		echo "error : ".mysql_error($action1);
	}
	$setting = mysql_result($action1,0, "setting");
	
	echo $setting;
	echo '<br/>';
	if($setting == 1) {
		$outputted = 0;
	}else{
		$outputted = 1;
	}
	echo $outputted;
	echo '</br>';
	$action2 = mysql_query("UPDATE `config` SET  `setting` = '$outputted' WHERE `id` = '$confid' ");
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=siteconfig">';
	
	
}

?>