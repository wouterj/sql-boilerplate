<?php

// Include het connectie bestand
require 'connect.php';

// Maak een array voor foutmeldingen voor de gebruiker
$userErrors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Het formulier is verzonden

	if (!isset($_POST['naam'])) {
		// Naam is niet ingevuld
		$userErrors[] = 'U heeft geen naam ingevuld';
	}

	if (count($userErrors) === 0) {
		// Er zit niks in $userErrors en dus is alles goed
		
		// Gebruik altijd MySQLi::escape_string() (alias van MySQLi::real_escape_string()) 
		// voor alle variabelen die een string bevatten en die de gebruiker kan 
		// aanpassen (alles met $_). Het gebruik van MySQLi::escape_string() zonder het
		// gebruik van quotes in de query is nog steeds niet goed.
		$sQuery = "
			SELECT
				name,
				job,
				rank
			FROM
				users
			WHERE
				name = '".$sqlLink->escape_string(ucfirst(trim($_POST['naam'])))."'
			";
		// Gebruik in je query geen backtricks (`) 
		// en alleen quotes (') als je te maken hebt met een string ($_POST['naam'] in dit geval)
		// en dus niet bij $rank, want dit is een int. Hierbij zetten we (int) om er zeker van te
		// zijn dat dit een int is
		
		// Voer de query uit
		$result = $sqlLink->query($sQuery);

		if ($sQuery === false) {
			// De query is niet gelukt
			// Omdat dit gewoon de query is en geen verbinding gebruiken we MySQLi::error()
			SQLerror($sqlLink->error(), 'Uw opdracht kan niet worden uitgevoerd', __FILE__);
		} else {
			// De query is gelukt, maar heeft hij wel een resultaat gekregen?
			// Dat kijken we na met MySQLi_result::num_rows, bij een SELECT query geeft deze
			// het aantal geselecteerde rijen weer
			if ($result->num_rows > 0) {
				// Er zijn meer dan 0 rijen opgehaald en dus is er iets gevonden

                $resultMessage = '';
				// Nu moeten we de resultaten nog fetchen voordat we ze kunnen gebruiken
				// het fetchen zet ze in een array die we vervolgens met een while loop uitlezen
				while ($row = $result->fetch_assoc($result)) {
					// $row is nu elk result. Met $row['kolomNaam'] kun je nu alles op vragen
					$resultMessage .= $row['name'].' is een '.$row['job'].' en zijn rank is '.$row['rank'].'<br>';
				}
			} else {
				// Er is niks gevonden, dit is geen systeem fout maar een zoekfout => user error
				$userError[] = 'Er kan niks gevonden worden';
			}
		}
	}
}

?>
<!DOCTYPE HTML>
<html lang=nl>
<head>
	<meta charset=UTF-8>
	<title>SQL Boilerplate - SELECT query met MySQLi</title>
</head>
<body>
	<?php if (count($errors) > 0) : 
		  // Er zijn errors gevonden ?>
		<div class="error">
			<ul>
			<?php foreach ($errors as $err) : ?>
				<li><?php echo $err; ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if (count($userErrors) > 0) : 
		  // Er zijn errors gevonden ?>
		<div class="error">
			<ul>
			<?php foreach ($userErrors as $err) : ?>
				<li><?php echo $err; ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; 
    if (isset($resultMessage)) {
        echo $resultMessage; 
    }
    ?>
	<form action method=post>
		<label>Naam: <input type=text name=naam></label><br>
		<input type=submit value=Zoek>
	</form>
</body>
</html>
