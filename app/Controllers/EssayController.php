<?php

class EssayController
{
	public function admin()
	{	
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}
		/* Admin */
		if($_SESSION['role'] == 1){
			$Essay = new Essay();

			/* Get all essays */
			$getEssays = $Essay -> getEssays() -> fetchAll();

			require 'app/Views/admin.view.php';	
		}else {
			header('Location: login');
		}
	}

	public function essay(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Essay = new Essay();

		$id = $_GET['id'];

		/* Get essay */
		$getEssay = $Essay -> getEssay($id) -> fetchAll();;

		if(count($getEssay) == 0){
			header('Location: login');
		}else{
			require 'app/Views/essay.view.php';
		}
	}

	public function add_essay(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Essay = new Essay();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$title = e(post('title'));
			$essay = e(post('essay'));

            $Essay->add_essay($title, $essay);

            header('Location: home');
        }
		require 'app/Views/addTask.view.php';
	}

	public function accept(){
		// Initialize the session
		session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$essayId = $_GET['essayId'];
		$userId = $_GET['userId'];

		$Essay = new Essay();

		$Essay->accept_essay($essayId, $userId);

		header('Location: admin');
	}

	public function refuse(){
		// Initialize the session
		session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$essayId = $_GET['essayId'];
		$userId = $_GET['userId'];

		$Essay = new Essay();

		$Essay->refuse_essay($essayId, $userId);

		header('Location: admin');
	}
}