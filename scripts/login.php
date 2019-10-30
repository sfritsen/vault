<?php
/**
 * LOGIN
 * 
 * Handles all login related tasks
 */
session_start();

// Security check for direct browser request
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die("This page must have gotten lost in the Matrix");
}

include dirname(__DIR__).'/scripts/db.php';

$action = $_POST['action'];
$username = $_POST['username'];
$password = $_POST['password'];

// Encrypt password
$pwd_encrypted = md5($password);

// Create security key
$security_key = md5($username.$password.date("U"));

try {

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Bunk connection: ".$conn->connect_error);
    }

    // Login user
    if ($action === 'login') {
        $sql = "
            SELECT account_id, username, security_key
            FROM accounts
            WHERE username = '$username'
            AND password = '$pwd_encrypted'
        ";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($result->num_rows > 0) {

            // Set session data
            session_regenerate_id();
            $_SESSION['session_id']     = session_id();
            $_SESSION['last_activity']  = time();
            $_SESSION['is_logged_in']   = true;
            $_SESSION['account_id']     = $row['account_id'];
            $_SESSION['username']       = $row['username'];
            $_SESSION['security_key']   = $row['security_key'];

            echo "good";

        } else {

            // As a safety precaution, destroy all session data
            session_unset(); 
            session_destroy(); 
            echo "Username and password do not match or no account is found";

        }
    }

    // Account check and creation
    if ($action === 'create') {
        $chk_login = "
            SELECT username
            FROM accounts
            WHERE username = '$username'
        ";
        $result = mysqli_query($conn, $chk_login);

        if ($result->num_rows > 0) {
            echo "Account already exists with username ".$username;
        } else {
            $sql = "
                INSERT INTO accounts (
                    username,
                    password,
                    security_key
                ) VALUES (
                    '$username',
                    '$pwd_encrypted',
                    '$security_key'
                )
            ";
            mysqli_query($conn, $sql);

            echo "Account created";
        }
    }

    $conn->close();

} catch (mysqli_sql_exception $e) {
    echo "<pre>".$e."</pre>";
}