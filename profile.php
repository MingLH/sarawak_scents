<?php
session_start();
include 'includes/db_connect.php';

// 1. SECURITY: Kick out if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. GET USER DETAILS
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_res = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_res);

// 3. GET ORDER HISTORY
$order_sql = "SELECT o.*, t.payment_method 
              FROM orders o 
              LEFT JOIN transactions t ON o.order_id = t.order_id 
              WHERE o.user_id = $user_id 
              ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $order_sql);

include 'includes/header.php';
?>

<div class="profile-container">
    
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('payment')) {
            alert("Payment Successful! Your order has been placed.");
            window.history.replaceState({}, document.title, "profile.php");
        }
    </script>

    <div class="profile-header">
        <div class="user-info">
            <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
            
            <div class="user-details">
                <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone_number']); ?></div>
            </div>

            <div class="profile-actions">
                <a href="edit_profile.php" class="btn-edit">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="logout.php" onclick="return confirm('Are you sure?');" class="btn-logout-soft">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="user-meta">
            <div>Member since</div>
            <div style="font-size: 1.2rem; font-weight: bold; color: #064e3b;">
                <?php echo date('F Y', strtotime($user['created_at'])); ?>
            </div>
        </div>
    </div>

    <h2 class="order-history-title">Order History</h2>

    <?php if (mysqli_num_rows($orders) > 0): ?>
        <table class="cart-table"> <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Total</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ord = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td data-label="Order #">
                            <strong>#<?php echo $ord['order_id']; ?></strong>
                        </td>
                        <td data-label="Date" style="color: #666;">
                            <?php echo date('d M Y', strtotime($ord['order_date'])); ?>
                        </td>
                        <td data-label="Method">
                            <?php echo htmlspecialchars($ord['payment_method'] ?? 'Online'); ?>
                        </td>
                        <td data-label="Total" style="color: #064e3b; font-weight: bold;">
                            RM <?php echo number_format($ord['total_amount'], 2); ?>
                        </td>
                        <td data-label="Status" style="text-align: center;">
                            <?php 
                                // Dynamic Badge Class
                                $statusClass = 'status-pending'; // Default
                                if ($ord['status'] == 'Paid') $statusClass = 'status-paid';
                                elseif ($ord['status'] == 'Shipped') $statusClass = 'status-shipped';
                                elseif ($ord['status'] == 'Cancelled') $statusClass = 'status-cancelled';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($ord['status']); ?>
                            </span>
                        </td>
                        <td data-label="Action" style="text-align: center;">
                            <a href="receipt.php?order_id=<?php echo $ord['order_id']; ?>" class="receipt-link">
                                View Receipt
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
            <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
            <h3 style="color: #888;">No orders yet</h3>
            <p style="color: #aaa; margin-bottom: 20px;">Looks like you haven't discovered our scents yet.</p>
            <a href="shop.php" class="btn-checkout" style="max-width: 200px; margin: 0 auto;">Start Shopping</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>