<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user is ALREADY logged in, they shouldn't be here
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}
?>
