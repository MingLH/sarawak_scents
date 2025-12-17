<?php
session_start();

//to prevent user/admin from accesing page when they're not logging in
if (isset($_SESSION['user_id'])) {
    $target = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'index.php';
    header("Location: $target");
    exit();
}
?>