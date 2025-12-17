<!--JUST TESTING NOT REAL DASHBOARD PAGE, YOU CAN USE THIS TO DIRECTLY-->
<?php
session_start();

// vv PLEASE INCLUDE THESE FOR SECURITY vv

// 1. Double Security Check
// Check if user is logged in AND if they are actually an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    
    // If they are a regular member, send them to their index
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'member') {
        $_SESSION['error_message'] = "You don't have permission to access this page";
        header("Location: index.php");
    } else {
        // If they aren't logged in at all, send to login
        $_SESSION['error_message'] = "You don't have permission to access this page";
        header("Location: login.php");
    }
    exit();
}
?>
<!--// ^^ PLEASE INCLUDE THESE FOR SECURITY ^^-->



<!--REMEMBER TO ADD HEADER AND FOOTER-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sarawak Scents</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Hello, <strong><?php echo ($_SESSION['full_name']); ?></strong></p>
        <a href="logout.php" class="logout-link" id="profileLogoutBtn">Log Out</a>
    </div>
    <script src="js/userAuthLogOut.js"></script>
</body>
</html>