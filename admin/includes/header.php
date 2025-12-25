<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../includes/db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sarawak Scents</title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="overlay" onclick="toggleSidebar()"></div>

<div class="admin-wrapper">
    
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-leaf" style="margin-right: 10px; color: #4ade80;"></i> ADMIN PANEL
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="products.php" class="<?php echo ($current_page == 'products.php' || $current_page == 'add_product.php') ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> <span>Manage Products</span>
                </a>
            </li>
            <li>
                <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> <span>Manage Orders</span>
                </a>
            </li>
            <li>
                <a href="users.php" class="<?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> <span>Members List</span>
                </a>
            </li>
            
            <li>
                <a href="../index.php" target="_blank" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <i class="fas fa-external-link-alt"></i> <span>View Website</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="../logout.php" class="logout-btn" onclick="return confirm('Logout of Admin Panel?');">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="main-content">
        
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i> Menu
        </button>