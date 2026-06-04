<?php
use Dotenv\Dotenv;
class LoginController{
    public function login() {
        // Initialize the session
        session_start();
    
        // Check if the user is already logged in, if yes then redirect to home page
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: home");
            exit;
        }
    
        // Include config file
        include __DIR__ . '/../../core/db_config.php';
    
        // Define variables and initialize with empty values
        $email = $password = "";
        $email_err = $password_err = "";

        // Unset all of the session variables
        $_SESSION = array();
        // Destroy the session.
        session_destroy();
    
        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if email is empty
            if (empty(trim($_POST["email"]))) {
                $email_err = "Bitte geben Sie eine E-Mail-Adresse ein.";
            } else {
                $email = strtolower(trim($_POST["email"]));
            }
    
            // Check if password is empty
            if (empty(trim($_POST["password"]))) {
                $password_err = "Bitte geben Sie Ihr Passwort ein.";
            } else {
                $password = trim($_POST["password"]);
            }
    
            // Validate credentials
            if (empty($email_err) && empty($password_err)) {
                // Prepare a select statement to get salt and email hash
                $sql = "SELECT benutzerId, email, password, salt, role FROM benutzer";
            
                if ($result = mysqli_query($link, $sql)) {
                    $found = false;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $generated_hash = hash_hmac('sha256', $email, $row['salt']);
            
                        if ($generated_hash === $row['email']) {
                            $found = true;
                            if (password_verify($password, $row['password'])) {
                                // Password is correct, start a new session
                                session_start();
            
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $row['benutzerId'];
                                $_SESSION["email"] = $email;
                                $_SESSION["role"] = $row['role'];
            
                                // Redirect user to home page
                                header("location: home");
                                exit();
                            } else {
                                // Display an error message if password is not valid
                                $password_err = "Das Passwort ist nicht gültig.";
                            }
                        }
                    }
                    if (!$found) {
                        // Display an error message if email doesn't exist
                        $email_err = "Kein Konto mit dieser E-Mail-Adresse gefunden.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            
                // Free result set
                mysqli_free_result($result);
            }            
    
            // Close connection
            mysqli_close($link);
        }
    
