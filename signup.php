<?php
// 1. Enable Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

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

    // --- NEW SECTION: DUPLICATE CHECK ---
    
    // Check if Email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result) > 0) {
        // ERROR: Email found! Stop everything.
    echo "<script>
                alert('This email is already registered. Please use a different email or Log In.');
                window.history.back(); 
              </script>";
        exit();
    }
    
    // --- END NEW SECTION ---

    // 3. Insert Data (Only happens if email check passed)
    $sql = "INSERT INTO users (full_name, phone_number, email, password) 
            VALUES ('$name', '$phone', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        // Success
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
    <title>Create new account</title>


    <link rel="stylesheet" href="css/style.css" />
</head>

<body>

    <a href="#" class="back-link" id="backBtn"><- <span class="back-text">Back</span>
    </a>


    <div class="signup-container">
        <div class="card">
            <h1>Create new account</h1>
            <p class="subtitle">Please fill in the required information</p>
            <form action="signup.php" method="post" name="signupForm" id="signupForm">

                <div class="field">
                    <label>Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" />
                </div>

                <div class="field">
                    <label>Phone number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number e.g. 0123456789" />
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email e.g. abc@gmail.com" />
                </div>

                <div class="field">
                    <label>Password</label>
                    <input type="password" id="password" name="password"
                        placeholder="8 characters with letters and numbers" />
                </div>

                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" id="repassword" name="repassword" />
                </div>

                <div class="terms"> <input type="checkbox" id="accept" /> <label for="accept"> I accept the <a
                            href="terms.html">Terms</a> and <a href="privacy.html">Privacy Policy</a> </label> </div>

                <button type="submit" name="btnSubmit">Sign Up</button>

                <p class="login">
                    Already have an account? <a href="login.php">Log In</a>
                </p>


            </form>

        </div>
    </div>

    <div class="modal" id="exitModal">
        <div class="modal-box">

            <button class="modal-close" id="closeModal">x</button>

            <h3>Leave this page?</h3>
            <p>
                <br>
                All unsaved changes will be gone
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