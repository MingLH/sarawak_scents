<?php
include 'includes/db_connect.php';
include 'includes/check_authorization.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Check if the session has the email set from the OTP verification
if (!isset($_SESSION['reset_email'])) {
    echo "<script>
            alert('Session expired or verification failed. Please start the password reset process again.');
            window.location.href = 'forget_password.php';
        </script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_to_update = $_SESSION['reset_email'];
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['repassword'] ?? '';

    // 2. Server-Side Validation
    if (empty($password) || empty($repassword)) {
        die("Error: New password fields are required. <a href='reset_password.php'>Go Back</a>");
    }

    if ($password !== $repassword) {
        die("Error: Passwords do not match. <a href='reset_password.php'>Go Back</a>");
    }

    // 3. Update the Database with HASHED password
    // Use password_hash instead of mysqli_real_escape_string for the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL UPDATE Query using the hash
    $update_sql = "UPDATE users SET password = '$hashed_password' WHERE email = '$email_to_update'";

    if (mysqli_query($conn, $update_sql)) {
        // SUCCESS: Clear session data and redirect to login
        session_destroy();
        
        echo "<script>
                sessionStorage.setItem('passwordResetSuccess', 'true');
                window.location.href = 'login.php';
            </script>";
        exit();
    } else {
        // Database Error
        echo "<h3>Database Error</h3>";
        echo "Error: Could not update password. " . mysqli_error($conn);
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset password</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>

    <a href="#" class="back-link" id="backBtn"><- <span class="back-text">Back</span>
    </a>

    <div class="login-container">
        <div class="card">
            <h1>Reset your password</h1>
            <p class="subtitle">Please enter a new password</p>

            <form action="reset_password.php" method="post" id="resetForm">
                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <input type="password" id="password" name="password"
                        placeholder="Combination of alphabets and number" />
                </div>

                <div class="field">
                    <label class="field-label" for="repassword">Confirm Password</label>
                    <input type="password" id="repassword" name="repassword" />
                </div>

                <button type="submit" id="resetBtn">Reset password</button>

            </form>
        </div>
    </div>

    <div class="modal" id="exitModal">
        <div class="modal-box">

            <button class="modal-close" id="closeModal">x</button>

            <h3>Leave this page?</h3>
            <p>
                If you exit now, the password reset process will be cancelled and cannot
                be undone.
            </p>

            <div class="modal-actions">
                <button class="btn-secondary" id="stayBtn">Stay</button>
                <button class="btn-primary" id="leaveBtn">Continue</button>
            </div>

        </div>
    </div>

    <script src="js/userAuth.js"></script>
</body>

</html>
