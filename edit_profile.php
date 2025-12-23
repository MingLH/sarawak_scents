<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

/* Fetch current data */
$stmt = $conn->prepare("SELECT phone_number, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Update profile */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = trim($_POST['phone_number']);
    $address = trim($_POST['address']);

    $update = $conn->prepare(
        "UPDATE users SET phone_number = ?, address = ? WHERE user_id = ?"
    );
    $update->bind_param("ssi", $phone, $address, $user_id);

    if ($update->execute()) {
        $message = "Profile updated successfully.";
    } else {
        $message = "Failed to update profile.";
    }
}

function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="container">
    <h2>Edit Profile</h2>

    <?php if ($message): ?>
        <p class="success"><?= escape($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Phone Number</label>
        <input type="text" name="phone_number"
               value="<?= escape($user['phone_number']); ?>" required>

        <label>Address</label>
        <textarea name="address" required><?= escape($user['address']); ?></textarea>

        <button type="submit" class="btn">Update Profile</button>
        <a href="profile.php" class="btn secondary">Cancel</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>

