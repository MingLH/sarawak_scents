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

// 3. GET ORDER HISTORY (Requirement 4.ii)
// We join with transactions to show the payment method if available
$order_sql = "SELECT o.*, t.payment_method 
              FROM orders o 
              LEFT JOIN transactions t ON o.order_id = t.order_id 
              WHERE o.user_id = $user_id 
              ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $order_sql);

include 'includes/header.php';
?>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 60vh;">
    
    <script>
        // Simple check if URL contains ?payment=success
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('payment')) {
            alert("Payment Successful! Your order has been placed.");
            // Clean URL
            window.history.replaceState({}, document.title, "profile.php");
        }
    </script>

    <div style="background: white; border-left: 5px solid #064e3b; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
        
        <div>
            <h1 style="margin: 0 0 5px 0; color: #333;">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
            
            <div style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                <div style="margin-bottom: 3px;">📧 <?php echo htmlspecialchars($user['email']); ?></div>
                <div>📞 <?php echo htmlspecialchars($user['phone_number']); ?></div>
            </div>

            <a href="edit_profile.php" style="display: inline-block; background: #064e3b; color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: opacity 0.3s;">
                Edit Profile
            </a>
            
            <a href="logout.php" id="profileLogoutBtn" onclick="return confirm('Are you sure?');" style="display: inline-block; background: #fee2e2; color: #991b1b; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 0.9rem; margin-left: 10px;">
                Logout
            </a>
        </div>

        <div style="text-align: right; align-self: center;">
            <div style="font-size: 0.9rem; color: #888; margin-bottom: 5px;">Member since</div>
            <div style="font-size: 1.2rem; font-weight: bold; color: #064e3b;"><?php echo date('F Y', strtotime($user['created_at'])); ?></div>
        </div>
    </div>

    <h2 style="color: #064e3b; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Order History</h2>

    <?php if (mysqli_num_rows($orders) > 0): ?>
        <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #374151;">Order #</th>
                        <th style="padding: 15px; text-align: left; color: #374151;">Date</th>
                        <th style="padding: 15px; text-align: left; color: #374151;">Method</th>
                        <th style="padding: 15px; text-align: left; color: #374151;">Total</th>
                        <th style="padding: 15px; text-align: center; color: #374151;">Status</th>
                        <th style="padding: 15px; text-align: center; color: #374151;">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ord = mysqli_fetch_assoc($orders)): ?>
                        <tr style="border-bottom: 1px solid #f3f4f6; background: #fff;">
                            <td style="padding: 15px; font-weight: bold; color: #333;">
                                #<?php echo $ord['order_id']; ?>
                            </td>
                            <td style="padding: 15px; color: #666;">
                                <?php echo date('d M Y, h:i A', strtotime($ord['order_date'])); ?>
                            </td>
                            <td style="padding: 15px; color: #666;">
                                <?php echo htmlspecialchars($ord['payment_method'] ?? 'Online'); ?>
                            </td>
                            <td style="padding: 15px; font-weight: bold; color: #064e3b;">
                                RM <?php echo number_format($ord['total_amount'], 2); ?>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <?php 
                                    // Status Logic: Green for Paid, Red for Cancelled
                                    $bg = ($ord['status'] == 'Paid') ? '#d1fae5' : '#fee2e2';
                                    $color = ($ord['status'] == 'Paid') ? '#065f46' : '#991b1b';
                                ?>
                                <span style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <?php echo $ord['status']; ?>
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="receipt.php?order_id=<?php echo $ord['order_id']; ?>" 
                                   style="display: inline-block; border: 1px solid #064e3b; color: #064e3b; padding: 6px 14px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.2s;">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px; background: white; border-radius: 8px; border: 1px dashed #ccc;">
            <h3 style="color: #888;">No orders yet</h3>
            <p style="color: #aaa; margin-bottom: 20px;">Looks like you haven't discovered our scents yet.</p>
            <a href="shop.php" style="background: #064e3b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Start Shopping</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>