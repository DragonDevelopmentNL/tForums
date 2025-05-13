<?php
// Vul hieronder je databasegegevens in:
$DB_HOST = 'localhost'; // Database host
$DB_USER = 'root';      // Database gebruiker
$DB_PASS = '';          // Database wachtwoord
$DB_NAME = 'forums';     // Database naam

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Database connectie mislukt: ' . $mysqli->connect_error);
}
?> 