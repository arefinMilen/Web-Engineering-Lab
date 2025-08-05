<?php
session_start();

// Destroy all session data
session_destroy();

// Clear session variables
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Redirect to admin login
header('Location: admin_login.php');
exit;
?>