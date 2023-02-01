<?php
class Time
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	public function addTimeRecord($time, $id){
        // $beschreibung = e(post('beschreibung'));
		$statement = $this->db->prepare('INSERT INTO `zeiteintraege` (zeit, fk_aufgabeId) VALUES (:zeit, :fk_aufgabeId)');
		$statement->bindParam(':zeit', $time, PDO::PARAM_STR);
		$statement->bindParam(':fk_aufgabeId', $id, PDO::PARAM_STR);
		$statement->execute();
	}

	public function getAllTimes(){
		$statement = $this->db->prepare('SELECT aufgabe.titel, aufgabe.beschreibung, zeiteintraege.zeit, zeiteintraege.created_at FROM zeiteintraege 
		INNER JOIN aufgabe ON aufgabe.aufgabeId = zeiteintraege.fk_aufgabeId 
		WHERE aufgabe.fk_benutzerId = :id ORDER BY zeiteintraege.created_at;');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	public function getTimesUnderADay(){
		$statement = $this->db->prepare('SELECT aufgabe.titel, aufgabe.beschreibung, zeiteintraege.zeit, zeiteintraege.created_at, zeiteintraege.zeiteintraegeId FROM zeiteintraege 
		INNER JOIN aufgabe ON aufgabe.aufgabeId = zeiteintraege.fk_aufgabeId 
		WHERE aufgabe.fk_benutzerId = :id AND zeiteintraege.created_at > SUBDATE( NOW(), INTERVAL 24 HOUR) 
		ORDER BY zeiteintraege.created_at;');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	public function getTimesOverADay(){
		$statement = $this->db->prepare('SELECT aufgabe.titel, aufgabe.beschreibung, zeiteintraege.zeit, zeiteintraege.created_at, zeiteintraege.zeiteintraegeId FROM zeiteintraege 
		INNER JOIN aufgabe ON aufgabe.aufgabeId = zeiteintraege.fk_aufgabeId 
		WHERE aufgabe.fk_benutzerId = :id AND zeiteintraege.created_at < SUBDATE( NOW(), INTERVAL 24 HOUR) 
		ORDER BY zeiteintraege.created_at;');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	public function delete_time($id){
		$statement = $this->db->prepare('DELETE FROM `zeiteintraege` WHERE zeiteintraegeId = :id');
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $statement->execute();
	}

	public function getTime($id){
		$statement = $this->db->prepare('SELECT * FROM zeiteintraege WHERE zeiteintraegeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	public function edit_time($id, $time){
		$statement = $this->db->prepare('UPDATE zeiteintraege SET zeit = :time WHERE zeiteintraegeId = :id');
		$statement->bindParam(':time', $time, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
	}
}