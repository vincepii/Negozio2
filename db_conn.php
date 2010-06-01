<?php
$user = 'user';
$password = 'password';
$link = mysql_connect('localhost', $user, $password);
if (!$link)
	die ('Could not connect: ' . mysql_error() );
?>
