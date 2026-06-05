<?php
use Dotenv\Dotenv;
class Time
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	public function addTimeRecord($rapport, $time, $id) {
		// Sanitize input
		$rapport = htmlspecialchars($rapport);
		$time = htmlspecialchars($time);
		$id = htmlspecialchars($id);
	
		// Generate an initialization vector (IV)
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		// Load encryption key from the .env file
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Encode IV to base64 for storage
		$iv_base64 = base64_encode($iv);
	
		// Encrypt the data
		$encrypted_rapport = openssl_encrypt($rapport, 'aes-256-cbc', $encryption_key, 0, $iv);
		$encrypted_time = openssl_encrypt($time, 'aes-256-cbc', $encryption_key, 0, $iv);
		$created_at = date('Y-m-d H:i:s'); // Generate the current timestamp
		$encrypted_created_at = openssl_encrypt($created_at, 'aes-256-cbc', $encryption_key, 0, $iv);
	
		// Prepare and execute the SQL statement
		$statement = $this->db->prepare('INSERT INTO `rapport` (rapport, zeit, fk_aufgabeId, iv, created_at) VALUES (:rapport, :zeit, :fk_aufgabeId, :iv, :created_at)');
		$statement->bindParam(':rapport', $encrypted_rapport, PDO::PARAM_STR);
		$statement->bindParam(':zeit', $encrypted_time, PDO::PARAM_STR);
		$statement->bindParam(':fk_aufgabeId', $id, PDO::PARAM_INT);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->bindParam(':created_at', $encrypted_created_at, PDO::PARAM_STR);
		$statement->execute();
	}

	public function delete_time($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('DELETE FROM `rapport` WHERE rapportId = :id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
	}

	public function getRapport($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('SELECT * FROM rapport WHERE rapportId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);  // Rückgabe als assoziatives Array
	}

	/* Get title of task (plus the fields needed for the reporting summary) */
	public function getTitleOfTask(){
		$statement = $this->db->prepare('SELECT DISTINCT titel, aufgabeId, iv, status, prioritaet, beschreibung, deadline, created_at FROM aufgabe WHERE fk_benutzerId = :id');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	/* Get all rapports from that task */
	public function getRapports(){
		$statement = $this->db->prepare('SELECT * FROM rapport ORDER BY created_at DESC');
		$statement->execute();
        return $statement;
	}

	/* Get all rapports belonging to one task (for the PDF export) */
	public function getRapportsForTask($id){
		$statement = $this->db->prepare('SELECT * FROM rapport WHERE fk_aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/* Calendar: all of the user's time reports (join enforces ownership) */
	public function getRapportsForUser(){
		$statement = $this->db->prepare('SELECT r.rapportId, r.rapport, r.zeit, r.start_time, r.end_time, r.created_at, r.iv, r.fk_aufgabeId FROM rapport r JOIN aufgabe a ON a.aufgabeId = r.fk_aufgabeId WHERE a.fk_benutzerId = :id');
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/* Calendar time-grid: set a report's start/end (and derived duration) on a
	   date. Owner-scoped; duration (zeit) and created_at are kept consistent. */
	public function updateStartEnd($id, $date, $start, $end, $rapport = null){
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) return false;
		if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $start)) return false;
		if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $end)) return false;
		if (strlen($start) === 5) $start .= ':00';
		if (strlen($end) === 5) $end .= ':00';

		$dur = strtotime($end) - strtotime($start);
		if ($dur < 0) return false; // end before start
		$zeit = sprintf('%02d:%02d:%02d', intdiv($dur, 3600), intdiv($dur % 3600, 60), $dur % 60);

		// Ownership check + IV
		$stmt = $this->db->prepare('SELECT r.iv FROM rapport r JOIN aufgabe a ON a.aufgabeId = r.fk_aufgabeId WHERE r.rapportId = :id AND a.fk_benutzerId = :uid');
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->bindValue(':uid', $_SESSION['id'], PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) return false;
		$iv = base64_decode($row['iv']);

		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$key = getenv('ENCRYPTION_KEY');

		$encCreated = openssl_encrypt($date . ' ' . $start, 'aes-256-cbc', $key, 0, $iv);
		$encStart   = openssl_encrypt($start, 'aes-256-cbc', $key, 0, $iv);
		$encEnd     = openssl_encrypt($end, 'aes-256-cbc', $key, 0, $iv);
		$encZeit    = openssl_encrypt($zeit, 'aes-256-cbc', $key, 0, $iv);

		if ($rapport !== null) {
			$encRapport = openssl_encrypt(htmlspecialchars($rapport), 'aes-256-cbc', $key, 0, $iv);
			$update = $this->db->prepare('UPDATE rapport SET created_at = :c, start_time = :s, end_time = :e, zeit = :z, rapport = :r WHERE rapportId = :id');
			$update->bindValue(':r', $encRapport, PDO::PARAM_STR);
		} else {
			$update = $this->db->prepare('UPDATE rapport SET created_at = :c, start_time = :s, end_time = :e, zeit = :z WHERE rapportId = :id');
		}
		$update->bindValue(':c', $encCreated, PDO::PARAM_STR);
		$update->bindValue(':s', $encStart, PDO::PARAM_STR);
		$update->bindValue(':e', $encEnd, PDO::PARAM_STR);
		$update->bindValue(':z', $encZeit, PDO::PARAM_STR);
		$update->bindValue(':id', $id, PDO::PARAM_INT);
		return $update->execute();
	}

	/* Calendar: move a time report to a new date, keeping its time-of-day. Owner-scoped. */
	public function updateDate($id, $date){
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
			return false;
		}
		$statement = $this->db->prepare('SELECT r.created_at, r.iv FROM rapport r JOIN aufgabe a ON a.aufgabeId = r.fk_aufgabeId WHERE r.rapportId = :id AND a.fk_benutzerId = :uid');
		$statement->bindValue(':id', $id, PDO::PARAM_INT);
		$statement->bindValue(':uid', $_SESSION['id'], PDO::PARAM_INT);
		$statement->execute();
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$row) {
			return false;
		}
		$iv = base64_decode($row['iv']);

		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');

		$oldCreated = openssl_decrypt($row['created_at'], 'aes-256-cbc', $encryption_key, 0, $iv);
		$timePart = ($oldCreated && strlen($oldCreated) >= 19) ? substr($oldCreated, 11, 8) : '12:00:00';
		$newCreated = $date . ' ' . $timePart;
		$encrypted = openssl_encrypt($newCreated, 'aes-256-cbc', $encryption_key, 0, $iv);

		$update = $this->db->prepare('UPDATE rapport SET created_at = :c WHERE rapportId = :id');
		$update->bindValue(':c', $encrypted, PDO::PARAM_STR);
		$update->bindValue(':id', $id, PDO::PARAM_INT);
		return $update->execute();
	}

	// Edit rapport with it's time
	public function edit_time($id, $rapport, $time) {
		// Eingaben bereinigen
		$id = htmlspecialchars($id);
		$rapport = htmlspecialchars($rapport);
		$time = htmlspecialchars($time);
	
		// Autoload und .env-Datei laden
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
	
		// Verschlüsselungsschlüssel holen
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// IV aus der Datenbank holen
		$statement = $this->db->prepare('SELECT iv FROM rapport WHERE rapportId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
	
		if (!$result) {
			throw new Exception('Rapport not found');
		}
	
		// IV dekodieren
		$iv = base64_decode($result['iv']);
	
		$Task = new Task();
	
		// Daten verschlüsseln
		$encrypted_rapport = $Task->encrypt($rapport, $encryption_key, $iv);
		$encrypted_time = $Task->encrypt($time, $encryption_key, $iv);
	
		// IV wieder base64-kodieren
		$iv_encoded = base64_encode($iv);
	
		// Datenbank-Update
		// Editing the duration directly makes the entry duration-only again (clears any window).
		$statement = $this->db->prepare('UPDATE rapport SET rapport = :rapport, zeit = :time, iv = :iv, start_time = NULL, end_time = NULL WHERE rapportId = :id');
		$statement->bindParam(':rapport', $encrypted_rapport, PDO::PARAM_STR);
		$statement->bindParam(':time', $encrypted_time, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_encoded, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
	}

	public function getHistorys($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('SELECT aufgabe.titel, rapport.rapport, rapport.zeit, rapport.created_at FROM rapport 
		INNER JOIN aufgabe ON aufgabe.aufgabeId = rapport.fk_aufgabeId
		WHERE fk_aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}
}