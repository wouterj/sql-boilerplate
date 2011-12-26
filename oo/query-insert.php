<?php

// Include het connectie bestand
require 'connect.php';

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
	if( !isset($_POST['job']) || $_POST['job'] == '' )
	{
		// Er is niks geselecteerd
		$userErrors[] = 'U heeft geen baan job ingevuld';
	}

	if( count($userErrors) == 0 )
	{
		// Er zit niks in $userErrors en dus is alles goed

		if( $_POST['job'] == 'dev' )
		{
			$rank = 2;
		}
		elseif( $_POST['job'] == 'designer' )
		{
			$rank = 1;
		}
		elseif( $_POST['job'] == 'leader' )
		{
			$rank = 5;
		}
		elseif( $_POST['job'] == 'it' )
		{
			$rank = 3;
		}
		else
		{
			$rank = 0;
		}

		// Gebruik altijd MySQLi::escape_string (alias van MySQLi::real_escape_string) 
		// voor alle variabelen die een string bevatten en die de gebruiker kan 
		// aanpassen (alles met $_). Het gebruik van MySQLi::escape_string zonder het
		// gebruik van quotes in de query is nog steeds niet goed.
		$iQuery = "
			INSERT INTO
				users
				(
					name,
					job,
					rank
				)
			VALUES
				(
					'".$sqlLink->escape_string(ucfirst(trim($_POST['naam'])))."',
					'".$sqlLink->escape_string($_POST['job'])."',
					".(int) $rank."
				)
			";
		// Gebruik in je query geen backtricks (`) 
		// en alleen quotes (') als je te maken hebt met een string ($_POST['naam'] in dit geval)
		// en dus niet bij $rank, want dit is een int. Hierbij zetten we (int) om er zeker van te
		// zijn dat dit een int is
		
		// Voer de query uit
		$result = $sqlLink->query($iQuery);

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
			if( $result->affected_rows() > 0 )
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
		<label>Job: 
			<select name="job">
				<option value="dev">JS developer</option>
				<option value="dev">PHP developer</option>
				<option value="dev">SQL developer</option>
				<option selected></option>
				<option value="designer">Webdesigner</option>
				<option value="leader">Project leader</option>
				<option value="it">IT man</option>
			</select>
		</label><br>
		<input type=submit value=Opslaan />
	</form>
</body>
</html>
