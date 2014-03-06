<?php

	require_once("jsonRPCClient.php");
	include("settings.php");

	function establishRPCConnection($ip,$port)

	{
                $htss=($port == 443)?"https":"http";
		$bitcoin = new jsonRPCClient($htss . '://$db_wallet_user:$db_wallet_password@' . $ip . ':' . $port );
		return $bitcoin;

	}

?>
