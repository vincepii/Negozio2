<?php
	session_start();
	if (isset($_SESSION['user'])) {
		header("Location: start.php");
	}
	else if (isset($_POST['username'][3])) {
		include('db_conn.php');
		$pass_h = sha1($_POST['password']);
		
		$query = "SELECT * FROM negozio.utenti
				WHERE user_id = '".$_POST['username']."' and password = '".$pass_h."';";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		$row = mysql_fetch_assoc($result);
		mysql_close($link);
		if ($row) {
			$_SESSION['user'] = $_POST['username'];
			//impostare cookie
			//campi: username, scadenza, timestamp, sha1(username, timestamp, segreto condiviso)
			$now = time();
			$scadenza = $now + 900;
			$valore = $_SESSION['user'].",".$scadenza.",".$now.",".sha1($_SESSION['user'].",".$now.",".$pass_h);
			setcookie("credenziali", $valore, $scadenza);
			header("Location: start.php");
		}
		else {
			header("Location: registrazione.php");
		}
	}
	else {
		echo 'Inserire un username di almeno 4 caratteri';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Negozio Virtuale</title>
	</head>
	<body>
		<h1>Login</h1>
		<form name="loginform" action="" method="post" />
			Username: <br />
			<input type="text" name="username" maxlength="20" /><br />
			Password: <br />
			<input type="password" name="password" />
			<input type="submit" value="login" />
		</form>
		<br /><br />
		Nuovo utente: <a href="registrazione.php">registrati</a>
	</body>
</html>
