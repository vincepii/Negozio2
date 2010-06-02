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
		$id = $_GET['id'];
				
		echo "<br />";
		
		$query = 'LOCK TABLES negozio.prenotazioni WRITE, negozio.prodotti WRITE;';
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		//cancellazione entry scadute e ripristino delle disponibilità in prodotti
		$now = time();
		$query = "update negozio.prodotti, (
				select prod_id, sum(pezzi) as somma
				from negozio.prenotazioni
				where scadenza < FROM_UNIXTIME(".$now.")
				group by prod_id ) as T
				set negozio.prodotti.disponibili = negozio.prodotti.disponibili + T.somma
				where negozio.prodotti.id = T.prod_id;";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
			
		$query = "DELETE FROM negozio.prenotazioni 
				WHERE scadenza < FROM_UNIXTIME(".$now.");";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
			
		$query = 'UNLOCK TABLES';
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		$query = "(select * from negozio.prodotti\n
				   where id = '$id')";
				
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error() );
				
		$row = mysql_fetch_assoc($result);
		//crea pagina con i dati del prodotto
		echo "<H1>" . $row['nome'] . "</H1>";
		echo "<hr>";
		echo "<h4 style=\"float:left;margin-left:5%;\">Descrizione:</h4>
			  <h4 style=\"float:right;margin-right:40%;\">Immagine:</h4>
			  
			  <div>
			  <div  style=\"height:140px;border-width:2px;border-color:black;border-style:solid;float:left;margin-left:5%;width:35%;\" id=\"desc\">";
		echo  $row['descrizione'] . "</div>";
		echo  "<div style=\"width:35%;height:140px;border-width:2px;border-color:black;border-style:solid;float:right;margin-right:12.3%;\" id=\"img\"> . <IMG src=\"" . $row['immagine'] . "\" height=140px width=200px border=0>";
		echo  "</div><br>";
		echo "<div style=\"float:left;margin-left:5%;width:35%;\" id=\"prezzo\">
			  <h4>Prezzo:</h4><div style=\"float:left;\">" . $row['prezzo'] . "€ </div></div>";
		echo "<div style=\"float:left;margin-left:12.6%;width:35%;\" id=\"disp\">
			  <h4>Disponibili:</h4><div style=\"float:left;\">" . $row['disponibili'] . " pezzi</div></div>
			  </div><br>";
		echo "<form style=\"float:left;margin-top:5%;margin-left:5%;width:35%;\" name=\"Acquista\" action=\"acquista.php\" method=\"get\">
			  <input type=\"hidden\" name=\"id\" value=\"" .$row['id']. "\">
			  <b>Quantità</b>
			  <input type=\"text\" name=\"num\" value=\"1\">
			  <input type=\"submit\" value=\"Acquista\" onclick=\"return check_disp(".$row['disponibili'].",".$id.");\">
			  </form>";
		
		mysql_free_result($result);
		mysql_close ($link);
		?>
	    </div>
	    <script type="text/javascript">
	    function check_disp(n, id) {
			//controlli lato client sul numero di pezzi, avendo js abilitato
			//si evita di avere errore in acquista.php
		    d = document.Acquista.num.value;
		    if (d == 0) {
				alert("Impossibile selezionare 0 pezzi");
			    window.location = "scheda.php?id=" + id;
			    return false;
			}
		    if (d > n) {
			    alert("La quantità selezionata eccede le disponibilità");
			    //necessario fare refresh perché nel frattempo la disponibilità
			    //potrebbe essere cambiata
			    window.location = "scheda.php?id=" + id;
			    return false;
		    }
		    return true;
	    }
	    </script>
	</body>
</html>
