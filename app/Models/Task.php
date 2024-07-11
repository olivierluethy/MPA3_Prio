<?php
class Task
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	public function getAllTasks(){
		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE fk_BenutzerId = :id ORDER BY prioritaet');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	/* For OPEN TASKS */
	public function getAllTasksOpen(){
		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE fk_BenutzerId = :id AND status = 0
		ORDER BY prioritaet DESC');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	/* For DONE TASKS */
	public function getAllTasksDone(){
		$statement = $this->db->prepare('SELECT * FROM aufgabe WHERE fk_BenutzerId = :id AND status = 1
		ORDER BY prioritaet DESC');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	/* To add one task */
	public function add_task($titel, $beschreibung, $motivation, $deadline, $prioritaet) {
		// Sanitize inputs
		$titel = htmlspecialchars($titel);
		$beschreibung = htmlspecialchars($beschreibung);
		$motivation = htmlspecialchars($motivation);
		$deadline = htmlspecialchars($deadline);
		$prioritaet = intval($prioritaet); // Make sure priority is an integer
	
		// Count the total number of tasks
		$countStatement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$countStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$countStatement->execute();
		$result = $countStatement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);
	
		// Check if the new priority is within the allowed range (1 to totalTasks + 1)
		if ($prioritaet >= 1 && $prioritaet <= $totalTasks + 1) {
			$statement = $this->db->prepare("INSERT INTO `aufgabe` (titel, beschreibung, motivation, deadline, prioritaet, fk_benutzerId) VALUES (:titel, :beschreibung, :motivation, :deadline, :prioritaet, :id)");
			$statement->bindParam(':titel', $titel, PDO::PARAM_STR);
			$statement->bindParam(':beschreibung', $beschreibung, PDO::PARAM_STR);
			$statement->bindParam(':motivation', $motivation, PDO::PARAM_STR);
			$statement->bindParam(':deadline', $deadline, PDO::PARAM_STR);
			$statement->bindParam(':prioritaet', $prioritaet, PDO::PARAM_INT);
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
	public function edit_task($titel, $beschreibung, $motivation, $deadline, $prioritaet, $id){
		$titel = htmlspecialchars($titel);
		$beschreibung = htmlspecialchars($beschreibung);
		$motivation = htmlspecialchars($motivation);
		$deadline = htmlspecialchars($deadline);
		$prioritaet = htmlspecialchars($prioritaet);
		$id = htmlspecialchars($id);

		// Count the total number of tasks
		$countStatement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$countStatement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$countStatement->execute();
		$result = $countStatement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);

		// Check if the new priority is within the allowed range
		if ($prioritaet >= 1 && $prioritaet <= $totalTasks + 1) {
			$statement = $this->db->prepare('UPDATE aufgabe SET titel = :titel, beschreibung = :beschreibung, motivation = :motivation, deadline = :deadline, prioritaet = :prioritaet WHERE aufgabeId = :id');
			$statement->bindParam(':titel', $titel, PDO::PARAM_STR);
			$statement->bindParam(':beschreibung', $beschreibung, PDO::PARAM_STR);
			$statement->bindParam(':motivation', $motivation, PDO::PARAM_STR);
			$statement->bindParam(':deadline', $deadline, PDO::PARAM_STR);
			$statement->bindParam(':prioritaet', $prioritaet, PDO::PARAM_STR);
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
	public function complete_task($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('UPDATE aufgabe SET status = 1 WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();

		$statement2 = $this->db->prepare('UPDATE benutzer SET mangelpunkte = mangelpunkte - 1 WHERE benutzerId = :id');
		$statement2->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$statement2->execute();
	}

	public function complete_task_past($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('UPDATE aufgabe SET status = 1 WHERE aufgabeId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();

		$statement2 = $this->db->prepare('UPDATE benutzer SET mangelpunkte = mangelpunkte + 1 WHERE benutzerId = :id');
		$statement2->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$statement2->execute();
	}

	public function getDeadtime($id){
		$id = htmlspecialchars($id);

		$statement = $this->db->prepare('SELECT deadline FROM aufgabe WHERE aufgabeId = :task AND fk_BenutzerId = :id');
		$statement->bindParam(':task', $id, PDO::PARAM_STR);
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	/* To set a task a higher priority */
	public function higherPrio($task){
		$task = htmlspecialchars($task);
	
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
		$currentPrio = $prioStatement->fetch(PDO::FETCH_ASSOC)['prioritaet'];
	
		// Erhöht die Priorität nur, wenn sie innerhalb der erlaubten Grenze bleibt
		if ($currentPrio < $totalTasks) {
			$updateStatement = $this->db->prepare('UPDATE aufgabe SET prioritaet = prioritaet + 1 WHERE aufgabeId = :task');
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
	public function lowerPrio($task){
		$task = htmlspecialchars($task);
	
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
		$currentPrio = $prioStatement->fetch(PDO::FETCH_ASSOC)['prioritaet'];
	
		// Senkt die Priorität nur, wenn sie größer als 1 ist
		if ($currentPrio > 1) {
			$updateStatement = $this->db->prepare('UPDATE aufgabe SET prioritaet = prioritaet - 1 WHERE aufgabeId = :task');
			$updateStatement->bindParam(':task', $task, PDO::PARAM_STR);
			$updateStatement->execute();

			header('Location: home');
		} else {?>
			<script>alert('The priority cannot be lowered because it has already reached the lowest number of <?= $totalTasks ?> tasks.'); window.location.href = 'home';</script>
			<?php
		}
	}	

	/* To get the amount of deficiency points */
	public function getDeficiencyPoints(){
		$statement = $this->db->prepare('SELECT mangelpunkte FROM benutzer WHERE benutzerId = :id AND role = 0');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	/* If user has 10 deficiency points he gets no access */
	public function lowerRole(){
		$statement = $this->db->prepare('UPDATE benutzer SET role = 2 WHERE benutzerId = :id');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
	}

	// Sort Algorithm
    public function sortTask($sort_option){
		$sort_option = htmlspecialchars($sort_option);

        $statement = $this->db->prepare("SELECT * FROM aufgabe WHERE fk_benutzerId = :benutzerId AND status = 0 ORDER BY $sort_option");
        $statement->bindParam(':benutzerId', $_SESSION["id"], PDO::PARAM_INT);
		/* Bind Param fügt alles mit zusätzlichen Gänsefüschen zu, um SQL-Injection zu verhindern "" */
        $statement->execute();
        return $statement;
    }

	// Search for possible priorities
	public function showPossiblePriorities() {
		$statement = $this->db->prepare('SELECT COUNT(*) as totalTasks FROM aufgabe WHERE status = 0 AND fk_benutzerId = :id');
		$statement->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		$totalTasks = intval($result['totalTasks']);
	
		// Generate an array of possible priorities from 1 to totalTasks + 1
		$possiblePriorities = range(1, $totalTasks + 1);
	
		return $possiblePriorities;
	}	
}