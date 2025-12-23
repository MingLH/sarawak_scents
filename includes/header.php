<?php
// 1. SESSION START: This must be the very first line of code.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarawak Scents</title>
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 97%">
        
        <div class="logo">
            <a href="index.php" style="text-decoration: none; font-size: 1.5rem; font-weight: bold; color: #2c3e50;">
                <img src="assets/images/Sarawak_Scents_Logo.png" alt="Sarawak Scents" style="height: 50px; vertical-align: middle;">
            </a>
        </div>

        <ul class="nav-links" style="list-style: none; display: flex; gap: 20px; align-items: center; margin: 0;">
            <li><a href="index.php" style="text-decoration: none; color: #333;">Home</a></li>
            <li><a href="shop.php" style="text-decoration: none; color: #333;">Shop</a></li>
            
            <li>
                <a href="cart.php" style="text-decoration: none; color: #333; position: relative;">
                    Cart 🛒
                    <?php 
                    // Calculate the number of items in the cart
                    $cart_count = 0;
                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                        // Count total quantity of all items 
                        foreach ($_SESSION['cart'] as $item) {
                            $cart_count += $item['quantity'];
                        }
                    }
                    
                    // Only show the badge if there is at least 1 item
                    if ($cart_count > 0): ?>
                        <span style="background-color: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; position: absolute; top: -10px; right: -15px; font-weight: bold;">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>

            <?php if (isset($_SESSION['user_id'])): ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/admin_dashboard.php" style="color: #d35400; font-weight: bold;">Admin Panel</a></li>
                
                <?php else: ?>
                    <li><a href="profile.php" style="padding: 8px 15px; background-color: #27ae60; color: white; border-radius: 5px; text-decoration: none;">My Profile</a></li>
                <?php endif; ?>

                <li>
                    <a href="logout.php" 
                    onclick="return confirm('Are you sure you want to logout?');" 
                    style="padding: 8px 15px; background-color: #e74c3c; color: white; border-radius: 5px; text-decoration: none;">
                    Logout
                    </a>
                </li>
            
            <?php else: ?>
                
                <li><a href="login.php" style="padding: 8px 15px; background-color: #27ae60; color: white; border-radius: 5px; text-decoration: none;">Login</a></li>
                <li>
                    <a href="signup.php" style="padding: 8px 15px; background-color: #27ae60; color: white; border-radius: 5px; text-decoration: none;">Sign Up</a>
                </li>

            <?php endif; ?>
        </ul>
    </nav>

    <div class="main-wrapper" style="min-height: 80vh; padding: 20px; width: 100%;">