<?php
use Dotenv\Dotenv;
class Task
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	public function getSalt() {
		$statement = $this->db->prepare('SELECT salt FROM benutzer WHERE benutzerId = :id');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['salt'] : null;
	}	

	// Funktion zur Verschlüsselung
    private function encrypt($data, $key, $iv) {
        return openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    }

	public function decrypt($data, $key, $iv) {
		$decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
		if ($decrypted === false) {
			return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
		}
		return $decrypted;
	}

	/* To add one task */
	public function add_task($titel, $beschreibung, $motivation, $deadline, $prioritaet) {
		// Sanitize inputs
		$titel = htmlspecialchars($titel);
		$beschreibung = htmlspecialchars($beschreibung);
		$motivation = htmlspecialchars($motivation);
		$deadline = htmlspecialchars($deadline);
		$prioritaet = intval($prioritaet);

		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();

		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');

		// IV kodieren, damit es in der Datenbank gespeichert werden kann
		$iv_base64 = base64_encode($iv);
	
		// Count the total number of tasks
		$countStatement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$countStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$countStatement->execute();
		$result = $countStatement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);

		$created_at = date('Y-m-d H:i:s'); // Generate the current timestamp

		// Daten verschlüsseln
		$encrypted_titel = $this->encrypt($titel, $encryption_key, $iv);
		$encrypted_beschreibung = $this->encrypt($beschreibung, $encryption_key, $iv);
		$encrypted_motivation = $this->encrypt($motivation, $encryption_key, $iv);
		$encrypted_deadline = $this->encrypt($deadline, $encryption_key, $iv);
		$encrypted_prioritaet = $this->encrypt($prioritaet, $encryption_key, $iv);
		$encrypted_status = $this->encrypt(0, $encryption_key, $iv);
		$encrypted_created_at = $this->encrypt($created_at, $encryption_key, $iv);
	
		// Check if the new priority is within the allowed range (1 to totalTasks + 1)
		if ($prioritaet >= 1 && $prioritaet <= $totalTasks + 1) {
			$statement = $this->db->prepare("INSERT INTO `aufgabe` (titel, beschreibung, motivation, deadline, prioritaet, status, iv, created_at, fk_benutzerId) VALUES (:titel, :beschreibung, :motivation, :deadline, :prioritaet, :status, :iv, :created_at, :id)");
			$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
			$statement->bindParam(':beschreibung', $encrypted_beschreibung, PDO::PARAM_STR);
			$statement->bindParam(':motivation', $encrypted_motivation, PDO::PARAM_STR);
			$statement->bindParam(':deadline', $encrypted_deadline, PDO::PARAM_STR);
			$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_STR);
			$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
			$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
			$statement->bindParam(':created_at', $encrypted_created_at, PDO::PARAM_STR);
			$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
			$statement->execute();
	
			header('Location: home');
		} else {
			$message = $prioritaet > $totalTasks + 1 ? 
				'The priority cannot be higher than ' . ($totalTasks + 1) . '.' :
				'The priority must be at least 1.';
	
			echo "<script>
					alert('$message');
					window.location.href = 'add_task';
				  </script>";
		}
	}	

	/* To edit a task */
	public function edit_task($titel, $beschreibung, $motivation, $deadline, $prioritaet, $id) {
		$titel = htmlspecialchars($titel);
		$beschreibung = htmlspecialchars($beschreibung);
		$motivation = htmlspecialchars($motivation);
		$deadline = htmlspecialchars($deadline);
		$prioritaet = htmlspecialchars($prioritaet);
		$id = htmlspecialchars($id);
	
		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig
	
		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();
	
		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// IV kodieren, damit es in der Datenbank gespeichert werden kann
		$iv_base64 = base64_encode($iv);
	
		// Count the total number of tasks
		$countStatement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$countStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$countStatement->execute();
		$result = $countStatement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);
	
		// Check if the new priority is within the allowed range
		if ($prioritaet >= 1 && $prioritaet <= $totalTasks + 1) {
			// Daten verschlüsseln
			$encrypted_titel = $this->encrypt($titel, $encryption_key, base64_decode($iv_base64));
			$encrypted_beschreibung = $this->encrypt($beschreibung, $encryption_key, base64_decode($iv_base64));
			$encrypted_motivation = $this->encrypt($motivation, $encryption_key, base64_decode($iv_base64));
			$encrypted_deadline = $this->encrypt($deadline, $encryption_key, base64_decode($iv_base64));
			$encrypted_prioritaet = $this->encrypt($prioritaet, $encryption_key, base64_decode($iv_base64));
	
			$statement = $this->db->prepare('UPDATE aufgabe SET titel = :titel, beschreibung = :beschreibung, motivation = :motivation, deadline = :deadline, prioritaet = :prioritaet, iv = :iv WHERE aufgabeId = :id');
			$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
			$statement->bindParam(':beschreibung', $encrypted_beschreibung, PDO::PARAM_STR);
			$statement->bindParam(':motivation', $encrypted_motivation, PDO::PARAM_STR);
			$statement->bindParam(':deadline', $encrypted_deadline, PDO::PARAM_STR);
			$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_STR);
			$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);
			$statement->execute();
	
			header('Location: home');
		} else {
			$message = $prioritaet > $totalTasks + 1 ? 
				'The priority cannot be higher than ' . ($totalTasks + 1) . '.' :
				'The priority must be at least 1.';
	
			echo "<script>
					alert('$message');
					window.location.href = 'edit_task';
				  </script>";
		}
	}

	/* To delete one task */
	public function deleteTask($id){
		$id = htmlspecialchars($id);
		
		$statement = $this->db->prepare('DELETE FROM `rapport` WHERE fk_aufgabeId = :id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

		$statement2 = $this->db->prepare('DELETE FROM `aufgabe` WHERE aufgabeId = :id');
        $statement2->bindParam(':id', $id, PDO::PARAM_INT);
        $statement2->execute();
	}

	/* To get all informations about a specific task */
	public function getTask($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	/* If task completed on point user receives one minus point */
	public function complete_task($id) {
		$id = htmlspecialchars($id);
		
		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig
		
		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();
		
		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');
		
		// Zuerst IV von der Datenbank holen
		$statement = $this->db->prepare('SELECT iv FROM aufgabe WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		
		if (!$result) {
			throw new Exception('Aufgabe nicht gefunden');
		}
		
		// IV dekodieren
		$iv = base64_decode($result['iv']);
		
		// Status verschlüsseln
		$encrypted_status = $this->encrypt('1', $encryption_key, $iv);
		
		// Update-Statement für Aufgabe mit verschlüsseltem Status
		$statement = $this->db->prepare('UPDATE aufgabe SET status = :status WHERE aufgabeId = :id');
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
	}	

	public function complete_task_past($id) {
		$id = htmlspecialchars($id);
	
		// Initialisierungsvektor (IV) und Verschlüsselungsschlüssel aus der Datenbank oder .env laden
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// IV aus der Datenbank laden
		$ivStatement = $this->db->prepare('SELECT iv FROM aufgabe WHERE aufgabeId = :id');
		$ivStatement->bindParam(':id', $id, PDO::PARAM_INT);
		$ivStatement->execute();
		$iv_row = $ivStatement->fetch(PDO::FETCH_ASSOC);
		$iv = base64_decode($iv_row['iv']);
	
		// Verschlüsselten Statuswert berechnen
		$status = '1';
		$encrypted_status = $this->encrypt($status, $encryption_key, $iv);
	
		// Aufgabe-Status aktualisieren
		$statement = $this->db->prepare('UPDATE aufgabe SET status = :status WHERE aufgabeId = :id');
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();

		// Holen der aktuellen Mangelpunkte und des IV-Werts des Benutzers
		$statement2 = $this->db->prepare('SELECT mangelpunkte, iv FROM benutzer WHERE benutzerId = :id');
		$statement2->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$statement2->execute();
		$user_result = $statement2->fetch(PDO::FETCH_ASSOC);
	
		if (!$user_result) {
			throw new Exception('Benutzer nicht gefunden');
		}
	
		// Entschlüsseln der aktuellen Mangelpunkte
		$iv = base64_decode($user_result['iv']);
		$current_mangelpunkte = (int) $this->decrypt($user_result['mangelpunkte'], $encryption_key, $iv);
	
		// Mangelpunkte erhöhen
		$new_mangelpunkte = $current_mangelpunkte + 1;
	
		// Verschlüsseln der neuen Mangelpunkte
		$encrypted_mangelpunkte = $this->encrypt($new_mangelpunkte, $encryption_key, $iv);
	
		// Aktualisieren der Mangelpunkte in der Datenbank
		$statement2 = $this->db->prepare('UPDATE benutzer SET mangelpunkte = :mangelpunkte WHERE benutzerId = :id');
		$statement2->bindParam(':mangelpunkte', $encrypted_mangelpunkte, PDO::PARAM_STR);
		$statement2->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$statement2->execute();
	}

	public function getDeadtime($id){
		$id = htmlspecialchars($id);
	
		$statement = $this->db->prepare('SELECT deadline, iv FROM aufgabe WHERE aufgabeId = :task AND fk_benutzerId = :id');
		$statement->bindParam(':task', $id, PDO::PARAM_STR);
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
	
		$result = $statement->fetch(PDO::FETCH_ASSOC);
	
		if ($result) {
			$encrypted_deadline = $result['deadline'];
			$iv = $result['iv'];
	
			// Load environment variables
			require_once __DIR__ . '/../../vendor/autoload.php';
			$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
			$dotenv->load();
	
			// Get encryption key from environment
			$encryption_key = getenv('ENCRYPTION_KEY');
	
			$decrypted_deadline = $this->decrypt($encrypted_deadline, $encryption_key, base64_decode($iv));
	
			return $decrypted_deadline;
		}
	
		return null; // Ensure null is returned if no result is found
	}
	
	
	/* To set a task a higher priority */
	public function higherPrio($task) {
		$task = htmlspecialchars($task);
	
		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig
	
		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();
	
		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Holen des IV-Werts aus der Datenbank
		$statement = $this->db->prepare('SELECT iv FROM aufgabe WHERE aufgabeId = :task');
		$statement->bindParam(':task', $task, PDO::PARAM_STR);
		$statement->execute();
		$iv_row = $statement->fetch(PDO::FETCH_ASSOC);
		$iv_base64 = $iv_row['iv'];
	
		// Count the total number of tasks
		$countStatement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$countStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$countStatement->execute();
		$result = $countStatement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);
	
		// Überprüft die aktuelle Priorität der Aufgabe
		$prioStatement = $this->db->prepare('SELECT prioritaet FROM aufgabe WHERE aufgabeId = :task');
		$prioStatement->bindParam(':task', $task, PDO::PARAM_STR);
		$prioStatement->execute();
		$currentPrioEncrypted = $prioStatement->fetch(PDO::FETCH_ASSOC)['prioritaet'];
	
		// Entschlüsseln der Priorität
		$currentPrio = $this->decrypt($currentPrioEncrypted, $encryption_key, base64_decode($iv_base64));
	
		// Erhöht die Priorität nur, wenn sie innerhalb der erlaubten Grenze bleibt
		if ($currentPrio < $totalTasks) {
			$newPrio = $currentPrio + 1;
			$encrypted_newPrio = $this->encrypt($newPrio, $encryption_key, base64_decode($iv_base64));
	
			$updateStatement = $this->db->prepare('UPDATE aufgabe SET prioritaet = :newPrio WHERE aufgabeId = :task');
			$updateStatement->bindParam(':newPrio', $encrypted_newPrio, PDO::PARAM_STR);
			$updateStatement->bindParam(':task', $task, PDO::PARAM_STR);
			$updateStatement->execute();
	
			header('Location: home');
		} else {
			echo "<script>
				alert('The priority cannot be increased because it has already reached the maximum number of $totalTasks tasks.');
				window.location.href = 'home';
			</script>";
		}
	}	

	/* To set a task a lower priority */
	public function lowerPrio($task) {
		$task = htmlspecialchars($task);
	
		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		require_once __DIR__ . '/../../vendor/autoload.php';
	
		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
	
		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Holen des IV-Werts aus der Datenbank
		$statement = $this->db->prepare('SELECT iv FROM aufgabe WHERE aufgabeId = :task');
		$statement->bindParam(':task', $task, PDO::PARAM_STR);
		$statement->execute();
		$iv_row = $statement->fetch(PDO::FETCH_ASSOC);
		$iv_base64 = $iv_row['iv'];
	
		// Count the total number of tasks
		$countStatement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$countStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$countStatement->execute();
		$result = $countStatement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);
	
		// Überprüft die aktuelle Priorität der Aufgabe
		$prioStatement = $this->db->prepare('SELECT prioritaet FROM aufgabe WHERE aufgabeId = :task');
		$prioStatement->bindParam(':task', $task, PDO::PARAM_STR);
		$prioStatement->execute();
		$currentPrioEncrypted = $prioStatement->fetch(PDO::FETCH_ASSOC)['prioritaet'];
	
		// Entschlüsseln der Priorität
		$currentPrio = $this->decrypt($currentPrioEncrypted, $encryption_key, base64_decode($iv_base64));
	
		// Senkt die Priorität nur, wenn sie größer als 1 ist
		if ($currentPrio > 1) {
			$newPrio = $currentPrio - 1;
			$encrypted_newPrio = $this->encrypt($newPrio, $encryption_key, base64_decode($iv_base64));
	
			$updateStatement = $this->db->prepare('UPDATE aufgabe SET prioritaet = :newPrio WHERE aufgabeId = :task');
			$updateStatement->bindParam(':newPrio', $encrypted_newPrio, PDO::PARAM_STR);
			$updateStatement->bindParam(':task', $task, PDO::PARAM_STR);
			$updateStatement->execute();
	
			header('Location: home');
		} else {
			echo "<script>
				alert('The priority cannot be lowered because it has already reached the lowest number of $totalTasks tasks.');
				window.location.href = 'home';
			</script>";
		}
	}	

	/* To get the amount of deficiency points */
	public function getDeficiencyPoints() {
		// Load encryption key from the .env file
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Prepare the SQL statement to get the encrypted role and IV
		$statement = $this->db->prepare('SELECT iv, mangelpunkte FROM benutzer WHERE benutzerId = :id');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
	
		if ($result) {
			$iv = hex2bin($result['iv']); // Assuming IV is stored as a hex string in the database
			$decrypted_mangelpunkte = $this->decrypt($result['mangelpunkte'], $encryption_key, $iv);
	
			// Return the result as an associative array to simulate a result set
			return [
				['mangelpunkte' => (int) $decrypted_mangelpunkte]
			];
		}
	
		throw new Exception('User not found');
	}

	/* If user has 10 deficiency points he gets no access */
	public function lowerRole() {
		// Initialisierungsvektor (IV) generieren
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig
	
		// Laden der .env-Datei
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
		$dotenv->load();
	
		// Hole den Verschlüsselungsschlüssel aus der .env-Datei
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// IV kodieren, damit es in der Datenbank gespeichert werden kann
		$iv_base64 = base64_encode($iv);
	
		// Rolle verschlüsseln
		$role = '2';
		$encrypted_role = $this->encrypt($role, $encryption_key, $iv);
	
		// Update-Statement für benutzer mit verschlüsselter Rolle
		$statement = $this->db->prepare('UPDATE benutzer SET role = :role, iv = :iv WHERE benutzerId = :id');
		$statement->bindParam(':role', $encrypted_role, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
	}

	// Sort Algorithm
    public function sortTask($sort_option){
		$sort_option = htmlspecialchars($sort_option);

        $statement = $this->db->prepare("SELECT * FROM aufgabe WHERE fk_benutzerId = :benutzerId ORDER BY $sort_option");
        $statement->bindParam(':benutzerId', $_SESSION["id"], PDO::PARAM_INT);
		/* Bind Param fügt alles mit zusätzlichen Gänsefüschen zu, um SQL-Injection zu verhindern "" */
        $statement->execute();
        return $statement;
    }

	// Search for possible priorities
	public function showPossiblePriorities() {
		// Initialisierungsvektor (IV) und Verschlüsselungsschlüssel aus der Datenbank oder .env laden
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// IV aus der Datenbank laden
		$ivStatement = $this->db->prepare('SELECT iv FROM aufgabe WHERE fk_benutzerId = :id LIMIT 1');
		$ivStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$ivStatement->execute();
		$iv_row = $ivStatement->fetch(PDO::FETCH_ASSOC);
	
		$iv = null;
		if ($iv_row !== false && !empty($iv_row['iv'])) {
			$iv = base64_decode($iv_row['iv']);
		}
	
		// Verschlüsselten Statuswert berechnen, falls es einen IV gibt
		$encrypted_status = null;
		if ($iv !== null) {
			$status = '0';
			$encrypted_status = $this->encrypt($status, $encryption_key, $iv);
		}
	
		// Anzahl der Aufgaben mit verschlüsseltem Status zählen, wenn es einen verschlüsselten Status gibt
		$totalTasks = 0;
		if ($encrypted_status !== null) {
			$statement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = :status AND fk_benutzerId = :id');
			$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
			$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
	
			if ($result !== false && !empty($result['totalTasks'])) {
				$totalTasks = intval($result['totalTasks']);
			}
		}
	
		// Setze totalTasks auf 1, wenn es keine Aufgaben gibt
		if ($totalTasks === 0) {
			$totalTasks = 1;
		}
	
		// Erzeugen eines Arrays von möglichen Prioritäten von 1 bis totalTasks + 1
		$possiblePriorities = range(1, $totalTasks + 1);
	
		return $possiblePriorities;
	}	
}