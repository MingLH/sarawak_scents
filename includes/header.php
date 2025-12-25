<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Smart Path Logic
$isInAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$basePath = $isInAdmin ? '../' : ''; 

$current_page = basename($_SERVER['PHP_SELF']);
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarawak Scents</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/style.css">
</head>
<body class="with-fixed-header">

    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo $basePath; ?>index.php" class="nav-logo">
                <img src="<?php echo $basePath; ?>assets/images/Sarawak_Scents_Logo.png" alt="Sarawak Scents">
            </a>

            <button class="hamburger" id="hamburger">
                <i class="fas fa-bars"></i>
            </button>

            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="<?php echo $basePath; ?>index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo $basePath; ?>shop.php" class="<?php echo ($current_page == 'shop.php') ? 'active' : ''; ?>">Shop</a></li>
                    <li>
                        <a href="<?php echo $basePath; ?>cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">
                            Cart 
                            <?php if($cart_count > 0): ?>
                                <span class="cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>

                <ul class="nav-auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li><a href="<?php echo $basePath; ?>admin/dashboard.php" class="btn-admin-link">Admin Panel</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo $basePath; ?>profile.php" class="btn-profile">My Profile</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $basePath; ?>logout.php" onclick="return confirm('Logout?');" class="btn-logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $basePath; ?>login.php" class="btn-nav btn-login">Login</a></li>
                        <li><a href="<?php echo $basePath; ?>signup.php" class="btn-nav btn-signup">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">