<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/sarawak_scents/includes/db_connect.php';

// Get filter period from GET parameter, default to 'all'
$filter = $_GET['filter'] ?? 'all';

// Function to get filtered transactions from database
function getFilteredTransactions($conn, $filter) {
    // Base query - join transactions with orders to get total_amount
    $query = "SELECT 
                t.transaction_id,
                t.order_id,
                t.transaction_date,
                t.payment_method,
                t.payment_status,
                o.total_amount
              FROM transactions t
              INNER JOIN orders o ON t.order_id = o.order_id";
    
    // Add WHERE clause for filter
    $query .= " WHERE 1=1";
    
    // Add date filter based on selection
    switch ($filter) {
        case 'daily':
            // Transactions from today
            $query .= " AND DATE(t.transaction_date) = CURDATE()";
            break;
        case 'weekly':
            // Transactions from the last 7 days
            $query .= " AND t.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'monthly':
            // Transactions from the current month
            $query .= " AND YEAR(t.transaction_date) = YEAR(CURDATE()) 
                       AND MONTH(t.transaction_date) = MONTH(CURDATE())";
            break;
        default:
            // No additional filter, show all
            break;
    }
    
    // Order by date descending (newest first)
    $query .= " ORDER BY t.transaction_date DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $transactions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
    
    return $transactions;
}

// Get filtered transactions
$filteredTransactions = getFilteredTransactions($conn, $filter);

// Calculate total amount for the filtered transactions
$totalAmount = 0;
foreach ($filteredTransactions as $transaction) {
    // Only count successful transactions
    if ($transaction['payment_status'] === 'Success') {
        $totalAmount += $transaction['total_amount'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f5f5f5; }
        .nav { margin: 20px; text-align: center; }
        .print-btn { text-align: center; margin-top: 20px;}
        .header { margin: 20px; text-align: center; }
        .success { color: green; font-weight: bold; }
        .failed { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="nav">
        <button onclick="window.location.href='add_product.php'">Add Product</button>
        <button onclick="window.location.href='manage_orders.php'">Manage Orders</button>
        <button onclick="window.location.href='members_list.php'">Members List</button>
    </div>

    <h2>Transaction Report</h2>

    <!-- Filter form -->
    <form method="get" action="">
        <label for="filter">Filter by:</label>
        <select name="filter" id="filter">
            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All</option>
            <option value="daily" <?php echo $filter === 'daily' ? 'selected' : ''; ?>>Daily</option>
            <option value="weekly" <?php echo $filter === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
            <option value="monthly" <?php echo $filter === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
        </select>
        <button type="submit">Apply Filter</button>
    </form>

    <!-- Display total amount (only successful transactions) -->
    <p><strong>Total Amount: RM <?php echo number_format($totalAmount, 2); ?></strong></p>

    <!-- Transaction table -->
    <table border="1">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Order ID</th>
                <th>Date</th>
                <th>Amount (RM)</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($filteredTransactions)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No transactions found for the selected filter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($filteredTransactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['order_id']); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                        <td>RM <?php echo number_format($transaction['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($transaction['payment_method'] ?? 'N/A'); ?></td>
                        <td class="<?php echo strtolower($transaction['payment_status']); ?>">
                            <?php echo htmlspecialchars($transaction['payment_status']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table><br>

    <!-- Placeholder PDF download button -->
    <div class="print-btn">
        <button onclick="window.print()">Download PDF (Print)</button>
    </div>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>