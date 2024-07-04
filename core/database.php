<?php

require_once 'db_config.php';

function connectDatabase() {
    try {
        $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME;
        return new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    } catch (PDOException $e) {
        die('Keine Verbindung zur Datenbank möglich: ' . $e->getMessage());
    }
}
