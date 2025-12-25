<?php
// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/Exception.php';
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/config.php'; // Load the secret variables

session_start();
include 'includes/db_connect.php';

// ==========================================
// 1. THE GATEKEEPER (Requirement 2.ii)
// ==========================================
// If user is NOT logged in, send them to Login/Register page
if (!isset($_SESSION['user_id'])) {
    // Optional: Set a message so they know why they were redirected
    echo "<script>alert('Please login or register to complete your purchase.'); window.location.href='login.php';</script>";
    exit();
}

// 2. Security: Kick them out if Cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 3. Fetch User Address (Auto-fill for convenience)
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_res = mysqli_query($conn, $user_sql);
$user_data = mysqli_fetch_assoc($user_res);

// 4. Calculate Final Total
$ids = implode(',', array_keys($_SESSION['cart']));
$sql = "SELECT * FROM products WHERE product_id IN ($ids)";
$result = mysqli_query($conn, $sql);

$total_amount = 0;
$order_details = []; // We will store details here to use after insertion

while ($row = mysqli_fetch_assoc($result)) {
    $qty = $_SESSION['cart'][$row['product_id']];
    $subtotal = $row['price'] * $qty;
    $total_amount += $subtotal;
    
    // Save for step 6
    $order_details[] = [
        'id' => $row['product_id'],
        'price' => $row['price'],
        'qty' => $qty
    ];
}

// ==========================================
// 5. HANDLE "PLACE ORDER" CLICK
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // A. Update Address (if user changed it)
    mysqli_query($conn, "UPDATE users SET address = '$address' WHERE user_id = $user_id");
    
    // B. Insert ORDER (Status = Paid immediately for dummy flow)
    $insert_order = "INSERT INTO orders (user_id, total_amount, status, order_date) 
                     VALUES ($user_id, $total_amount, 'Paid', NOW())";
    
    if (mysqli_query($conn, $insert_order)) {
        $order_id = mysqli_insert_id($conn); // Get the ID of the order we just created
        
        // C. Insert ORDER ITEMS (The Loop)
        foreach ($order_details as $item) {
            $pid = $item['id'];
            $pqty = $item['qty'];
            $pprice = $item['price'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                 VALUES ($order_id, $pid, $pqty, $pprice)");
        }
        
        // D. Insert TRANSACTION Record (Rubric 5.iv)
        mysqli_query($conn, "INSERT INTO transactions (order_id, payment_method, payment_status, transaction_date) 
                             VALUES ($order_id, '$payment_method', 'Success', NOW())");
        
        // === SEND EMAIL VIA GMAIL SMTP ===
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            // ENABLE DEBUGGING (Remove this after fixing!)
            $mail->SMTPDebug = 0; // Turn off debug output (Silence is golden)
            $mail->Debugoutput = 'html';
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_EMAIL;
            $mail->Password   = SMTP_PASSWORD;  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // === FIX: BYPASS SSL CERTIFICATE CHECK ===
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom(SMTP_EMAIL, 'Sarawak Scents');
            $mail->addAddress($user_data['email'], $user_data['full_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Order Confirmation - Order #$order_id";
            $mail->Body    = "
                <h2>Thank you for your order!</h2>
                <p>Hi <b>{$user_data['full_name']}</b>,</p>
                <p>We have received your order.</p>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr><td><strong>Order ID:</strong></td><td>#$order_id</td></tr>
                    <tr><td><strong>Total Amount:</strong></td><td>RM " . number_format($total_amount, 2) . "</td></tr>
                    <tr><td><strong>Date:</strong></td><td>" . date('d M Y, h:i A') . "</td></tr>
                </table>
                <p>We will ship your items to:<br>
                {$address}</p>
                <p>Thank you for shopping with Sarawak Scents!</p>
            ";
            
            // Plain text version for non-HTML mail clients
            $mail->AltBody = "Thank you for your order #$order_id. Total: RM $total_amount";
            
            $mail->send();
        } catch (Exception $e) {
            // If email fails, we still want the order to complete, so we just log the error or ignore it
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            // exit();
        }

        // E. EMPTY THE CART
        unset($_SESSION['cart']);
        
        // Redirect with a flag so profile.php handles the alert cleanly
        header("Location: profile.php?payment=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
?>

<div class="checkout-container">
    
    <h1 class="checkout-title">Secure Checkout</h1>
    
    <form action="checkout.php" method="POST" class="checkout-form">
        
        <div class="checkout-left">
            
            <div class="checkout-section">
                <h3 class="checkout-subtitle">1. Shipping Details</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color:#555;">Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" disabled 
                           class="form-input" style="background: #f4f6f8; cursor: not-allowed;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color:#555;">Delivery Address</label>
                    <textarea name="address" required rows="3" class="form-input" 
                              style="height: auto; font-family: inherit;"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="checkout-section">
                <h3 class="checkout-subtitle">2. Payment Method</h3>
                
                <div class="payment-options">
                    <label class="payment-label">
                        <input type="radio" name="payment_method" value="Online Banking" checked class="payment-radio"> 
                        <span class="payment-text">Online Banking (FPX)</span>
                    </label>
                    
                    <label class="payment-label">
                        <input type="radio" name="payment_method" value="Credit Card" class="payment-radio"> 
                        <span class="payment-text">Credit / Debit Card</span>
                    </label>
                    
                    <label class="payment-label">
                        <input type="radio" name="payment_method" value="E-Wallet" class="payment-radio"> 
                        <span class="payment-text">Touch 'n Go / GrabPay</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="checkout-right">
            <div class="order-summary-box">
                <h3 class="checkout-subtitle">Order Summary</h3>
                
                <div style="margin-bottom: 15px; font-size: 0.9rem; color: #888;">
                    Date: <?php echo date('d M Y'); ?>
                </div>
                
                <?php 
                // Reset data pointer to loop again for display
                mysqli_data_seek($result, 0);
                while($row = mysqli_fetch_assoc($result)): 
                    $qty = $_SESSION['cart'][$row['product_id']];
                ?>
                    <div class="summary-row">
                        <span class="summary-item-name">
                            <?php echo $qty; ?>x <?php echo htmlspecialchars($row['name']); ?>
                        </span>
                        <span>RM <?php echo number_format($row['price'] * $qty, 2); ?></span>
                    </div>
                <?php endwhile; ?>
                
                <div class="summary-total">
                    <span>Total</span>
                    <span>RM <?php echo number_format($total_amount, 2); ?></span>
                </div>

                <button type="submit" onclick="return confirm('Confirm payment of RM <?php echo $total_amount; ?>?');" class="btn-pay">
                    <i class="fas fa-lock"></i> Pay Now
                </button>
                
                <div class="secure-notice">
                    <i class="fas fa-shield-alt"></i> 256-bit SSL Encrypted Payment
                </div>
            </div>
        </div>

    </form>
</div>

<?php include 'includes/footer.php'; ?>