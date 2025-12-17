<!--JUST TESTING NOT REAL DASHBOARD PAGE, YOU CAN USE THIS TO DIRECTLY-->
<?php
session_start();

// vv PLEASE INCLUDE THESE FOR SECURITY vv

// 1. Security Check: If the user is NOT logged in, send them to login.php
if (!isset($_SESSION['user_id'])) {
    // Set the message for the login page to display
    $_SESSION['error_message'] = "Please log in to access this page.";
    
    header("Location: login.php");
    exit();
}

// 2. Role Check: If user is an Admin, they shouldn't be here
if ($_SESSION['role'] === 'admin') {
    // Redirect them to their own dashboard
    header("Location: admin_dashboard.php");
    exit();
}

// 3. Flash Message Check: Show "Access Denied" if they tried to enter the admin area
$error_to_show = "";
if (isset($_SESSION['error_message'])) {
    $error_to_show = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear it so it doesn't show again
}
?>

<!--// ^^ PLEASE INCLUDE THESE FOR SECURITY ^^-->

<!--REMEMBER TO ADD HEADER AND FOOTER-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - Sarawak Scents</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <div class="welcome-card">
            <span class="role-badge"><?php echo $_SESSION['role']; ?></span>
            <h1>Welcome to Sarawak Scents</h1>
            <p>Hello, <strong><?php echo ($_SESSION['full_name']); ?></strong>!</p>
            <p>This is your member dashboard.</p>

            <hr>

            <a href="logout.php" class="logout-link" id="profileLogoutBtn">Log Out</a>
        </div>
    </div>

    <?php if ($error_to_show): ?>
    <script>
        alert("<?php echo $error_to_show; ?>");
    </script>
    <?php endif; ?>

    <script src="js/userAuthLogOut.js"></script>
    
</body>
</html>
