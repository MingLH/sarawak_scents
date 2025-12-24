<?php
// 1. SESSION START
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. GET CURRENT PAGE NAME
$current_page = basename($_SERVER['PHP_SELF']);

// 3. CART COUNT
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
    
    <style>
        * { box-sizing: border-box; }

        /* Base Navigation Styles */
        body { 
            font-family: 'Poppins', sans-serif; 
            margin: 0; 
            background-color: #f9f9f9;
            padding-top: 90px; 
        }

        .navbar { 
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            
            /* Layout: 3 Columns (Logo, Center, Right) */
            display: grid; 
            grid-template-columns: 200px 1fr 200px; /* Left Fixed, Middle Flex, Right Fixed */
            align-items: center; 
            padding: 0.5rem 2rem; 
            background-color: #fff; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }

        /* CENTER LINKS (Home, Shop, Cart) */
        .nav-center {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 40px;
            margin: 0;
            padding: 0;
        }

        .nav-center li a { 
            text-decoration: none; 
            color: #555; 
            font-weight: 500; 
            font-size: 1rem; 
            transition: color 0.3s;
            position: relative; /* For badge positioning */
        }
        
        .nav-center li a.active, .nav-center li a:hover { 
            color: #064e3b; 
            font-weight: 700; 
            text-decoration: underline;
            text-underline-offset: 10px;
        }

        /* CART BADGE STYLE */
        .cart-badge {
            background-color: #e74c3c; /* Red */
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            position: absolute;
            top: -8px;
            right: -12px;
            min-width: 18px;
            text-align: center;
            line-height: 1;
        }

        /* RIGHT LINKS (Auth) */
        .nav-right {
            display: flex;
            justify-content: flex-end;
            list-style: none;
            gap: 15px;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        /* Button Styles */
        .btn-nav { padding: 8px 20px; border-radius: 5px; color: white !important; font-weight: normal !important; text-decoration: none; font-size: 0.9rem; transition: opacity 0.3s; }
        .btn-login { background-color: #064e3b; } /* Green */
        .btn-signup { background-color: #333; }   /* Dark */
        .btn-nav:hover { opacity: 0.9; }

        .btn-logout { color: #e74c3c !important; font-weight: 600 !important; text-decoration: none; font-size: 0.95rem; }
        .btn-profile { color: #064e3b !important; font-weight: 600 !important; text-decoration: none; font-size: 0.95rem; }

        /* Responsive Fix */
        @media (max-width: 768px) {
            .navbar { grid-template-columns: 1fr; height: auto; padding: 10px; gap: 10px; }
            .nav-center { gap: 20px; margin: 10px 0; }
            .nav-right { justify-content: center; }
            body { padding-top: 140px; } /* More padding for taller mobile header */
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="/sarawak_scents/assets/images/Sarawak_Scents_Logo.png" alt="Sarawak Scents" style="height: 50px;">
            </a>
        </div>

        <ul class="nav-center">
            <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="shop.php" class="<?php echo ($current_page == 'shop.php' || $current_page == 'product_details.php') ? 'active' : ''; ?>">Shop</a></li>
            <li>
                <a href="cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">
                    Cart 
                    <?php if($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <ul class="nav-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/admin_dashboard.php" style="color: #d35400; font-weight: bold; text-decoration:none;">Admin Panel</a></li>
                <?php else: ?>
                    <li>
                        <a href="profile.php" class="btn-profile <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                            My Profile
                        </a>
                    </li>
                <?php endif; ?>

                <li>|</li> <li>
                    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?');" class="btn-logout">
                        Logout
                    </a>
                </li>
            
            <?php else: ?>
                <li><a href="login.php" class="btn-nav btn-login">Login</a></li>
                <li><a href="signup.php" class="btn-nav btn-signup">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="main-wrapper" style="width: 100%;">