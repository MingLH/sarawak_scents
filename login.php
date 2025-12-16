<?php
session_start(); // Start a session to remember the user is logged in
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Get data from the form
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 2. Check Database for matching email AND password
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    // 3. Verify Result
    if (mysqli_num_rows($result) === 1) {
        // SUCCESS: User found
        $row = mysqli_fetch_assoc($result);
        
        // Save user info in session variables (to use on other pages)
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['full_name'] = $row['full_name'];

        // Redirect to your homepage/dashboard
        echo "<script>
                alert('Login Successful! Welcome " . $row['full_name'] . "');
                window.location.href = 'index.php'; 
              </script>";
    } else {
        // FAILURE: No match found
        echo "<script>
                alert('Invalid email or password. Please try again.');
                window.location.href = 'login.php';
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

<body>
    <div class="login-container">
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
