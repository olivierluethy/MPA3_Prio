<?php

class LoginController{
    public function login(){
        // Initialize the session
        session_start();
        
        // Check if the user is already logged in, if yes then redirect him to index page
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: about");
            exit;
        }
        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the session.
        session_destroy();
        
        $pdo = connectDatabase();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        require 'app/Views/login/login.view.php';

        // Define variables and initialize with empty values
        $email = $password = "";
        $email_err = $password_err = "";

        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Check if email is empty
            if (empty(trim($_POST["email"]))) {
                $email_err = "Please enter email.";
            } else {
                $email = trim($_POST["email"]);
            }

            // Check if password is empty
            if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter your password.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Validate credentials
            if (empty($email_err) && empty($password_err)) {
                // Prepare a select statement
                $sql = "SELECT benutzerId, email, password, role FROM benutzer WHERE email = :email";
                
                if($stmt = $pdo->prepare($sql)){
                    // Bind variables to the prepared statement as parameters
                    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
                    
                    // Set parameters
                    $email = trim($_POST["email"]);
                    
                    // Attempt to execute the prepared statement
                    if($stmt->execute()){
                        // Check if username exists, if yes then verify password
                        if($stmt->rowCount() == 1){
                            if($row = $stmt->fetch()){
                                $id = $row["benutzerId"];
                                $email = $row["email"];
                                $role = $row["role"];
                                $hashed_password = $row["password"];
                                if(password_verify($password, $hashed_password)){
                                    // Password is correct, so start a new session
                                    session_start();

                                    // Store data in session variables
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["email"] = $email;
                                    $_SESSION["role"] = $role;

                                    // Redirect user to index page
                                    header("location: home");
                                } else {
                                    // $_SESSION["emailDirection"] = $_POST['emailuser'];

                                    // Display an error message if password is not valid
                                    echo 
                                    "<div class='loginPasswordIsWrong'>
                                        <h2>Passwort ungültig</h2>
                                    </div>";
                                }
                            }
                        } else {
                            echo 
                            "<div class='loginPasswordIsWrong'>
                                <h2>Email Adresse wurde nicht gefunden</h2>
                            </div>";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    unset($stmt);
                }
            }
        }
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
                $role = 0; // Assuming role 0 for normal user
                $role_hash = hash_hmac('sha256', $role, $salt);
    
                // Prepare an insert statement
                $sql = "INSERT INTO benutzer (email, password, salt, role) VALUES (:email, :password, :salt, :role)";
                if ($stmt = $pdo->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bindParam(":email", $email_hash, PDO::PARAM_STR);
                    $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                    $stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
                    $stmt->bindParam(":role", $role_hash, PDO::PARAM_STR);
    
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
            // Close connection
            unset($pdo);
        }
    }
    

    /* Damit sich der eingeloggte Benutzer wieder ausloggen kann */
    public function logout(){
        $pdo = connectDatabase();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        require 'app/Views/login/logout.view.php';
    }
}