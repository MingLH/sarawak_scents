<?php
session_start();
require_once "includes/db_connect.php";

/* -----------------------------
   1. Authentication Check
------------------------------ */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

/* -----------------------------
   2. Fetch User Data (Prepared Statement)
------------------------------ */
$user_id = $_SESSION['user_id'];

$sql = "SELECT full_name, email, phone_number, address, created_at 
        FROM users 
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

/* -----------------------------
   3. XSS Protection Helper
------------------------------ */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Sarawak Scents</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="container">
    <h2>My Profile</h2>

    <table class="profile-table">
        <tr>
            <th>Full Name</th>
            <td><?= escape($user['full_name']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= escape($user['email']); ?></td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td><?= escape($user['phone_number'] ?? '-'); ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?= escape($user['address'] ?? '-'); ?></td>
        </tr>
        <tr>
            <th>Member Since</th>
            <td><?= escape(date("d M Y", strtotime($user['created_at']))); ?></td>
        </tr>
    </table>

    <div class="profile-actions">
        <a href="edit_profile.php" class="btn">Edit Profile</a>
        <a href="change_password.php" class="btn">Change Password</a>
        <a href="order_history.php" class="btn">Order History</a>
    </div>
</div>

<?php include "includes/footer.php"; ?>

</body>
</html>

