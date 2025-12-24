<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/db_connect.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- LOGIC HANDLERS ---

// 1. Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $pid = intval($_POST['product_id']);
    $qty = intval($_POST['quantity']);
    
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// 2. Remove Item
if (isset($_GET['remove'])) {
    $pid = intval($_GET['remove']);
    unset($_SESSION['cart'][$pid]);
    header("Location: cart.php");
    exit();
}

// 3. Update Quantities (Triggered automatically now)
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $pid => $qty) {
        $qty = intval($qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid] = $qty;
        }
    }
    // No redirect needed here, we just let the page reload to show new prices
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 50vh;">
    <h1 style="color: #064e3b; margin-bottom: 20px;">Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px;">
            <p>Your cart is empty.</p>
            <a href="shop.php" style="color: #064e3b; font-weight: bold;">Continue Shopping</a>
        </div>
    <?php else: ?>
        
        <form action="cart.php" method="POST">
            <input type="hidden" name="update_cart" value="1">
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <thead style="background: #f3f4f6;">
                    <tr>
                        <th style="padding: 15px; text-align: left;">Product</th>
                        <th style="padding: 15px; text-align: left;">Price</th>
                        <th style="padding: 15px; text-align: left;">Quantity</th>
                        <th style="padding: 15px; text-align: left;">Total</th>
                        <th style="padding: 15px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $ids = implode(',', array_keys($_SESSION['cart']));
                    
                    // Fetch product details
                    if ($ids) {
                        $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
                        $result = mysqli_query($conn, $sql);

                        while ($row = mysqli_fetch_assoc($result)) {
                            $pid = $row['product_id'];
                            $qty = $_SESSION['cart'][$pid];
                            $subtotal = $row['price'] * $qty;
                            $total += $subtotal;
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            </div>
                        </td>
                        <td style="padding: 15px;">RM <?php echo number_format($row['price'], 2); ?></td>
                        <td style="padding: 15px;">
                            <input type="number" 
                                   name="quantities[<?php echo $pid; ?>]" 
                                   value="<?php echo $qty; ?>" 
                                   min="1" 
                                   max="10" 
                                   onchange="this.form.submit()" 
                                   style="width: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                        </td>
                        <td style="padding: 15px; color: #064e3b; font-weight: bold;">RM <?php echo number_format($subtotal, 2); ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="cart.php?remove=<?php echo $pid; ?>" 
                               style="color: #ef4444; text-decoration: none; font-weight: bold; font-size: 1.2rem;">&times;</a>
                        </td>
                    </tr>
                    <?php 
                        } 
                    }
                    ?>
                </tbody>
            </table>

            <div style="text-align: right; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <h2 style="margin: 0 0 15px 0; color: #333;">Grand Total: RM <?php echo number_format($total, 2); ?></h2>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" style="background: #064e3b; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                            Proceed to Checkout
                        </a>
                    <?php else: ?>
                        <a href="login.php" style="background: #e67e22; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                            Log in to Checkout 🔒
                        </a>
                        <p style="margin-top: 10px; font-size: 0.9rem; color: #666;">
                            You must be a member to purchase.
                        </p>
                    <?php endif; ?>
                    
                </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>