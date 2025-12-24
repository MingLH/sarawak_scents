<?php
session_start();
include '../includes/db_connect.php';

// 1. SECURITY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$msg = "";
$msg_type = "";

// 2. HANDLE STATUS UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Updated Allowed Statuses
    $allowedStatuses = ['Pending', 'Paid', 'Shipped', 'Cancelled'];
    
    if (in_array($newStatus, $allowedStatuses)) {
        $query = "UPDATE orders SET status = '$newStatus' WHERE order_id = $orderId";
        if (mysqli_query($conn, $query)) {
            // Success message
            $msg = "Order #$orderId updated to $newStatus.";
            $msg_type = "success";
        } else {
            $msg = "Error updating: " . mysqli_error($conn);
            $msg_type = "error";
        }
    } else {
        $msg = "Invalid status selected.";
        $msg_type = "error";
    }
}

// 3. FETCH ORDERS (Sorted by ID so #39 is at the top)
$query = "SELECT o.order_id, o.user_id, o.total_amount, o.order_date, o.status, u.full_name 
          FROM orders o 
          INNER JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.order_id DESC"; // Fixed sorting issue
$result = mysqli_query($conn, $query);
?>

<?php include 'includes/header.php'; ?>

    <h1 style="color: #333;">Manage Orders</h1>

    <?php if ($msg): ?>
        <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card">
        <h3 style="color:#555; margin-top:0; margin-bottom:15px;">Order History</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total (RM)</th>
                    <th>Status</th>
                    <th>Update Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($order['full_name']); ?>
                            <br><small style="color:#888;">User ID: <?php echo $order['user_id']; ?></small>
                        </td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                        <td style="font-weight:bold; color:#064e3b;">
                            RM <?php echo number_format($order['total_amount'], 2); ?>
                        </td>
                        <td>
                            <?php 
                                // Visual Badges
                                $bg = '#eee'; $color = '#333';
                                if($order['status'] == 'Paid') { $bg = '#d1fae5'; $color = '#065f46'; }
                                if($order['status'] == 'Pending') { $bg = '#fef3c7'; $color = '#92400e'; }
                                if($order['status'] == 'Shipped') { $bg = '#dbeafe'; $color = '#1e40af'; }
                                if($order['status'] == 'Cancelled') { $bg = '#fee2e2'; $color = '#991b1b'; }
                            ?>
                            <span style="background:<?php echo $bg; ?>; color:<?php echo $color; ?>; padding:5px 10px; border-radius:15px; font-weight:bold; font-size:0.85rem;">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <select name="status" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
                                    <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Paid" <?php echo ($order['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                    <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn-action" style="padding:6px 10px; font-size:0.8rem;">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:20px;">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include 'includes/footer.php'; ?>