<?php
// --- LOGIC SECTION (UNTOUCHED) ---
include 'includes/db_connect.php';
include 'includes/check_authorization.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Get and Sanitize Data
    $name     = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['repassword'] ?? '';

    // Check empty fields
    if (empty($name) || empty($email) || empty($password)) {
        die("Error: Required fields are missing. <a href='signup.php'>Go Back</a>");
    }

    // Check Passwords Match
    if ($password != $repassword) {
        die("Error: Passwords do not match. <a href='signup.php'>Go Back</a>");
    }

    // --- DUPLICATE CHECK ---
    $check_email = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>
                alert('This email is already registered. Please use a different email or Log In.');
                window.history.back(); 
            </script>";
        exit();
    }
    
    // --- PASSWORD HASHING ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insert Data 
    $sql = "INSERT INTO users (full_name, email, password, phone_number, role) 
            VALUES ('$name', '$email', '$hashed_password', '$phone', 'member')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                sessionStorage.setItem('signupSuccess', 'true');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "<h3>Database Error</h3>";
        echo "Error description: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account - Sarawak Scents</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

    <a href="index.php" class="back-link" id="backBtn">
        <span>&larr;</span> Back
    </a>

    <div class="auth-container">
        <div class="card"> <div class="logo-container">
                <img src="assets/images/Sarawak_Scents_Logo.png" alt="Logo">
            </div>

            <h1>Create new account</h1>
            <p class="subtitle">Please fill in the required information</p>
            
            <form action="signup.php" method="post" name="signupForm" id="signupForm">

                <div class="field">
                    <label>Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required />
                </div>

                <div class="field">
                    <label>Phone number</label>
                    <input type="tel" id="phone" name="phone" placeholder="e.g. 0123456789" />
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" id="email" name="email" placeholder="e.g. user@example.com" required />
                </div>

                <div class="field">
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="Min 8 chars, letters & numbers" required />
                </div>

                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" id="repassword" name="repassword" required />
                </div>

                <div class="field" style="display:flex; align-items:center; gap:10px;"> 
                    <input type="checkbox" id="accept" style="width:auto;" required /> 
                    <label for="accept" style="margin:0; font-weight:normal; font-size:0.9rem;">
                        I accept the <a href="terms.html" style="color:#064e3b; font-weight:bold;">Terms</a> and <a href="privacy.html" style="color:#064e3b; font-weight:bold;">Privacy Policy</a>
                    </label> 
                </div>

                <button type="submit" name="btnSubmit">Sign Up</button>

                <p class="signup">
                    Already have an account? <a href="login.php">Log In</a>
                </p>

            </form>
        </div>
    </div>

    <div class="modal" id="exitModal">
        <div class="modal-box">
            <button class="modal-close" id="closeModal" style="position:absolute; top:10px; right:15px; background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            <h3 style="margin-top:0;">Leave this page?</h3>
            <p style="color:#666; margin-bottom:20px;">All unsaved changes will be gone</p>
            
            <div class="modal-actions">
                <button class="btn-secondary" id="stayBtn">Stay</button>
                
                <button class="btn-primary" id="leaveBtn" data-redirect="index.php" style="margin-top:0;">Continue</button>
            </div>
        </div>
    </div>

    <script src="js/userAuth.js"></script>

</body>
</html>