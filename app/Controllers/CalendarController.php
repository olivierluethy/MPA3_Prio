<?php
use Dotenv\Dotenv;

class CalendarController
{
    /* Calendar page */
    public function calendar(){
        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header('Location: about');
            exit;
        }

        $Task = new Task();
        $salt = $Task->getSalt();                       // header.php nav needs this
        $possiblePriorities = $Task->ShowPossiblePriorities(); // reused task edit modal

        require 'app/Views/calendar.view.php';
    }

    /* JSON event feed (tasks by deadline + time reports by date), owner-scoped */
    public function events(){
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            http_response_code(401);
            echo json_encode([]);
            exit;
        }

        require_once __DIR__ . '/../../vendor/autoload.php';
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $encryption_key = getenv('ENCRYPTION_KEY');

        $Task = new Task();
        $Time = new Time();
        $events = [];
        $titleById = [];

        // --- Tasks: all-day events on their deadline -------------------------
        foreach ($Task->getTasksForUser() as $t) {
            $iv = base64_decode($t['iv']);
            $title = decode_all($Task->decrypt($t['titel'], $encryption_key, $iv));
            $titleById[$t['aufgabeId']] = $title;

            $date = $this->dateOnly($Task->decrypt($t['deadline'], $encryption_key, $iv));
            if (!$date) continue;

            $isDone = ($Task->decrypt($t['status'], $encryption_key, $iv) === '1');
            $events[] = [
                'id'              => 'task-' . $t['aufgabeId'],
                'title'           => $title,
                'start'           => $date,
                'allDay'          => true,
                'backgroundColor' => $isDone ? '#16a34a' : '#4f46e5',
                'borderColor'     => $isDone ? '#16a34a' : '#4f46e5',
                'extendedProps'   => [
                    'type'        => 'task',
                    'taskId'      => (int) $t['aufgabeId'],
                    'description' => sanitize_html(decode_all($Task->decrypt($t['beschreibung'], $encryption_key, $iv))),
                    'motivation'  => sanitize_html(decode_all($Task->decrypt($t['motivation'], $encryption_key, $iv))),
                    'priority'    => decode_all($Task->decrypt($t['prioritaet'], $encryption_key, $iv)),
                    'deadline'    => $date,
                    'status'      => $isDone ? 'done' : 'open',
                ],
            ];
        }

        // --- Time reports: all-day events on their date ----------------------
        foreach ($Time->getRapportsForUser() as $r) {
            $iv = base64_decode($r['iv']);
            $date = $this->dateOnly($Task->decrypt($r['created_at'], $encryption_key, $iv));
            if (!$date) continue;

            $zeit = $Task->decrypt($r['zeit'], $encryption_key, $iv);
            $seconds = max(0, strtotime($zeit) - strtotime('00:00:00'));
            $taskTitle = $titleById[$r['fk_aufgabeId']] ?? 'Task';

            $events[] = [
                'id'              => 'time-' . $r['rapportId'],
                'title'           => $taskTitle . ' · ' . format_duration($seconds),
                'start'           => $date,
                'allDay'          => true,
                'backgroundColor' => '#0d9488', // teal-600
                'borderColor'     => '#0d9488',
                'extendedProps'   => [
                    'type'      => 'time',
                    'timeId'    => (int) $r['rapportId'],
                    'date'      => $date,
                    'duration'  => $zeit, // raw HH:MM:SS for the reused edit modal
                    'report'    => decode_all($Task->decrypt($r['rapport'], $encryption_key, $iv)),
                    'taskTitle' => $taskTitle,
                ],
            ];
        }

        echo json_encode($events);
        exit;
    }

    private function dateOnly($value){
        if (!$value) return null;
        $ts = strtotime($value);
        return $ts ? date('Y-m-d', $ts) : null;
    }
}
