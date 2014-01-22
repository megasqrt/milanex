<?php
	//Application Db
	$dbtype = "mysql"; 
	$db_host = "";
	$db_user = "";
	$db_pass = "";
	$db_name = "";
	$db_port = "";
	$db_table_prefix = "userCake_";
	$langauge = "en";

	//application settings
	$debug_mode = false; //turns error reporting on or off. 
	$title="OpenEx 0.4.6"; //the page title.
	$maint_url = "system/maintenance.php"; //the url to your maintenance mode page
	$BYPASS_CONFIG = ""; //set a bypass key for maintenance mode for the admin. usage is https://yourdomain.com/index.php?BYPASS=<your key without quotes>
	date_default_timezone_set('America/New_York');
	$websiteName = "OpenEx 0.4.6"; //optional website name used for mail functions
	$websiteUrl = ""; //including trailing slash
	$emailActivation = false;
	$resend_activation_threshold = 24;
	$emailAddress = "no-reply@openex.pw";
	$emailDate = date("l \\t\h\e jS");
	$mail_templates_dir = "models/mail-templates/";
	$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
	$default_replace = array($websiteName,$websiteUrl,$emailDate);
	
	//---------------------------------------------------------------------------
	
	if($debug_mode === true) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}else{
	
	}

?>
