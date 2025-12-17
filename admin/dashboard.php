<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.header {
    background-color: #333;
    color: white;
    padding: 10px;
    text-align: center;
}

.nav {
    margin: 20px;
    text-align: center;
}

.nav button,
.filter-form button,
.output-options button {
    padding: 8px 16px;
    margin: 0 10px;
    cursor: pointer;
}

/* Container center */
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Filter form center */
.filter-form {
    margin-bottom: 20px;
    text-align: center;
}

.filter-form select {
    padding: 8px;
    margin: 0 10px;
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

/* Output buttons center */
.output-options {
    text-align: center;
    margin-top: 20px;
}

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
    <div class="container">
        <h2>Transaction Report</h2>
        <form class="filter-form" method="POST">
            <label for="period">Filter by Period:</label>
            <select name="period" id="period">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
            <button type="submit">Apply Filter</button>
        </form>
        <div id="report-container">
            <?php
            // Placeholder transaction data (replace with database queries later)
            $transactions = [
                ['date' => '2023-10-01', 'amount' => 100.00, 'description' => 'Sale 1'],
                ['date' => '2023-10-02', 'amount' => 200.00, 'description' => 'Sale 2'],
                ['date' => '2023-10-03', 'amount' => 150.00, 'description' => 'Sale 3'],
                ['date' => '2023-10-04', 'amount' => 300.00, 'description' => 'Sale 4'],
                ['date' => '2023-10-05', 'amount' => 250.00, 'description' => 'Sale 5'],
            ];

            $period = isset($_POST['period']) ? $_POST['period'] : 'daily';
            $filteredTransactions = [];

            // Simple filtering logic (placeholder - enhance with actual date logic later)
            foreach ($transactions as $transaction) {
                // For simplicity, include all for now; in real scenario, filter by date range
                $filteredTransactions[] = $transaction;
            }

            if (!empty($filteredTransactions)) {
                echo '<table>';
                echo '<tr><th>Date</th><th>Amount</th><th>Description</th></tr>';
                foreach ($filteredTransactions as $transaction) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($transaction['date']) . '</td>';
                    echo '<td>$' . number_format($transaction['amount'], 2) . '</td>';
                    echo '<td>' . htmlspecialchars($transaction['description']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p>No transactions found for the selected period.</p>';
            }
            ?>
        </div>
        <div class="output-options">
            <button onclick="generatePDF()">Generate PDF</button>
        </div>
    </div>
    <script>

        function generatePDF() {
            // Open a new window with the report content and trigger print (user can save as PDF)
            var reportContent = document.getElementById('report-container').innerHTML;
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Transaction Report</title></head><body>' + reportContent + '</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>
