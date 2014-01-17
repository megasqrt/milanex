<?php
require_once("../models/config.php");
if(isMaintenanceDisabled()) {
	echo '<meta http-equiv="refresh" content="0; URL= https://openex.pw">';
	die();
}else{

}
?>
<!Doctype html5 lang="en">
<html>
	<head>
	<meta http-equiv="refresh" content="350; URL=https://openex.pw/system/maintenance.php">
	<link rel="stylesheet" href="../assets/css/style.css" type="text/css" />
	<title>Maintenance</title>
	<style>
	* {
		margin: 0px;
		padding: 0px;
	}
	body {
		background: #B6B6B4;
		color: #fff;
	}
	.centered {
		position: fixed;
		top: 50%;
		z-index: 100;
		background: #0072c6;
		height: 200px;
		margin-top: -100px;
		width: 100%;
	}
	#Splash {
		font-family: 'Exo-Bold', sans-serif;
		font-size: 50px;
	}
	#message {
		font-family: 'ws-ui', sans-serif;
		font-size: 25px;
		text-indentation: 50px;
	}
	</style>
</head>
<body>
	<div class="centered"><br/>
			<center>
		<p id="Splash">OpenEx Is Undergoing Maintenance</p><br/>
		<p id="message">We'll Be Back Soon! :-)</p>
			</center>
	</div>
</body>
</html>