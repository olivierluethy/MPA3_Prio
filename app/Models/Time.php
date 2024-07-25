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

	/* Get title of task */
	public function getTitleOfTask(){
		$statement = $this->db->prepare('SELECT DISTINCT titel, aufgabeId, iv FROM aufgabe WHERE fk_benutzerId = :id');
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
		$statement = $this->db->prepare('UPDATE rapport SET rapport = :rapport, zeit = :time, iv = :iv WHERE rapportId = :id');
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