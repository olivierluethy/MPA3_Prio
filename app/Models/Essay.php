<?php
use Dotenv\Dotenv;
class Essay
{
    public $db;

    public function __construct()
    {
        $this->db = connectDatabase();
    }

	/* Get all essays */
	public function getEssays() {
		// Load encryption key from the .env file
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Generate an initialization vector (IV)
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		// Encode IV to base64 for storage
		$iv_base64 = base64_encode($iv);
	
		// Encrypt the status value
		$status = 1;
		$encrypted_status = openssl_encrypt($status, 'aes-256-cbc', $encryption_key, 0, $iv);
	
		// Prepare and execute the SQL statement with the encrypted status
		$statement = $this->db->prepare('SELECT * FROM essays WHERE status = :status AND iv = :iv');
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->execute();
	
		return $statement;
	}	

	/* Get one specific essay */
	public function getEssay($id){
		$id = e($_GET['id']);

		$statement = $this->db->prepare('SELECT * FROM essays WHERE essayId = :id');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();
        return $statement;
	}

	public function add_essay($title, $essay) {
		// Load encryption key from the .env file
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Generate an initialization vector (IV)
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		// Encode IV to base64 for storage
		$iv_base64 = base64_encode($iv);
	
		// Encrypt the title, essay, and status values
		$encrypted_title = openssl_encrypt($title, 'aes-256-cbc', $encryption_key, 0, $iv);
		$encrypted_essay = openssl_encrypt($essay, 'aes-256-cbc', $encryption_key, 0, $iv);
		$status = 1;
		$encrypted_status = openssl_encrypt($status, 'aes-256-cbc', $encryption_key, 0, $iv);
	
		// Prepare and execute the SQL statement with the encrypted values
		$statement = $this->db->prepare("INSERT INTO `essays` (titel, essay, status, iv, fk_benutzerId) VALUES (:titel, :essay, :status, :iv, :id)");
		$statement->bindParam(':titel', $encrypted_title, PDO::PARAM_STR);
		$statement->bindParam(':essay', $encrypted_essay, PDO::PARAM_STR);
		$statement->bindParam(':status', $encrypted_status, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$statement->execute();
	}	

	/* If admin user accepts the essay */
	public function accept_essay($essayId, $userId) {
		$essayId = htmlspecialchars($essayId);
		$userId = htmlspecialchars($userId);
	
		// Load encryption key from the .env file
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Generate an initialization vector (IV)
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		// Encode IV to base64 for storage
		$iv_base64 = base64_encode($iv);
	
		// Encrypt the role and mangelpunkte values
		$role = 0;
		$mangelpunkte = 0;
		$encrypted_role = openssl_encrypt($role, 'aes-256-cbc', $encryption_key, 0, $iv);
		$encrypted_mangelpunkte = openssl_encrypt($mangelpunkte, 'aes-256-cbc', $encryption_key, 0, $iv);
	
		// Prepare and execute the SQL statement to update the user role and mangelpunkte with the encrypted values
		$statement = $this->db->prepare('UPDATE benutzer SET role = :role, mangelpunkte = :mangelpunkte, iv = :iv WHERE benutzerId = :userId');
		$statement->bindParam(':role', $encrypted_role, PDO::PARAM_STR);
		$statement->bindParam(':mangelpunkte', $encrypted_mangelpunkte, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->bindParam(':userId', $userId, PDO::PARAM_STR);
		$statement->execute();
	
		// Prepare and execute the SQL statement to update the essay status
		$statement2 = $this->db->prepare('UPDATE essays SET status = 2 WHERE essayId = :essayId');
		$statement2->bindParam(':essayId', $essayId, PDO::PARAM_STR);
		$statement2->execute();
	}	

	/* If admin user refuses the essay */
	public function refuse_essay($essayId, $userId) {
		$essayId = htmlspecialchars($essayId);
		$userId = htmlspecialchars($userId);
	
		// Load encryption key from the .env file
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();
		$encryption_key = getenv('ENCRYPTION_KEY');
	
		// Generate an initialization vector (IV)
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	
		// Encode IV to base64 for storage
		$iv_base64 = base64_encode($iv);
	
		// Encrypt the role value
		$role = 2;
		$encrypted_role = openssl_encrypt($role, 'aes-256-cbc', $encryption_key, 0, $iv);
	
		// Prepare and execute the SQL statement to update the user role with the encrypted value
		$statement = $this->db->prepare('UPDATE benutzer SET role = :role, iv = :iv WHERE benutzerId = :userId');
		$statement->bindParam(':role', $encrypted_role, PDO::PARAM_STR);
		$statement->bindParam(':iv', $iv_base64, PDO::PARAM_STR);
		$statement->bindParam(':userId', $userId, PDO::PARAM_STR);
		$statement->execute();
	
		// Prepare and execute the SQL statement to update the essay status
		$statement2 = $this->db->prepare('UPDATE essays SET status = 2 WHERE essayId = :essayId');
		$statement2->bindParam(':essayId', $essayId, PDO::PARAM_STR);
		$statement2->execute();
	}	
}