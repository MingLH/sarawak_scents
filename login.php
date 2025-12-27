<?php
session_start();

// If already logged in, don't show the login form, send them to their dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/admin_dashboard.php");
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

            // 1. Store session data
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            // 2. Determine redirect destination based on role
            $redirectPage = 'index.php'; // Default for members
            if ($row['role'] === 'admin') {
                $redirectPage = 'admin/dashboard.php';
            }

            // 3. Success Alert and Redirect
            echo "<script>
                    alert('Login Successful! Welcome " . $row['full_name'] . "');
                    window.location.href = '$redirectPage';
                </script>";
            exit();
        } else {
            echo "<script>
                    alert('Invalid password. Please try again.');
                    window.history.back();
                </script>";
        }
    } else {
        echo "<script>
                alert('Account not found.');
                window.history.back();
            </script>";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sarawak Scent</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body class="moving-bg">
    <div class="login-container">

        <?php if (isset($_SESSION['error_message'])): ?>
            <div
                style="background-color: #fff3cd; color: #856404; padding: 15px; text-align: center; border: 1px solid #ffeeba; margin-bottom: 20px; border-radius: 5px;">
                <?php
                echo htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">

            <div class="logo-container">
                <img src="assets/images/Sarawak_Scents_Logo.png" alt="Sarawak Scents Group Logo" class="logo-img">
            </div>

            <h1>Welcome back</h1>
            <p class="subtitle">Sign in to continue</p>

            <form action="login.php" method="post">
                <div class="field">
                    <label class="field-label" for="email">Email</label>
                    <input type="email" name="email" id="email" required />
                </div>

                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <input type="password" name="password" id="password" required />
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
        window.addEventListener("DOMContentLoaded", () => {

            if (sessionStorage.getItem("passwordResetSuccess")) {
                alert("Password reset successfully. Please sign in.");
                sessionStorage.removeItem("passwordResetSuccess");
            }

            if (sessionStorage.getItem("signupSuccess")) {
                alert("Account created successfully. Please sign in.");
                sessionStorage.removeItem("signupSuccess");
            }

        });


    </script>

</body>

</html>