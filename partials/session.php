<?php 

session_start();

// Expire the session after supplied time in seconds
$expire = 60 * 60;

if (isset($_SESSION['last_activity'])) {

    // Calculate time since last activity
    $time_inactive = time() - $_SESSION['last_activity'];

    // Check to see if they've been inactive for too long
    if ($time_inactive >= $expire) {
        session_unset();
        session_destroy();
        header('Location: index.php');
    }
}

// Set the activity time
$_SESSION['last_activity'] = time();

if ($_SESSION['is_logged_in'] !== true) {
    header('Location: index.php');
}

?>