<?php
use Dotenv\Dotenv;
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
		$Task = new Task();
		
		$salt = $Task->getSalt();

		// Load environment variables
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();

		// Get encryption key from environment
		$encryption_key = getenv('ENCRYPTION_KEY');

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

		$Task = new Task();
		$salt = $Task->getSalt();

        // Funktion zur Entschlüsselung
		function decrypt($data, $key, $iv) {
			$decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
			if ($decrypted === false) {
				return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
			}
			return $decrypted;
		}

		// Load environment variables
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();

		// Get encryption key from environment
		$encryption_key = getenv('ENCRYPTION_KEY');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$rapport = e(post('rapport'));
			$time = e(post('time'));

            $Time->edit_time($id, $rapport, $time);

            header('Location: zeituebersicht');
        }
		/* Needed data to show data that can be changed */
		else{
            $getRapport = $Time->getRapport($id);

			$iv = base64_decode($getRapport['iv']);

			$rapport = htmlspecialchars(decrypt($getRapport['rapport'], $encryption_key, $iv));
			$zeit = decrypt($getRapport['zeit'], $encryption_key, $iv);

			require 'app/Views/editTime.view.php';
        }
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

	/* Export a professional PDF time report for one task (owner only). */
	public function export_pdf(){
		// Initialize the session
		session_start();

		if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
			header('Location: login');
			exit();
		}

		if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
			http_response_code(400);
			exit('Invalid task id.');
		}
		$id = (int) $_GET['id'];

		// Load environment + encryption key
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');

		$Task = new Task();
		$Time = new Time();

		// Authorization: the task must belong to the logged-in user
		$task = $Task->getOwnedTask($id, $_SESSION['id']);
		if (!$task) {
			http_response_code(403);
			exit('You are not allowed to export this task.');
		}

		// Decrypt task fields
		$ivTask = base64_decode($task['iv']);
		$title       = decode_all($Task->decrypt($task['titel'], $encryption_key, $ivTask));
		$description = decode_all($Task->decrypt($task['beschreibung'], $encryption_key, $ivTask));
		$priority    = decode_all($Task->decrypt($task['prioritaet'], $encryption_key, $ivTask));
		$statusRaw   = $Task->decrypt($task['status'], $encryption_key, $ivTask);
		$status      = ($statusRaw === '1') ? 'Completed' : 'Open';
		$createdRaw  = $Task->decrypt($task['created_at'], $encryption_key, $ivTask);
		$deadlineRaw = $Task->decrypt($task['deadline'], $encryption_key, $ivTask);
		$createdLabel  = $createdRaw ? date('d M Y', strtotime($createdRaw)) : '—';
		$deadlineLabel = $deadlineRaw ? date('d M Y', strtotime($deadlineRaw)) : '—';

		// Decrypt + collect time entries, sorted chronologically
		$entries  = [];
		$totalSec = 0;
		foreach ($Time->getRapportsForTask($id) as $rapport) {
			$ivR   = base64_decode($rapport['iv']);
			$zeit  = $Task->decrypt($rapport['zeit'], $encryption_key, $ivR) ?: '00:00:00';
			$text  = decode_all($Task->decrypt($rapport['rapport'], $encryption_key, $ivR));
			$cr    = $Task->decrypt($rapport['created_at'], $encryption_key, $ivR);
			$ts    = $cr ? strtotime($cr) : 0;
			$totalSec += max(0, strtotime($zeit) - strtotime('00:00:00'));
			$entries[] = ['ts' => $ts, 'date' => $ts ? date('d M Y', $ts) : '—', 'seconds' => max(0, strtotime($zeit) - strtotime('00:00:00')), 'duration' => $zeit, 'text' => $text];
		}
		usort($entries, fn($a, $b) => $a['ts'] <=> $b['ts']);

		$entryCount = count($entries);
		$totalH = intval($totalSec / 3600);
		$totalM = intval(($totalSec % 3600) / 60);
		$genDate = date('d M Y, H:i');

		// Description is included by default; ?description=0 produces a clean
		// "summary only" report for HR / billing.
		$includeDescription = !(isset($_GET['description']) && $_GET['description'] === '0');

		// Render the report HTML
		ob_start();
		require __DIR__ . '/../Views/pdf/timeReport.view.php';
		$html = ob_get_clean();

		// Generate the PDF
		$options = new \Dompdf\Options();
		$options->set('defaultFont', 'DejaVu Sans');
		$options->set('isRemoteEnabled', false);
		$dompdf = new \Dompdf\Dompdf($options);
		$dompdf->loadHtml($html, 'UTF-8');
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();

		// Footer page numbers
		$canvas = $dompdf->getCanvas();
		$font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
		$canvas->page_text(270, 810, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 8, [0.5, 0.5, 0.5]);

		$filename = 'time-report-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $title ?: ('task-' . $id))
			. ($includeDescription ? '' : '-summary') . '.pdf';
		$dompdf->stream($filename, ['Attachment' => true]);
		exit();
	}
}