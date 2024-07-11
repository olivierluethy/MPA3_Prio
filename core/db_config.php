<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */

if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'localhost'); // Ersetzen Sie dies durch die IP-Adresse aus den Access Hosts
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'prio');
}

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}