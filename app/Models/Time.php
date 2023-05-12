<?php
class Time
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	public function addTimeRecord($rapport, $time, $id){
		// Set the encryption method
		$encryption_method = "AES-256-CBC";

		// Set the secret key and iv
		$secret_key = 'my_secret_key';
		$secret_iv = 'my_secret_iv';

		// Hash the secret key and iv
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		// Encrypt the rapport
		$encrypted_rapport = openssl_encrypt($rapport, $encryption_method, $key, 0, $iv);
		$encrypted_rapport = base64_encode($encrypted_rapport);

		// Encrypt the rapport
		$encrypted_time = openssl_encrypt($time, $encryption_method, $key, 0, $iv);
		$encrypted_time = base64_encode($encrypted_time);

		$statement = $this->db->prepare('INSERT INTO `rapport` (rapport, zeit, fk_aufgabeId) VALUES (:rapport, :zeit, :fk_aufgabeId)');
		$statement->bindParam(':rapport', $encrypted_rapport, PDO::PARAM_STR);
		$statement->bindParam(':zeit', $encrypted_time, PDO::PARAM_STR);
		$statement->bindParam(':fk_aufgabeId', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	/* Get title of task */
	public function getTitleOfTask(){
		$statement = $this->db->prepare('SELECT DISTINCT titel, aufgabeId FROM aufgabe WHERE fk_benutzerId = :id');
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

	public function delete_time($id){
		$statement = $this->db->prepare('DELETE FROM `rapport` WHERE rapportId = :id');
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $statement->execute();
	}

	public function getRapport($id){
		$statement = $this->db->prepare('SELECT * FROM rapport WHERE rapportId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	public function edit_time($id, $rapport, $time){
		$statement = $this->db->prepare('UPDATE rapport SET rapport = :rapport, zeit = :time WHERE rapportId = :id');
		$statement->bindParam(':rapport', $rapport, PDO::PARAM_STR);
		$statement->bindParam(':time', $time, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	public function getHistorys($id){
		$statement = $this->db->prepare('SELECT aufgabe.titel, rapport.rapport, rapport.zeit, rapport.created_at FROM rapport 
		INNER JOIN aufgabe ON aufgabe.aufgabeId = rapport.fk_aufgabeId
		WHERE fk_aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}
}