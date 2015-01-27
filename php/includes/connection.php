<?php
	$dbhost = 'eu-cdbr-azure-west-b.cloudapp.net';
	$dbuser = 'b8833e255abd25';
	$dbpass = 'f25e32ab';
	$dbname = 'llamadb';
  	$db = new PDO('mysql:host='.$dbhost.';dbname='.$dbname.';charset=utf8', $dbuser, $dbpass);
?>