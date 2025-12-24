<?php
session_start();
include '../includes/db_connect.php';

// 1. SECURITY: GATEKEEPER
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ==========================================
// 2. FILTER & DATE LOGIC
// ==========================================
$filter = $_GET['filter'] ?? 'all_time';

$start_date = "";
$end_date = "";
$chart_label = "Overview";
$chart_sql = "";

switch ($filter) {
    case 'daily':
        $picked_date = $_GET['date_input'] ?? date('Y-m-d');
        $start_date = $picked_date;
        $end_date = $picked_date;
        
        $chart_label = "Sales for " . date('d M Y', strtotime($picked_date)) . " (Hourly)";
        $chart_sql = "SELECT DATE_FORMAT(order_date, '%h %p') as label, SUM(total_amount) as total 
                      FROM orders 
                      WHERE status = 'Paid' AND DATE(order_date) = '$picked_date'
                      GROUP BY HOUR(order_date) 
                      ORDER BY order_date ASC";
        break;

    case 'weekly':
        $picked_week = $_GET['week_input'] ?? date('Y-\WW');
        $dto = new DateTime();
        $parts = explode('-W', $picked_week);
        $dto->setISODate($parts[0], $parts[1]); 
        $start_date = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $end_date = $dto->format('Y-m-d');

        $chart_label = "Sales: Week $parts[1] ($start_date to $end_date)";
        $chart_sql = "SELECT DATE_FORMAT(order_date, '%a %d') as label, SUM(total_amount) as total 
                      FROM orders 
                      WHERE status = 'Paid' AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'
                      GROUP BY DATE(order_date) 
                      ORDER BY order_date ASC";
        break;

    case 'monthly':
        $picked_month = $_GET['month_input'] ?? date('Y-m');
        $start_date = $picked_month . "-01";
        $end_date = date('Y-m-t', strtotime($start_date));

        $chart_label = "Sales for " . date('F Y', strtotime($start_date));
        $chart_sql = "SELECT DATE_FORMAT(order_date, '%d %b') as label, SUM(total_amount) as total 
                      FROM orders 
                      WHERE status = 'Paid' AND DATE_FORMAT(order_date, '%Y-%m') = '$picked_month'
                      GROUP BY DATE(order_date) 
                      ORDER BY order_date ASC";
        break;

    case 'custom':
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        $chart_label = "Sales from $start_date to $end_date";
        $chart_sql = "SELECT DATE_FORMAT(order_date, '%d %M') as label, SUM(total_amount) as total 
                      FROM orders 
                      WHERE status = 'Paid' AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'
                      GROUP BY DATE(order_date) 
                      ORDER BY order_date ASC";
        break;

    case 'all_time':
    default:
        $chart_label = "Total Sales History (Monthly Trend)";
        $chart_sql = "SELECT DATE_FORMAT(order_date, '%b %Y') as label, SUM(total_amount) as total 
                      FROM orders 
                      WHERE status = 'Paid'
                      GROUP BY YEAR(order_date), MONTH(order_date) 
                      ORDER BY order_date ASC";
        
        $start_date = "2000-01-01"; 
        $end_date = date('Y-12-31');
        break;
}

// 3. EXECUTE CHART QUERY
$chart_res = mysqli_query($conn, $chart_sql);
$labels = [];
$sales = [];

if (mysqli_num_rows($chart_res) > 0) {
    while ($row = mysqli_fetch_assoc($chart_res)) {
        $labels[] = $row['label']; 
        $sales[] = $row['total'];
    }
} else {
    $labels[] = "No Data";
    $sales[] = 0;
}
$js_labels = json_encode($labels);
$js_sales = json_encode($sales);


// 4. METRICS
$metrics_where = "WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
if ($filter == 'all_time') {
    $metrics_where = "WHERE 1=1"; 
}

$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders $metrics_where AND status = 'Paid'"))['total'] ?? 0;
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders $metrics_where"))['total'] ?? 0;
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'member'"))['total'] ?? 0;
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'"))['total'] ?? 0;


// 5. TRANSACTION TABLE
$query = "SELECT t.*, o.total_amount, o.status AS order_status, u.full_name 
          FROM transactions t
          JOIN orders o ON t.order_id = o.order_id
          JOIN users u ON o.user_id = u.user_id";

if ($filter != 'all_time') {
    $query .= " WHERE DATE(t.transaction_date) BETWEEN '$start_date' AND '$end_date'";
}

$query .= " ORDER BY t.transaction_date DESC LIMIT 100";
$trans_result = mysqli_query($conn, $query);

?>

<?php include 'includes/header.php'; ?>

