<?php
class Task
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	public function getAllTasks(){
		$secret_key = 'my_secret_key';
		
		$statement = $this->db->prepare('SELECT 
        aufgabeId, 
        AES_ENCRYPT(titel, :secret_key) as encrypted_titel, 
        AES_ENCRYPT(beschreibung, :secret_key) as encrypted_beschreibung, 
        AES_ENCRYPT(motivation, :secret_key) as encrypted_motivation, 
        AES_ENCRYPT(deadline, :secret_key) as encrypted_deadline, 
        AES_ENCRYPT(prioritaet, :secret_key) as encrypted_prioritaet, 
        status, 
        created_at 
			FROM aufgabe 
			WHERE fk_BenutzerId = :id 
			ORDER BY prioritaet');

		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->bindParam(':secret_key', $secret_key, PDO::PARAM_STR);
		$statement->execute();
		return $statement;
	}

	/* For OPEN TASKS */
	public function getAllTasksOpen(){
		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE fk_BenutzerId = :id AND status = 0
		ORDER BY prioritaet DESC');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	/* For DONE TASKS */
	public function getAllTasksDone(){
		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE fk_BenutzerId = :id AND status = 1
		ORDER BY prioritaet DESC');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	/* To add one task */
	public function add_task($titel, $beschreibung, $motivation, $deadline, $prioritaet){
		// Set the encryption method
		$encryption_method = "AES-256-CBC";

		// Set the secret key and iv
		$secret_key = 'my_secret_key';
		$secret_iv = 'my_secret_iv';

		// Hash the secret key and iv
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		// Encrypt the titel
		$encrypted_titel = openssl_encrypt($titel, $encryption_method, $key, 0, $iv);
		$encrypted_titel = base64_encode($encrypted_titel);

		// Encrypt the beschreibung
		$encrypted_beschreibung = openssl_encrypt($beschreibung, $encryption_method, $key, 0, $iv);
		$encrypted_beschreibung = base64_encode($encrypted_beschreibung);

		// Encrypt the motivation
		$encrypted_motivation = openssl_encrypt($motivation, $encryption_method, $key, 0, $iv);
		$encrypted_motivation = base64_encode($encrypted_motivation);

		// Encrypt the deadline
		$encrypted_deadline = openssl_encrypt($deadline, $encryption_method, $key, 0, $iv);
		$encrypted_deadline = base64_encode($encrypted_deadline);

		// Encrypt the priority
		$encrypted_prioritaet = openssl_encrypt(strval($prioritaet), $encryption_method, $key, 0, $iv);
		$encrypted_prioritaet = base64_encode($encrypted_prioritaet);

		$statement = $this->db->prepare("INSERT INTO `aufgabe` (titel, beschreibung, motivation, deadline, prioritaet, fk_benutzerId) VALUES (:titel, :beschreibung, :motivation, :deadline, :prioritaet, :id)");
		$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
		$statement->bindParam(':beschreibung', $encrypted_beschreibung, PDO::PARAM_STR);
		$statement->bindParam(':motivation', $encrypted_motivation, PDO::PARAM_STR);
		$statement->bindParam(':deadline', $encrypted_deadline, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_INT);
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
	}

	/* To edit a task */
	public function edit_task($titel, $beschreibung, $motivation, $deadline, $prioritaet, $id){
		// Set the encryption method
		$encryption_method = "AES-256-CBC";

		// Set the secret key and iv
		$secret_key = 'my_secret_key';
		$secret_iv = 'my_secret_iv';

		// Hash the secret key and iv
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		// Encrypt the titel
		$encrypted_titel = openssl_encrypt($titel, $encryption_method, $key, 0, $iv);
		$encrypted_titel = base64_encode($encrypted_titel);

		// Encrypt the beschreibung
		$encrypted_beschreibung = openssl_encrypt($beschreibung, $encryption_method, $key, 0, $iv);
		$encrypted_beschreibung = base64_encode($encrypted_beschreibung);

		// Encrypt the motivation
		$encrypted_motivation = openssl_encrypt($motivation, $encryption_method, $key, 0, $iv);
		$encrypted_motivation = base64_encode($encrypted_motivation);

		// Encrypt the deadline
		$encrypted_deadline = openssl_encrypt($deadline, $encryption_method, $key, 0, $iv);
		$encrypted_deadline = base64_encode($encrypted_deadline);

		// Encrypt the priority
		$encrypted_prioritaet = openssl_encrypt(strval($prioritaet), $encryption_method, $key, 0, $iv);
		$encrypted_prioritaet = base64_encode($encrypted_prioritaet);

		$statement = $this->db->prepare('UPDATE aufgabe SET titel = :titel, beschreibung = :beschreibung, motivation = :motivation, deadline = :deadline, prioritaet = :prioritaet WHERE aufgabeId = :id');
		$statement->bindParam(':titel', $encrypted_titel, PDO::PARAM_STR);
		$statement->bindParam(':beschreibung', $encrypted_beschreibung, PDO::PARAM_STR);
		$statement->bindParam(':motivation', $encrypted_motivation, PDO::PARAM_STR);
		$statement->bindParam(':deadline', $encrypted_deadline, PDO::PARAM_STR);
		$statement->bindParam(':prioritaet', $encrypted_prioritaet, PDO::PARAM_INT);
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	/* To delete one task */
	public function deleteTask($id){
		$statement = $this->db->prepare('DELETE FROM `rapport` WHERE fk_aufgabeId = :id');
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $statement->execute();

		$statement2 = $this->db->prepare('DELETE FROM `aufgabe` WHERE aufgabeId = :id');
        $statement2->bindParam(':id', $id, PDO::PARAM_STR);
        $statement2->execute();
	}

	/* To get all informations about a specific task */
	public function getTask($id){
		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	/* If task completed on point user receives one minus point */
	public function complete_task($id){
		$statement = $this->db->prepare('UPDATE aufgabe SET status = 1 WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();

		$statement2 = $this->db->prepare('UPDATE benutzer SET mangelpunkte = mangelpunkte - 1 WHERE benutzerId = :id');
		$statement2->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement2->execute();
	}

	public function complete_task_past($id){
		$statement = $this->db->prepare('UPDATE aufgabe SET status = 1 WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();

		$statement2 = $this->db->prepare('UPDATE benutzer SET mangelpunkte = mangelpunkte + 1 WHERE benutzerId = :id');
		$statement2->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement2->execute();
	}

	public function getDeadtime($id){
		$statement = $this->db->prepare('SELECT deadline FROM aufgabe WHERE aufgabeId = :task AND fk_BenutzerId = :id');
		$statement->bindParam(':task', $id, PDO::PARAM_STR);
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	/* To set a task a higher priority */
	public function higherPrio($task){
		$statement = $this->db->prepare('UPDATE aufgabe SET prioritaet = prioritaet + 1 WHERE aufgabeId = :task');
		$statement->bindParam(':task', $task, PDO::PARAM_STR);
		$statement->execute();
	}

	/* To set a task a lower priority */
	public function lowerPrio($task){
		$statement = $this->db->prepare('UPDATE aufgabe SET prioritaet = prioritaet - 1 WHERE aufgabeId = :task');
		$statement->bindParam(':task', $task, PDO::PARAM_STR);
		$statement->execute();
	}

	/* To get the amount of deficiency points */
	public function getDeficiencyPoints(){
		$statement = $this->db->prepare('SELECT mangelpunkte FROM benutzer WHERE benutzerId = :id AND role = 0');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	/* If user has 10 deficiency points he gets no access */
	public function lowerRole(){
		$statement = $this->db->prepare('UPDATE benutzer SET role = 2 WHERE benutzerId = :id');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
	}

	// Sort Algorithm
    public function sortTask($sort_option){
        $statement = $this->db->prepare("SELECT * FROM aufgabe WHERE fk_benutzerId = :benutzerId AND status = 0 ORDER BY $sort_option");
        $statement->bindParam(':benutzerId', $_SESSION["id"], PDO::PARAM_STR);
		/* Bind Param fügt alles mit zusätzlichen Gänsefüschen zu, um SQL-Injection zu verhindern "" */
        $statement->execute();
        return $statement;
    }
}