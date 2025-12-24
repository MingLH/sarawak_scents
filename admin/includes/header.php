<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sarawak Scents</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Roboto', sans-serif; margin: 0; background-color: #f4f6f9; display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR */
        .sidebar { width: 250px; background-color: #343a40; color: #fff; display: flex; flex-direction: column; padding-top: 20px; flex-shrink: 0; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 1.2rem; text-transform: uppercase; color: #bbb; letter-spacing: 1px; }
        .sidebar a { padding: 15px 25px; text-decoration: none; color: #d1d1d1; font-size: 1rem; display: block; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; border-left-color: #064e3b; }
        .logout-btn { margin-top: auto; background-color: #c0392b; text-align: center; }
        .logout-btn:hover { background-color: #a93226; border-left-color: transparent; }

        /* MAIN CONTENT STRUCTURE */
        .main-content { flex: 1; padding: 20px 40px; overflow-y: auto; }

        /* COMMON UTILITIES */
        .btn-action { padding: 8px 15px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        
        /* CARD & TABLE STYLES */
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #555; font-weight: 600; }

        /* PRINT MEDIA */
        @media print {
            .sidebar { display: none; }
            .btn-action, .logout-btn { display: none; }
            body { background: white; height: auto; overflow: visible; }
            .main-content { padding: 0; }
        }
    </style>
</head>
<body>

    <?php 
        // Get current page name (e.g., 'dashboard.php')
        $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
        <a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">Manage Products</a>
        <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">Manage Orders</a>
        <a href="users.php" class="<?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">Members List</a>
        
        <a href="../index.php" target="_blank" style="margin-top: 20px; border-top: 1px solid #555;">View Website &rarr;</a>
        
        <a href="../logout.php" class="logout-btn" onclick="return confirm('Logout of Admin Panel?');">Logout</a>
    </div>

    <div class="main-content">