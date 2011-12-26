<?php

// Include het connectie bestand
require 'connect.php';

/* Save MySQL deleting/updating
 * ============================
 * Omdat we nu een delete query gebruiken is er een kans dat we per ongeluk
 * de where vergeten in de query. Hierdoor zal heel te tabel worden verwijderd(!)
 * Om dit te voorkomen maken we een simpele saveSQL functie om te kijken of
 * we wel een where erin hebben zitten
 */
function saveSQL( $query )
{
	if( preg_match('/WHERE\s.*?=.*?/i', $query) )
		return true;
	return false;
}

// Maak een array voor foutmeldingen voor de gebruiker
$userErrors = Array();

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	// Het formulier is verzonden

	if( !isset($_POST['naam']) || $_POST['naam'] == '' )
	{
		// Naam is niet ingevuld
		$userErrors[] = 'U heeft geen naam ingevuld';
	}

	if( count($userErrors) == 0 )
	{
		// Er zit niks in $userErrors en dus is alles goed

		// Gebruik altijd MySQLi::escape_string() voor alle
		// variabelen die de gebruiker kan aanpassen (alles met $_)
		// en het moet ook een string zijn. Voor andere types
		// moet je gaan typecasten.
		// Ook moet je in de query quotes gebruiken
		$dQuery = "
			DELETE FROM
				users
			WHERE
				name = '".$sqlLink->escape_string(ucfirst(trim($_POST['naam'])))."'
			";
		// Gebruik in je query geen backtricks (`) 
		// en alleen quotes (') als je te maken hebt met een string ($_POST['naam'] in dit geval)
		// en dus niet bij $rank, want dit is een int. Hierbij zetten we (int) om er zeker van te
		// zijn dat dit een int is
		
		if( saveSQL($dQuery) )
		{
			// Voer de query uit
			$result = $sqlLink->query($sqlLink, $sQuery);
		}
		else
		{
			// De query is niet save
			SQLerror('', 'Er zit geen WHERE in de query, weet u zeker dat dit goed is?', __FILE__);
		}

		if( $sQuery === false )
		{
			// De query is niet gelukt
			SQLerror($sqlLink->error(), 'Uw opdracht kan niet worden uitgevoerd', __FILE__);
		}
		else
		{
			// De query is gelukt, maar heeft hij echt wel iets ingevoegt?
			// Dat bekijken we met mysql_affected_rows(), deze geeft het
			// aantal ingevoegde rijen terug.
			if( $sqlLink->affected_rows($sqlLink) > 0 )
			{
				// Er zijn meer dan 0 rijen ingevoegt, dus het is gelukt!

				// Met MySQLi::insert_id kunnen we het id ophalen, hierbij
				// moet het id veld wel een auto_increment hebben.
				$resultMessage = 'Uw opdracht is succesvol uitgevoerd. '.$_POST['naam'].' is in het systeem '.$sqlLink->insert_id;
			}
			else
			{
				// Er is niks gevonden, dit is geen systeem fout maar een zoekfout => user error
				$userError[] = 'Het opslaan is niet gelukt, probeer het later nog eens.';
			}
		}
	}
}

?>
<!DOCTYPE HTML>
<html lang=nl>
<head>
	<meta charset=UTF-8>
	<title>SQL Boilerplate - SELECT query met MySQL</title>
</head>
<body>
	<?php if( count($errors) > 0 ) : 
		  // Er zijn errors gevonden ?>
		<div class="error">
			<ul>
			<?php foreach( $errors as $err ) : ?>
				<li><?php echo $err; ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if( count($userErrors) > 0 ) : 
		  // Er zijn errors gevonden ?>
		<div class="error">
			<ul>
			<?php foreach( $userErrors as $err ) : ?>
				<li><?php echo $err; ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if( isset($resultMessage) ) echo $resultMessage; ?>
	<h2>Add new user</h2>
	<form action method=post>
		<label>Naam: <input type=text name=naam /></label><br>
		<input type=submit value=Opslaan />
	</form>
</body>
</html>
