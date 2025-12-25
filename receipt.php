<?php
session_start();
include 'includes/db_connect.php';

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['user_id'];

// 1. Fetch Order Details (Secure Check)
$sql = "SELECT o.*, u.full_name, u.email, u.address 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = $order_id AND o.user_id = $user_id";
$order_res = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($order_res);

if (!$order) { 
    die("Receipt not found or access denied."); 
}

// 2. Fetch Order Items
$items_res = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo $order_id; ?> - Sarawak Scents</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: #eef2f6;"> <div class="receipt-container">
        
        <div class="receipt-header">
            <img src="assets/images/Sarawak_Scents_Logo.png" alt="Logo" class="receipt-logo">
            <h1 class="receipt-title">Official Receipt</h1>
            <div class="receipt-meta">
                <p><strong>Order ID:</strong> #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
            </div>
        </div>

        <div class="bill-to">
            <strong style="color: #064e3b; display:block; margin-bottom:5px;">BILL TO:</strong>
            <?php echo htmlspecialchars($order['full_name']); ?><br>
            <span style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($order['email']); ?></span>
            <div style="margin-top: 10px; padding-top:10px; border-top:1px dashed #ddd;">
                <?php echo nl2br(htmlspecialchars($order['address'])); ?>
            </div>
        </div>

        <div class="receipt-items">
            <div style="font-size: 0.85rem; color: #999; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Items Purchased</div>
            
            <?php while($item = mysqli_fetch_assoc($items_res)): ?>
            <div class="receipt-item">
                <span class="item-name">
                    <?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?>
                </span>
                <span class="item-price">
                    RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                </span>
            </div>
            <?php endwhile; ?>

            <div class="receipt-total">
                <span>TOTAL PAID</span>
                <span>RM <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>

        <div class="receipt-status">
            <p style="margin-bottom: 5px;">Payment Status: <strong style="color: #064e3b;"><?php echo $order['status']; ?></strong></p>
            <p>Thank you for supporting authentic Bornean craftsmanship.</p>
        </div>

        <div class="receipt-actions">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="profile.php" class="btn-back-home">
                Back to Profile
            </a>
        </div>

    </div>

</body>
</html>