        // Load login view with appropriate error messages
        require 'app/Views/login/login.view.php';
    }

    // Funktion zur Verschlüsselung
    private function encrypt($data, $key, $iv) {
        return openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    }

    public function register() {
        // Initialize the session
        session_start();
    
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Check if the user is already logged in, if yes then redirect him to index page
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: home");
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("location: login");
            exit;
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Define variables and initialize with empty values
            $email = $password = $confirm_password = "";
            $email_err = $password_err = $confirm_password_err = "";
    
            // Validate email
            if (empty(trim($_POST["email"]))) {
                $email_err = "Bitte geben Sie eine E-Mail-Adresse ein.";
            } else {
                $email = strtolower(trim($_POST["email"])); // Email to lowercase
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_err = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
                } else {
                    // Check if email already exists
                    $sql = "SELECT email, salt FROM benutzer";
                    if ($result = $pdo->query($sql)) {
                        $email_exists = false;
                        while ($row = $result->fetch()) {
                            $stored_email_hash = $row['email'];
                            $stored_salt = $row['salt'];
                            $check_email_hash = hash_hmac('sha256', $email, $stored_salt);
                            if ($stored_email_hash === $check_email_hash) {
                                $email_exists = true;
                                break;
                            }
                        }
    
                        if ($email_exists) {
                            $email_err = "Diese E-Mail-Adresse ist bereits vergeben.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
            }
    
            // Validate password
            if (empty(trim($_POST["password"]))) {
                $password_err = "Bitte geben Sie ein Passwort ein.";
            } elseif (strlen(trim($_POST["password"])) < 6) {
                $password_err = "Das Passwort muss mindestens 6 Zeichen haben.";
            } else {
                $password = trim($_POST["password"]);
            }
    
            // Validate confirm password
            if (empty(trim($_POST["verypass"]))) {
                $confirm_password_err = "Bitte bestätigen Sie das Passwort.";
            } else {
                $confirm_password = trim($_POST["verypass"]);
                if (empty($password_err) && ($password != $confirm_password)) {
                    $confirm_password_err = "Die Passwörter stimmen nicht überein.";
                }
            }
    
            // Check input errors before inserting in database
            if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
                // Generate salt
                $salt = bin2hex(random_bytes(16)); // 16 bytes = 128 bits
                // Hash the email with the salt
                $email_hash = hash_hmac('sha256', $email, $salt);
                // Hash the password
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                // Hash the role
                $role = 0;
                $role_hash = hash_hmac('sha256', $role, $salt);
                
                // Initialisierungsvektor (IV) generieren
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            
                require_once __DIR__ . '/../../vendor/autoload.php'; // Pfad anpassen, falls notwendig

                // Laden der .env-Datei
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Pfad anpassen, falls notwendig
                $dotenv->load();

                // Hole den Verschlüsselungsschlüssel aus der .env-Datei
                $encryption_key = getenv('ENCRYPTION_KEY');

                // IV kodieren, damit es in der Datenbank gespeichert werden kann
                $iv_base64 = base64_encode($iv);

                $encrypted_mangelpunkte = $this->encrypt(0, $encryption_key, $iv);
    
                // Prepare an insert statement
                $sql = "INSERT INTO benutzer (email, password, salt, mangelpunkte, role, iv) VALUES (:email, :password, :salt, :mangelpunkte, :role, :iv)";
                if ($stmt = $pdo->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bindParam(":email", $email_hash, PDO::PARAM_STR);
                    $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                    $stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
                    $stmt->bindParam(":mangelpunkte", $encrypted_mangelpunkte, PDO::PARAM_STR);
                    $stmt->bindParam(":role", $role_hash, PDO::PARAM_STR);
                    $stmt->bindParam(":iv", $iv_base64, PDO::PARAM_STR);
    
                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Prepare a select statement
                        $sql = "SELECT benutzerId, email, password, role FROM benutzer WHERE email = :email";
                        if ($stmt = $pdo->prepare($sql)) {
                            // Bind variables to the prepared statement as parameters
                            $stmt->bindParam(":email", $email_hash, PDO::PARAM_STR);
    
                            // Attempt to execute the prepared statement
                            if ($stmt->execute()) {
                                // After register is successful, auto login
                                if ($stmt->rowCount() == 1) {
                                    if ($row = $stmt->fetch()) {
                                        $id = $row["benutzerId"];
                                        $email = $row["email"];
                                        $hashed_password = $row["password"];
                                        $role = $row["role"];
                                        if (password_verify($password, $hashed_password)) {
                                            // Unset all of the session variables
                                            $_SESSION = array();
                                            // Destroy the session.
                                            session_destroy();
                                            // Password is correct, so start a new session
                                            session_start();
    
                                            // Store data in session variables
                                            $_SESSION["loggedin"] = true;
                                            $_SESSION["id"] = $id;
                                            $_SESSION["email"] = $email;
                                            $_SESSION["role"] = $role;
    
                                            // Redirect user to index page
                                            header("location: home");
                                            exit;
                                        }
                                    }
                                } else {
                                    // Display an error message if email doesn't exist
                                    echo "<h2>No account found with that email.</h2>";
                                    echo "<a href='loginRegister'><button class='back'>Try again</button></a>";
                                }
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
    
                            // Close statement
                            unset($stmt);
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    // Close statement
                    unset($stmt);
                }
            }

            // If we reach this point, registration did not succeed (the success
            // path redirects and exits above). Re-render the form WITH the error
            // messages and open the Register tab, instead of a blank page.
            unset($pdo);
            $active_form = 'register';
            require 'app/Views/login/login.view.php';
            exit;
        }
    }
    

    /* Damit sich der eingeloggte Benutzer wieder ausloggen kann */
    public function logout(){
        $pdo = connectDatabase();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        require 'app/Views/login/logout.view.php';
    }
}