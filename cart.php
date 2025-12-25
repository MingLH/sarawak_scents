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

// 3. Update Quantities
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $pid => $qty) {
        $qty = intval($qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid] = $qty;
        }
    }
}

include 'includes/header.php';
?>

<div class="cart-container">
    <h1 class="cart-title">Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align: center; padding: 60px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
            <i class="fas fa-shopping-basket" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <h3 style="color: #666;">Your cart is empty.</h3>
            <p style="color: #999; margin-bottom: 30px;">Looks like you haven't made your choice yet.</p>
            <a href="shop.php" class="btn-checkout" style="max-width: 250px; margin: 0 auto; display: inline-block;">Continue Shopping</a>
        </div>
    <?php else: ?>
        
        <form action="cart.php" method="POST">
            <input type="hidden" name="update_cart" value="1">
            
            <table class="cart-table">
                <thead>
                    <tr>
                        <th width="45%">Product</th>
                        <th width="15%">Price</th>
                        <th width="15%" style="text-align: center;">Quantity</th>
                        <th width="15%">Total</th>
                        <th width="10%" style="text-align: center;">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $ids = implode(',', array_keys($_SESSION['cart']));
                    
                    if ($ids) {
                        $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
                        $result = mysqli_query($conn, $sql);

                        while ($row = mysqli_fetch_assoc($result)) {
                            $pid = $row['product_id'];
                            $qty = $_SESSION['cart'][$pid];
                            $subtotal = $row['price'] * $qty;
                            $total += $subtotal;
                    ?>
                    <tr>
                        <td data-label="Product">
                            <div class="cart-product-info">
                                <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" 
                                     class="cart-img" alt="Product">
                                <span class="cart-prod-name"><?php echo htmlspecialchars($row['name']); ?></span>
                            </div>
                        </td>

                        <td data-label="Price">
                            RM <?php echo number_format($row['price'], 2); ?>
                        </td>

                        <td data-label="Quantity" style="text-align: center;">
                            <input type="number" 
                                   name="quantities[<?php echo $pid; ?>]" 
                                   value="<?php echo $qty; ?>" 
                                   min="1" 
                                   max="10" 
                                   class="cart-qty-input"
                                   onchange="this.form.submit()">
                        </td>

                        <td data-label="Total" style="color: #064e3b; font-weight: bold;">
                            RM <?php echo number_format($subtotal, 2); ?>
                        </td>

                        <td data-label="Action" style="text-align: center;">
                            <a href="cart.php?remove=<?php echo $pid; ?>" 
                               class="btn-remove" title="Remove Item">
                               <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        } 
                    }
                    ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-total-row">
                    <span>Grand Total:</span>
                    <span class="cart-total-price">RM <?php echo number_format($total, 2); ?></span>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="checkout.php" class="btn-checkout">
                        Proceed to Checkout <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-login-checkout">
                        Log in to Checkout <i class="fas fa-lock" style="margin-left: 10px;"></i>
                    </a>
                    <p style="margin-top: 15px; font-size: 0.9rem; color: #666;">
                        You must be a member to purchase.
                    </p>
                <?php endif; ?>
            </div>

        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>