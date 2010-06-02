<?php
include_once('login.php');
include('db_conn.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Negozio Virtuale</title>
	</head>
	<body>
		<div>
		<?php
		$id_prod = $_GET['id'];
		$num = $_GET['num'];
		
		$query = "LOCK TABLES negozio.prenotazioni WRITE;";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());

		//verifica esistenza prenotazione
		$query = "SELECT * FROM negozio.prenotazioni
				  WHERE prod_id = ".$id_prod." AND user_id = '".$_SESSION['user']."';";

		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		$row = mysql_fetch_assoc($result);
		if (!$row){
			//prenotazione inesistente:
			$query = "UNLOCK TABLES;";
			$result = mysql_query($query, $link);
			if (!$result)
				die ('Invalid query: ' . mysql_error());

			echo "<h3>La sua prenotazione è scaduta o inesistente. Il pagamento con la
					banca non è avvenuto.</h3>";		
		}
		else{
			//salvataggio della prenotazione e cancellazione (ripristinata se pagamento non verrà fatto)
			$salva_pezzi = $row['pezzi'];
			$salva_scandenza = $row['scadenza'];
			
			$query = "DELETE FROM negozio.prenotazioni
					WHERE prod_id = ".$id_prod." AND user_id = '".$_SESSION['user']."'";
			$result = mysql_query($query, $link);
			if (!$result)
				die ('Invalid query: ' . mysql_error());
				
			$query = "UNLOCK TABLES;";
			$result = mysql_query($query, $link);
			if (!$result)
				die ('Invalid query: ' . mysql_error());
			
			//transazione simulata con la banca
			sleep(rand(1,10));
			//esito pagamento
			$esito = rand(0,1);
			if ($esito){
				//pagamento completato
				echo "<h3>Pagamento eseguito con successo.</h3>";
			}
			else{
				//pagamento non effettuato: ripristino della prenotazione
				$query = "LOCK TABLES negozio.prenotazioni WRITE;";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());
					
				//se nel frattempo è stata rifatta una prenotazione per lo stesso prodotto
				//si deve aggiornare, altrimenti creare una nuova entry
				$query = "SELECT * FROM negozio.prenotazioni
						  WHERE prod_id = ".$id_prod." AND user_id = '".$_SESSION['user']."';";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());
				
				$row = mysql_fetch_assoc($result);
				if (!$row) {
					//la stessa prenotazione non è stata rifatta
					$query = "INSERT INTO negozio.prenotazioni
							values(".$id_prod.", '".$_SESSION['user']."', ".$salva_pezzi.", '".$salva_scandenza."');";
					$result = mysql_query($query, $link);
					if (!$result)
						die ('Invalid query: ' . mysql_error());
				}
				else {
					//durante il pagamento è stata rifatta la stessa prenotazione
					$query = "UPDATE negozio.prenotazioni
							SET pezzi = pezzi + ".$salva_pezzi." 
							WHERE prod_id = ".$id_prod." and user_id = '".$_SESSION['user']."'";
					$result = mysql_query($query, $link);
					if (!$result)
						die ('Invalid query: ' . mysql_error());
				}
				
				$query = "UNLOCK TABLES;";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());

				echo "<h3>Errore con il pagamento. Riprovare. Prenotazione
						ancora esistente.</h3>";
			}
		}

		mysql_close ($link);
		?>
		</div>
	</body>
</html>
