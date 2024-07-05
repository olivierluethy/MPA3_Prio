<?php

class TimeController
{
	public function addTimeRecord(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Time = new Time();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$rapport = e(post('rapport'));
			$time = e(post('time'));
			$id = e(post('taskId'));

			$Time->addTimeRecord($rapport, $time, $id);

			header('Location: home');
		}
	}

	public function zeituebersicht(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: about');
		}

		$Time = new Time();

		/* Get title and id of task */
		$getTitleOfTask = $Time -> getTitleOfTask() -> fetchAll(); // title, aufgabeId

		/* Get all rapports for that title from that task */
		$getRapports = $Time -> getRapports() -> fetchAll(); // get all rapports

		require 'app/Views/zeituebersicht.view.php';
	}

	public function delete_time(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Time = new Time();

		$id = $_GET['id'];

		$Time->delete_time($id);
        
        header('Location: zeituebersicht');
	}

	public function edit_time(){
		$Time = new Time();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

        $title = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$rapport = e(post('rapport'));
			$time = e(post('time'));

            $Time->edit_time($id, $rapport, $time);

            header('Location: http://localhost/Prio/zeituebersicht');
        }
		/* Needed data to show data that can be changed */
		else{
            $getRapport = $Time->getRapport($id) -> fetchAll();;
        }
        require 'app/Views/editTime.view.php';
	}

	public function showHistory(){
		$Time = new Time();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

		/* Get all rapports from task */
		$getHistorys = $Time -> getHistorys($id)-> fetchAll();

		$totaltime = 0;
        $sum = strtotime('00:00:00');
		// Calculate total time used for this task
		foreach($getHistorys as $getHistory){
			// Converting the time into seconds
            $timeinsec = strtotime($getHistory['zeit']) - $sum;

            // Sum the time with previous value
            $totaltime = $totaltime + $timeinsec;
		}

		$h = intval($totaltime / 3600);
		if ($h < 10)
		{
			$h = "0" . $h;
		}

		$totaltime = $totaltime - ($h * 3600);

		// Minutes is obtained by dividing
		// remaining total time with 60
		$m = intval($totaltime / 60);
		if ($m < 10)
		{
			$m = "0" . $m;
		}

		// Remaining value is seconds
		$s = $totaltime - ($m * 60);
		if ($s < 10)
		{
			$s = "0" . $s;
		}
		// Converting the time into seconds
		$timeinsec = strtotime($getHistory['zeit']) - $sum;

		// Sum the time with previous value
		$totaltime = $totaltime + $timeinsec;

		$totaltime = "$h:$m:$s";

		require 'app/Views/history.view.php';
	}

	public function formatTimeOutput($h, $m, $s){
		$output = "";
		if($h == 1){
			$output .= "$h Hour ";
		}else if($h > 1){
			$output .= "$h Hours ";
		}
		if($m == 1){
			$output .= "$m Minute ";
		}else if($m > 1){
			$output .= "$m Minutes ";
		}
		if($s == 1){
			$output .= "$s Second ";
		}else if($s > 1){
			$output .= "$s Seconds ";
		}
		
		echo $output;
	}
}