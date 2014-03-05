<?php

include 'mysqli.class.php';

$config = array();
$config['host'] = 'localhost';
$config['user'] = 'username';
$config['pass'] = 'password';
$config['table'] = 'database';

$db = new DB($config);

?>