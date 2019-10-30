<?php
session_start();

echo '<pre>';

// Export all session data
print_r($_SESSION);

// Must use the same value in session_left as set in session.php
// for proper timeout calculation
$session_time = (time() - $_SESSION['last_activity']) / 60;
$session_left = 60 - $session_time;
echo '<p>Session expiring in '.round($session_left, 2).' min</p>';

echo '</pre>';