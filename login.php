<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
if (isset($_SESSION['user'])) {
	if (isset($_COOKIE['credenziali'])) {
		$valori = explode(",", $_COOKIE['credenziali']);
		$scadenza = $valori[1];
		if ($scadenza < time()) {
			//cookie scaduto
			session_destroy();
			echo 'Sessione scaduta, effettuare nuovamente il <a href="index.php">login</a>';
			exit;
		}
		else {
			echo "User: ".$_SESSION['user'].'&nbsp;';
			echo '&nbsp;<a href="logout.php">logout</a>&nbsp;';
			echo '&nbsp;<a href="index.php">HOME</a>&nbsp;';
			echo '&nbsp;<a href="carrello.php">carrello</a> <hr />&nbsp;';
		}
	}
	else {
		//cookie non pervenuto (scaduto lato client)
		session_destroy();
		echo 'Sessione scaduta, effettuare nuovamente il <a href="index.php">login</a>';
		exit;
	}
}
else {
	header("Location: index.php");
}
?>
