<?php
class Essay
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	/* Get all essays */
	public function getEssays(){
		$statement = $this->db->prepare('SELECT * FROM essays WHERE status = 1');
		$statement->execute();
        return $statement;
	}

	/* Get one specific essay */
	public function getEssay($id){
		$id = e($_GET['id']);

		$statement = $this->db->prepare('SELECT * FROM essays WHERE essayId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_STR);
		$statement->execute();
        return $statement;
	}

	public function add_essay($title, $essay){
		$title = e(post($title));
		$essay = e(post($essay));

		$statement = $this->db->prepare("INSERT INTO `essays` (titel, essay, status, fk_benutzerId) VALUES (:titel, :essay, 1, :id)");
		$statement->bindParam(':titel', $title, PDO::PARAM_STR);
		$statement->bindParam(':essay', $essay, PDO::PARAM_STR);
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
		$statement->execute();
	}

	/* If admin user accepts the essay */
	public function accept_essay($essayId, $userId){
		$essayId = htmlspecialchars($essayId);
		$userId = htmlspecialchars($userId);

		$statement = $this->db->prepare('UPDATE benutzer SET role = 0, mangelpunkte = 0 WHERE benutzerId = :userId');
		$statement->bindParam(':userId', $userId, PDO::PARAM_STR);
		$statement->execute();

		$statement2 = $this->db->prepare('UPDATE essays SET status = 2 WHERE essayId = :essayId');
		$statement2->bindParam(':essayId', $essayId, PDO::PARAM_STR);
		$statement2->execute();
	}

	/* If admin user refuses the essay */
	public function refuse_essay($essayId, $userId){
		$essayId = htmlspecialchars($essayId);
		$userId = htmlspecialchars($userId);

		$statement = $this->db->prepare('UPDATE benutzer SET role = 2 WHERE benutzerId = :userId');
		$statement->bindParam(':userId', $userId, PDO::PARAM_STR);
		$statement->execute();

		$statement2 = $this->db->prepare('UPDATE essays SET status = 2 WHERE essayId = :essayId');
		$statement2->bindParam(':essayId', $essayId, PDO::PARAM_STR);
		$statement2->execute();
	}
}