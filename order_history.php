<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT order_id, total_amount, order_date, status
     FROM orders
     WHERE user_id = ?
     ORDER BY order_date DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="container">
    <h2>Order History</h2>

    <?php if ($orders->num_rows === 0): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table class="profile-table">
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total (RM)</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= escape($row['order_id']); ?></td>
                    <td><?= escape(date("d M Y", strtotime($row['order_date']))); ?></td>
                    <td><?= escape(number_format($row['total_amount'], 2)); ?></td>
                    <td><?= escape($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <a href="profile.php" class="btn">Back to Profile</a>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>

