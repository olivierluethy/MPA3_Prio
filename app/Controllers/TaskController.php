<?php
use Dotenv\Dotenv;

class TaskController
{
	public function home() {
		try {
			// Initialize the session
			session_start();
	
			// Check if user is not logged in
			if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
				header('Location: about');
				exit;
			}
	
			// Check if user has a role
			if (isset($_SESSION["role"])) {
				$Task = new Task();
				$salt = $Task->getSalt();
	
				// Check if salt is retrieved successfully
				if ($salt !== null) {
					$role_hash = hash_hmac('sha256', 1, $salt);
	
					// Load environment variables
					require_once __DIR__ . '/../../vendor/autoload.php';
					$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
					$dotenv->load();
	
					// Get encryption key from environment
					$encryption_key = getenv('ENCRYPTION_KEY');
	
					// Check if user is an admin
					if ($_SESSION['role'] == $role_hash) {
						header("Location: admin");
						exit;
					} else {
						// Default sorting option
						$sort_option = "prioritaet DESC";
	
						// Check and set sorting options from GET parameter
						if (isset($_GET["sort"])) {
							switch ($_GET["sort"]) {
								case "alphabet":
									$sort_option = "titel, beschreibung, motivation";
									break;
								case "priority":
									$sort_option = "prioritaet ASC";
									break;
								case "deadline":
									$sort_option = "deadline ASC";
									break;
								default:
									// Handle invalid sort options gracefully
									throw new Exception("Invalid sort option");
							}
						}
	
						// Fetch sorted tasks
						$getObjects = $Task->sortTask($sort_option);
						$getObjects = $getObjects->fetchAll();
					}
				}
			}
	
			// Load the view
			require 'app/Views/home.view.php';
		} catch (Exception $e) {
			// Handle any unexpected exceptions
			// Log the error or redirect to an error page
			echo "Error: " . $e->getMessage();
		}
	}	

	public function about(){
		// Initialize the session
        session_start();

		require 'app/Views/about.view.php';
	}

	public function add_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();
        
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = e(post('title'));
			$beschreibung = e(post('description'));
			$motivation = e(post('motivation'));
			$deadline = e(post('deadline'));
			$prioritaet = e(post('priority'));

            $Task->add_task($titel, $beschreibung, $motivation, $deadline, $prioritaet);
        } else{
			$possiblePriorities = $Task->ShowPossiblePriorities();
		}
		require 'app/Views/addTask.view.php';
	}

	public function edit_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$id = $_GET['id'];

		$Task = new Task();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = e(post('title'));
			$beschreibung = e(post('description'));
			$motivation = e(post('motivation'));
			$deadline = e(post('deadline'));
			$prioritaet = e(post('priority'));
        
			$Task->edit_task($titel, $beschreibung, $motivation, $deadline, $prioritaet, $id);
        }else{
			/* Get Data to edit */
			$getTask = $Task -> getTask($id) -> fetchAll();
			$possiblePriorities = $Task->ShowPossiblePriorities();
        }
		require 'app/Views/editTask.view.php';
	}

	public function delete_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();

		$id = $_GET['id'];

		$Task->deleteTask($id);
        
        header('Location: home');
	}

	public function higherPrio(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();

		$id = $_GET['id'];

		$Task->higherPrio($id);
	}

	public function lowerPrio(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();

		$id = $_GET['id'];

		$Task->lowerPrio($id);
	}

	public function complete_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();

		/* Get Data to edit */
		$getDeadtime = $Task -> getDeadtime($_GET['id']) -> fetchAll();;

		if(new DateTime() > new DateTime($getDeadtime[0][0])){
			/* Date is in the past */
			$Task->complete_task_past($_GET['id']);
		}
		else {
			/* Date is NOT in the past */
			$Task->complete_task($_GET['id']);
		}

		/* Get amount of deficiency points */
		$getDeficiencyPoints = $Task -> getDeficiencyPoints() -> fetchAll();

		if($getDeficiencyPoints[0][0] <= 10){
			$Task->lowerRole();
			header('Location: logout');
		}else if($getDeficiencyPoints[0][0] < 10){
			header('Location: home');
		}
	}
}