<style>
    /* Screen Styles */
    .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .card-metric { border-left: 5px solid #ccc; }
    .number { font-size: 1.8rem; font-weight: bold; color: #333; margin-top: 10px; }
    .chart-container { height: 350px; position: relative; width: 100%; }
    
    .filter-group { display: flex; align-items: center; gap: 10px; background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
    .filter-input { padding: 6px; border: 1px solid #ccc; border-radius: 4px; display: none; } 
    
    /* UPDATED STATUS BADGES */
    .status-paid { background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 0.8rem; }
    .status-pending { background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 0.8rem; }
    .status-shipped { background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 0.8rem; }
    .status-cancelled { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 15px; font-weight: bold; font-size: 0.8rem; }

    /* --- 🖨️ PRINT SPECIFIC STYLES --- */
    @media print {
        .filter-group, .btn-action, .sidebar { display: none !important; }
        
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        .card {
            box-shadow: none;
            border: 1px solid #ccc;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        
        .chart-container {
            width: 100% !important;
            height: 300px !important;
        }
        canvas {
            width: 100% !important;
            height: 100% !important;
        }

        h1, h3 { color: black; }
    }
</style>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #333; margin: 0;">Dashboard</h1>
        
        <form method="GET" class="filter-group">
            <span style="font-weight:bold; color:#555;">Filter:</span>
            
            <select name="filter" id="filterSelect" onchange="toggleInputs()" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; font-weight:bold;">
                <option value="all_time" <?php echo ($filter == 'all_time') ? 'selected' : ''; ?>>All Time</option>
                <option value="daily" <?php echo ($filter == 'daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="weekly" <?php echo ($filter == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="monthly" <?php echo ($filter == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                <option value="custom" <?php echo ($filter == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
            </select>

            <input type="date" name="date_input" id="dateInput" class="filter-input" value="<?php echo $_GET['date_input'] ?? date('Y-m-d'); ?>">
            <input type="week" name="week_input" id="weekInput" class="filter-input" value="<?php echo $_GET['week_input'] ?? date('Y-\WW'); ?>">
            <input type="month" name="month_input" id="monthInput" class="filter-input" value="<?php echo $_GET['month_input'] ?? date('Y-m'); ?>">
            
            <div id="rangeInput" class="filter-input" style="display:none; gap:5px; align-items:center;">
                <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? date('Y-m-01'); ?>">
                <span>to</span>
                <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? date('Y-m-d'); ?>">
            </div>

            <button type="submit" class="btn-action">Apply</button>
        </form>
    </div>

    <div class="cards-grid">
        <div class="card card-metric" style="border-left-color: #27ae60;">
            <h3>Sales (Selected Period)</h3>
            <div class="number">RM <?php echo number_format($total_sales, 2); ?></div>
        </div>
        <div class="card card-metric" style="border-left-color: #2980b9;">
            <h3>Orders (Selected Period)</h3>
            <div class="number"><?php echo $total_orders; ?></div>
        </div>
        <div class="card card-metric" style="border-left-color: #f39c12;">
            <h3>Pending Orders</h3>
            <div class="number"><?php echo $total_pending; ?></div>
        </div>
        <div class="card card-metric" style="border-left-color: #8e44ad;">
            <h3>Total Members</h3>
            <div class="number"><?php echo $total_users; ?></div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-top:0; margin-bottom:15px;"><?php echo $chart_label; ?></h3>
        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin:0;">Transaction Records</h3>
            <button onclick="window.print()" class="btn-action" style="background:#555;">🖨️ Print PDF Report</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th> <th>Date</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Order Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($trans_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($trans_result)): ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($row['order_id']); ?></strong></td> <td><?php echo date('d M Y, h:i A', strtotime($row['transaction_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td> 
                        <td>RM <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <?php 
                                // Logic: Determine Class based on Order Status
                                $status = $row['order_status'];
                                $badgeClass = 'status-pending'; // Default
                                
                                if ($status == 'Paid') $badgeClass = 'status-paid';
                                elseif ($status == 'Shipped') $badgeClass = 'status-shipped';
                                elseif ($status == 'Cancelled') $badgeClass = 'status-cancelled';
                            ?>
                            <span class="<?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px;">No records found for this period.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $js_labels; ?>, 
                datasets: [{
                    label: 'Revenue (RM)',
                    data: <?php echo $js_sales; ?>,
                    backgroundColor: 'rgba(6, 78, 59, 0.7)',
                    borderColor: 'rgba(6, 78, 59, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        function toggleInputs() {
            const val = document.getElementById('filterSelect').value;
            const ids = ['dateInput', 'weekInput', 'monthInput', 'rangeInput'];
            ids.forEach(id => document.getElementById(id).style.display = 'none');

            if (val === 'daily') document.getElementById('dateInput').style.display = 'block';
            if (val === 'weekly') document.getElementById('weekInput').style.display = 'block';
            if (val === 'monthly') document.getElementById('monthInput').style.display = 'block';
            if (val === 'custom') document.getElementById('rangeInput').style.display = 'flex';
        }
        toggleInputs();
    </script>

<?php include 'includes/footer.php'; ?>