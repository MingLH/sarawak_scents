<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            $redirectPage = ($row['role'] === 'admin') ? 'admin/dashboard.php' : 'index.php';
            
            echo "<script>
                    alert('Login Successful! Welcome " . $row['full_name'] . "');
                    window.location.href = '$redirectPage';
                </script>";
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Account not found.'); window.history.back();</script>";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" /> <title>Login - Sarawak Scents</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

    <a href="index.php" class="back-link">
        <span>&larr;</span> Back
    </a>

    <div class="auth-container">
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="background-color: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="logo-container">
                <img src="assets/images/Sarawak_Scents_Logo.png" alt="Logo">
            </div>

            <h1>Welcome back</h1>
            <p class="subtitle">Sign in to continue</p>

            <form action="login.php" method="post">
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required placeholder="user@example.com" />
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••" />
                </div>

                <div class="forgot">
                    <a href="forget_password.php">Forgot password?</a>
                </div>

                <button type="submit">Sign in</button>

                <p class="signup">
                    Don’t have an account? <a href="signup.php">Sign up</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        if (sessionStorage.getItem("signupSuccess")) {
            alert("Account created successfully. Please sign in.");
            sessionStorage.removeItem("signupSuccess");
        }
    </script>

</body>
</html>