<?php
include_once('login.php');
include('db_conn.php');

$id = $_GET['id'];
$num = $_GET['num'];

if ($num == 0)
	die('Impossibile selezionare 0 pezzi');

$query = 'LOCK TABLES negozio.prodotti WRITE, negozio.prenotazioni WRITE';
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

//controllo per la quantità richiesta dall'utente
$query = "SELECT disponibili FROM negozio.prodotti
		WHERE id =". $id;
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

$row = mysql_fetch_assoc($result);

//la disponiblità vista dall'utente potrebbe essere cambiata
if ($num <= $row["disponibili"]) {
	//se la prenotazione esiste già, si incrementa pezzi
	//altrimenti si inserisce la nuova prenotazione.
	$query = "SELECT * FROM negozio.prenotazioni
			where user_id = '" .$_SESSION['user']. "' and prod_id = ".$id.";";
	$result = mysql_query($query, $link);
	if (!$result)
		die ('Invalid query: ' . mysql_error());
	
	$row = mysql_fetch_assoc($result);
	if (!$row) {
		//prenotazione non esisteva (il lock in scrittura assicura che nel frattempo non venga inserita una)
		$query = "INSERT INTO negozio.prenotazioni
			values(".$id.", '".$_SESSION['user']."', ".$num.", DATE_ADD(NOW(), INTERVAL 1 HOUR));";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
	}
	else {
		//aggiungere alla prenotazione e rinnovare la scadenza (lock in scrittura garantisce che non venga mod. entry)
		$query = "UPDATE negozio.prenotazioni
				SET pezzi = pezzi +".$num.", scadenza = DATE_ADD(NOW(), INTERVAL 1 HOUR)
				WHERE prod_id = ".$id." and user_id = '".$_SESSION['user']."';";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
	}
	
	//decremento dei pezzi prenotati da prodotti
	//il lock garantisce che non sia stato modificato il valore letto all'inizio (non si può avere disp<0)
	$query = "update negozio.prodotti
			set negozio.prodotti.disponibili = negozio.prodotti.disponibili - ".$num."
			where negozio.prodotti.id = ".$id.";";
	$result = mysql_query($query, $link);
	if (!$result)
		die ('Invalid query: ' . mysql_error());
}
else {
	$query = "UNLOCK TABLES;";
	$result = mysql_query($query, $link);
	if (!$result)
		die ('Invalid query: ' . mysql_error());
	die("Quantità richiesta non disponibile");
}

$query = "UNLOCK TABLES;";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());
	
mysql_close ($link);
header("Location: carrello.php");
?>
