<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/sarawak_scents/includes/db_connect.php';

// Define allowed order statuses
$allowedStatuses = ['Pending', 'Paid', 'Cancelled'];

// Initialize success message variable
$successMessage = '';

// Handle order status update via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validate the new status
    if (in_array($newStatus, $allowedStatuses)) {
        // Update the order status in database
        $query = "UPDATE orders SET status = '$newStatus' WHERE order_id = $orderId";
        
        if (mysqli_query($conn, $query)) {
            if (mysqli_affected_rows($conn) > 0) {
                $successMessage = "Order #{$orderId} status updated to {$newStatus} successfully.";
            } else {
                $successMessage = "Order not found or status is already {$newStatus}.";
            }
        } else {
            $successMessage = "Error updating order: " . mysqli_error($conn);
        }
    } else {
        $successMessage = "Invalid status selected.";
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode($successMessage));
    exit;
}

// Get message from URL parameter
if (isset($_GET['msg'])) {
    $successMessage = $_GET['msg'];
}

// Fetch all orders from database with user information
$query = "SELECT o.order_id, o.user_id, o.total_amount, o.order_date, o.status, u.full_name 
          FROM orders o 
          INNER JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Count total orders
$totalOrders = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Module</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f5f5f5; }
        form { display: inline-block; margin: 0; }
        .nav { margin: 20px; text-align: center; }
        .header { margin: 20px; text-align: center; }
        .success { color: white; background-color: green; padding: 10px; border-radius: 5px; margin: 10px 0;}
        .error { color: white; background-color: red; padding: 10px; border-radius: 5px; margin: 10px 0;}
        .status-pending { background-color: #fff3cd; }
        .status-paid { background-color: #d4edda; }
        .status-cancelled { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Orders</h1>
    </div>
    
    <div class="nav">
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>
    
    <!-- Display success or error message -->
    <?php if ($successMessage): ?>
        <p class="<?php echo strpos($successMessage, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($successMessage); ?>
        </p>
    <?php endif; ?>
    
    <!-- Display order count -->
    <p><strong>Total Orders: <?php echo $totalOrders; ?></strong></p>
    
    <!-- Orders table -->
    <?php if ($totalOrders == 0): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>User ID</th>
                    <th>Order Date</th>
                    <th>Total Amount (RM)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr class="status-<?php echo strtolower($order['status']); ?>">
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($order['order_date'])); ?></td>
                        <td>RM <?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><strong><?php echo htmlspecialchars($order['status']); ?></strong></td>
                        <td>
                            <!-- Status update form -->
                            <form method="post" action="">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <select name="status">
                                    <?php foreach ($allowedStatuses as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                                            <?php echo $status; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>