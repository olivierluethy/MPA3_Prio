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
 * Dekodiert HTML-Entities vollständig (auch mehrfach kodierte Werte).
 *
 * Hintergrund: Benutzereingaben wurden historisch beim Speichern mehrfach
 * mit htmlspecialchars() kodiert (einmal im Controller via e(), einmal im
 * Model). Diese Funktion stellt den ursprünglichen Rohtext wieder her,
 * indem sie so lange dekodiert, bis sich nichts mehr ändert.
 */
function decode_all(?string $value): string
{
    $value = (string) $value;
    $previous = null;
    $iterations = 0;
    while ($value !== $previous && $iterations < 5) {
        $previous = $value;
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $iterations++;
    }
    return $value;
}

/**
 * Sichere Ausgabe für Klartext-Felder (Titel, Rapport, Zeit ...):
 * vollständig dekodieren und danach GENAU EINMAL escapen.
 * Verhindert Doppelkodierung und bleibt XSS-sicher.
 */
function display_text(?string $value): string
{
    return htmlspecialchars(decode_all($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Ausgabe für Rich-Text-Felder (CKEditor-Inhalte: Beschreibung, Motivation,
 * Essay): vollständig dekodieren und als HTML ausgeben. Der Inhalt stammt
 * aus CKEditor und ist bereits bereinigt.
 */
function display_html(?string $value): string
{
    return decode_all($value);
}

/**
 * Bereinigt gespeichertes Rich-Text-HTML (CKEditor) serverseitig gegen XSS,
 * damit es als formatierte, schreibgeschützte Ausgabe gerendert werden kann.
 * Nutzt symfony/html-sanitizer (W3C-konform): Skripte, Event-Handler,
 * gefährliche URLs usw. werden entfernt; sichere Formatierung bleibt erhalten.
 */
function sanitize_html(?string $value): string
{
    $value = (string) $value;
    if (trim($value) === '') {
        return '';
    }

    static $sanitizer = null;
    if ($sanitizer === null) {
        $config = (new \Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig())
            ->allowSafeElements()                                  // p, ul, ol, li, strong, em, a, h1-6, blockquote, ...
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
            ->forceAttribute('a', 'target', '_blank');
        $sanitizer = new \Symfony\Component\HtmlSanitizer\HtmlSanitizer($config);
    }

    return $sanitizer->sanitize($value);
}

/**
 * Gibt ein modernes Inline-SVG-Icon (Heroicons-Stil) als String zurück.
 * Wird in den Views für Aktions-Buttons (Bearbeiten, Löschen, Priorität,
 * Zeiterfassung) verwendet, damit ein einheitlicher, vektorbasierter
 * Icon-Stil in der ganzen Anwendung genutzt wird.
 */
function icon(string $name, string $class = 'h-5 w-5'): string
{
    $paths = [
        'pencil'       => '<path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>',
        'trash'        => '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>',
        'chevron-up'   => '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"/>',
        'chevron-down' => '<path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>',
        'play'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 0 1 0 1.971l-11.54 6.348a1.125 1.125 0 0 1-1.667-.985V5.653Z"/>',
        'stop'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"/>',
        'clock'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
        'x'            => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>',
        'plus'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>',
        'sparkles'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>',
        'bolt'         => '<path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>',
        'shield'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.285Z"/>',
        'chart'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>',
        'flag'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"/>',
        'calendar'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>',
        'download'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>',
    ];
    $p = $paths[$name] ?? '';
    $cls = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
    return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true" class="' . $cls . '">' . $p . '</svg>';
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