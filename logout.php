<?php
session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. Security Step: Kill the actual cookie on the browser
// This ensures the old session ID cannot be reused by hackers.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Redirect to Homepage (Better User Experience)
header("Location: index.php");
exit();
?>