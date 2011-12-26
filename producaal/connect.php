<?php
/*
 * MySQLi CONNECTIE BESTAND
 * In dit bestand maken we de verbinding met een MySQL
 * server doormiddel van MySQLi functies. In deze
 * functie zit meteen het selecteren van een db, dus
 * dat doen we ook meteen.
 */

/* Error-Handling
 * ==============
 * Als eerst maken we een functie voor het beheren van
 * errors. Hiermee kunnen we makkelijk het probleem vinden
 * als er iets mis is.
 * Zodra een script online is wil je niet dat de gebruiker
 * de foutmeldingen krijgt, vandaar dat we hier error_log
 * gebruiken als DEBUG_MODE false is.
 */
// DEBUG_MODE, deze wordt false als het script online geplaatst is
define('DEBUG_MODE', true);

/*
 * We slaan alle errors op in $errors. Deze lezen we in het
 * script uit in via een foreach loop
 */
$errors = Array();

if( DEBUG_MODE )
{ // DEBUG_MODE staat aan
	// Zorg dat we alle errors te zien krijgen
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_NOTICE);
}
else
{
	// DEBUG_MODE uit dus geen errors tonen
	ini_set('display_errors', 'Off');
	error_reporting(0);
}

function SQLerror( $error, $message, $file )
{
	// $error is het resultaat van mysql_error()
	// $message is de tekst die bij de error staat, 
	//          deze tekst zullen we gebruiken als DEBUG_MODE uit staat
	// $file is het resultaat van __FILE__ in het bestand van de error
	
	global $errors; // Zorg dat de error variabele die we net hebben gemaakt in deze functie komt

	if( DEBUG_MODE )
	{ # DEBUG_MODE aan => sla de errors op zodat we ze later kunnen tonen
		$errors[] = $message.': '.$error;
	}
	else
	{ # DEBUG_MODE uit => log de errors en sla alleen de $message op
		// We slaan niet alleen de error op, maar ook het bestand en de datum
		$log = $file.' ['.date('H:i:s').'] '.$error;
		error_log($log);

		$errors[] = $message;
	}
}

/* CONNECTIE MET MySQL SERVER
 * ==========================
 */
$sqlLink = mysqli_connect('localhost', 'username', 'password', 'sql-boilerplate');
// Verander de host, inlog naam, wachtwoord en sql-boilerplate (db) in de juiste gegevens

if( $sqlLink === false )
{
	// Als mysqli_connect false returned is er iets mis gegaan, gebruik de net gemaakte error functie
	// Omdat het de connectie betreft gebruiken we mysqli_connect_error()
	SQLerror( mysqli_connect_error(), 'We kunnen geen verbinding aanmaken', __FILE__ );
}
