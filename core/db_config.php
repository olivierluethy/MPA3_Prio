<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Pfad anpassen, falls notwendig

use Dotenv\Dotenv;

// Laden der .env-Datei
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // Pfad anpassen, falls notwendig
$dotenv->load();

/* Database credentials aus Umgebungsvariablen */
if (!defined('DB_SERVER')) {
    define('DB_SERVER', $_ENV['DB_SERVER']);
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', $_ENV['DB_USERNAME']);
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $_ENV['DB_NAME']);
}

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
