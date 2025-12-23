<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $message = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!password_verify($current, $row['password'])) {
            $message = "Current password is incorrect.";
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare(
                "UPDATE users SET password = ? WHERE user_id = ?"
            );
            $update->bind_param("si", $hashed, $user_id);
            $update->execute();

            $message = "Password changed successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="container">
    <h2>Change Password</h2>

    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" class="btn">Update Password</button>
        <a href="profile.php" class="btn secondary">Cancel</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>

