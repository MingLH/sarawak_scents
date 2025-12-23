<?php
session_start();
include 'includes/db_connect.php';

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['user_id'];

// Fetch Order Details (Secure: Only allow if order belongs to this user)
$sql = "SELECT o.*, u.full_name, u.email, u.address 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = $order_id AND o.user_id = $user_id";
$order_res = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($order_res);

if (!$order) { die("Receipt not found or access denied."); }

// Fetch Items
$items_res = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $order_id; ?></title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 40px; }
        .receipt { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px dashed #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { border-top: 2px solid #333; margin-top: 20px; padding-top: 10px; font-weight: bold; font-size: 1.2rem; display: flex; justify-content: space-between; }
        .print-btn { display: block; width: 100%; padding: 10px; background: #333; color: white; text-align: center; text-decoration: none; margin-top: 20px; border-radius: 5px; cursor: pointer; }

        /* =========================================
           PRINT STYLES (Hide buttons when printing)
           ========================================= */
        @media print {
            /* Hide elements with this class */
            .no-print { display: none !important; }
            
            /* Clean up the layout for paper */
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; border: none; margin: 0; width: 100%; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2 style="margin:0;">SARAWAK SCENTS</h2>
            <p>Official Receipt</p>
            <p style="font-size: 0.9rem; color: #666;">
                Date: <?php echo date('d/m/Y h:i A', strtotime($order['order_date'])); ?><br>
                Order ID: #<?php echo $order_id; ?>
            </p>
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Bill To:</strong><br>
            <?php echo htmlspecialchars($order['full_name']); ?><br>
            <?php echo htmlspecialchars($order['email']); ?>
        </div>

        <div style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Items Purchased</div>
        
        <?php while($item = mysqli_fetch_assoc($items_res)): ?>
        <div class="item">
            <span><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?></span>
            <span>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
        </div>
        <?php endwhile; ?>

        <div class="total">
            <span>TOTAL PAID</span>
            <span>RM <?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
        
        <div style="text-align: center; margin-top: 30px; font-size: 0.8rem; color: #888;">
            Thank you for shopping with us!<br>
            Payment Status: <?php echo $order['status']; ?>
        </div>

        <button onclick="window.print()" class="print-btn no-print">Print Receipt</button>
        
        <a href="profile.php" class="no-print" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none;">
            Back to Profile
        </a>
    </div>
</body>
</html>