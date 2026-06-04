<?php
/**
 * Nutze diese Funktion um einfach eine Ausgabe
 * mit htmlspecialchars() zu erstellen.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Nutze diese Funktion um auf einen POST-Wert
 * zuzugreifen.
 */
function post(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

function get(string $key, $default = '', callable $filter = null)
{
    $value = $_GET[$key] ?? $default;
    if ($filter) {
        $value = $filter($value);
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


/**
 * Liest eine Konfiguration aus den Umgebungsvariablen
 * ($_ENV via phpdotenv oder echte Prozess-Umgebung).
 */
function env(string $key, $default = null)
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }
    $value = getenv($key);
    return $value === false ? $default : $value;
}

/**
 * Basis-Pfad der Anwendung (z. B. "" am Web-Root oder "/Prio"
 * in einem Unterverzeichnis). Über APP_BASE_PATH konfigurierbar,
 * damit keine Pfade fest verdrahtet werden müssen.
 */
function base_path(): string
{
    return rtrim((string) env('APP_BASE_PATH', ''), '/');
}

/**
 * Baut eine anwendungsinterne URL relativ zum konfigurierten Basis-Pfad.
 */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = base_path();
    return $base === '' ? '/' . $path : $base . '/' . $path;
}

/**
 * Baut eine URL zu einer statischen Datei (public/, images/ usw.).
 */
function asset(string $path): string
{
    return url($path);
}

/**
 * Stellt eine Verbindung zur Datenbank her und gibt die
 * Datenbankverbindung als PDO zurück.
 */
$dbInstance = null;

function db(): PDO
{
    global $dbInstance;

    if ($dbInstance) {
        return $dbInstance;
    }

    try {
        $dbInstance = new PDO('mysql:host=127.0.0.1;dbname=' . $db['name'], $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ]);
    } catch (PDOException $e) {
        die('Keine Verbindung zur Datenbank möglich: ' . $e->getMessage());
    }
}