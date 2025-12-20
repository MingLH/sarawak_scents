<?php
// Placeholder transaction data array (simulates database table)
$transactions = [
    [
        'id' => 1,
        'date' => '2025-12-01',
        'amount' => 100.50,
        'description' => 'order 1'
    ],
    [
        'id' => 2,
        'date' => '2025-12-02',
        'amount' => 250.00,
        'description' => 'order 2'
    ],
    [
        'id' => 3,
        'date' => '2025-12-03',
        'amount' => 75.25,
        'description' => '0rder 3'
    ],
    [
        'id' => 4,
        'date' => '2025-10-10',
        'amount' => 150.00,
        'description' => 'order 4'
    ],
    [
        'id' => 5,
        'date' => '2025-10-15',
        'amount' => 200.75,
        'description' => 'order 5'
    ],
    [
        'id' => 6,
        'date' => '2025-09-20',
        'amount' => 50.00,
        'description' => 'order 6'
    ],
    [
        'id' => 7,
        'date' => '2025-09-25',
        'amount' => 300.00,
        'description' => 'order 7'
    ],
    [
        'id' => 8,
        'date' => '2025-08-15',
        'amount' => 125.50,
        'description' => 'order 8'
    ]
];

// Get filter period from GET parameter, default to 'all'
$filter = $_GET['filter'] ?? 'all';

// Function to filter transactions based on period
function filterTransactions($transactions, $filter) {
    $filtered = [];
    $now = new DateTime();

    foreach ($transactions as $transaction) {
        $transDate = new DateTime($transaction['date']);

        switch ($filter) {
            case 'daily':
                // Transactions from today
                if ($transDate->format('Y-m-d') === $now->format('Y-m-d')) {
                    $filtered[] = $transaction;
                }
                break;
            case 'weekly':
                // Transactions from the last 7 days
                $weekAgo = clone $now;
                $weekAgo->modify('-7 days');
                if ($transDate >= $weekAgo) {
                    $filtered[] = $transaction;
                }
                break;
            case 'monthly':
                // Transactions from the current month
                if ($transDate->format('Y-m') === $now->format('Y-m')) {
                    $filtered[] = $transaction;
                }
                break;
            default:
                // No filter, include all
                $filtered[] = $transaction;
                break;
        }
    }

    return $filtered;
}

// Apply filter to transactions
$filteredTransactions = filterTransactions($transactions, $filter);

// Calculate total amount for the filtered transactions
$totalAmount = 0;
foreach ($filteredTransactions as $transaction) {
    $totalAmount += $transaction['amount'];
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

    <!-- Display total amount -->
    <p><strong>Total Amount: $<?php echo number_format($totalAmount, 2); ?></strong></p>

    <!-- Transaction table -->
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($filteredTransactions)): ?>
                <tr>
                    <td colspan="4">No transactions found for the selected filter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($filteredTransactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                        <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table><br>

    <!-- Placeholder PDF download button -->
    <div class="print-btn">
        <button onclick="window.print()">Download PDF (Print)</button>
    </div>
</body>
</html>
