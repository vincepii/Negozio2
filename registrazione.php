<?php
	session_start();
	
	if (isset($_POST['username'][3])) {
		if ($_POST['password'] == "") echo "La password vuota non è consentita";
		else {
			include('db_conn.php');
			$pass_h = sha1($_POST['password']);
			$query = "SELECT * FROM negozio.utenti
					WHERE user_id = '".$_POST['username']."'";
			$result = mysql_query($query, $link);
			if (!$result) die ('Invalid query: ' . mysql_error());
			$row = mysql_fetch_assoc($result);
			if ($row) echo "Utente già registrato";
			else {
				$query = "INSERT INTO negozio.utenti
						VALUES('".$_POST['username']."', '".$pass_h."')";
				$result = mysql_query($query, $link);
				if (!$result) die ('Invalid query: ' . mysql_error());
				mysql_close($link);
				header("Location: index.php");
			}
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
	<title>Negozio virtuale</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>

<body>
	<h1>Registrazione</h1>
	<form name="loginform" action="" method="post" />
		Username: <br />
		<input type="text" name="username" maxlength="20" /><br />
		Password: <br />
		<input type="password" name="password" />
		<input type="submit" value="registrati" />
	</form>
	<br /><br />
	<a href="index.php">Indietro</a>
</body>
</html>
