<?php
// Placeholder order data array (simulates database table)
$orders = [
    [
        'id' => 1,
        'customer_name' => 'John Doe',
        'order_date' => '2023-10-01',
        'total_amount' => 150.50,
        'status' => 'Pending'
    ],
    [
        'id' => 2,
        'customer_name' => 'Jane Smith',
        'order_date' => '2023-10-02',
        'total_amount' => 250.00,
        'status' => 'Shipped'
    ],
    [
        'id' => 3,
        'customer_name' => 'Bob Johnson',
        'order_date' => '2023-10-03',
        'total_amount' => 75.25,
        'status' => 'Completed'
    ],
    [
        'id' => 4,
        'customer_name' => 'Alice Brown',
        'order_date' => '2023-10-04',
        'total_amount' => 300.00,
        'status' => 'Pending'
    ],
    [
        'id' => 5,
        'customer_name' => 'Charlie Wilson',
        'order_date' => '2023-10-05',
        'total_amount' => 125.75,
        'status' => 'Shipped'
    ]
];

// Define allowed order statuses
$allowedStatuses = ['Pending', 'Shipped', 'Completed'];

// Initialize success message variable
$successMessage = '';

// Handle order status update via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];

    // Validate the new status
    if (in_array($newStatus, $allowedStatuses)) {
        // Find and update the order status (placeholder logic)
        foreach ($orders as &$order) {
            if ($order['id'] === $orderId) {
                $order['status'] = $newStatus;
                $successMessage = "Order #{$orderId} status updated to {$newStatus} successfully.";
                break;
            }
        }
    } else {
        $successMessage = "Invalid status selected.";
    }
}
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
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .nav { margin: 20px; text-align: center; }
        .header { margin: 20px; text-align: center; }
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
    <p>Total Orders: <?php echo count($orders); ?></p>

    <!-- Orders table -->
    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <!-- Status update form -->
                            <form method="post" action="">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
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
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
