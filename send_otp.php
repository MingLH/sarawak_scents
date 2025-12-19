<?php
// send_otp.php
header('Content-Type: application/json');
include 'includes/db_connect.php';
include 'includes/check_authorization.php';

// Get JSON input from the JavaScript fetch request
$data = json_decode(file_get_contents("php://input"), true);
$email = mysqli_real_escape_string($conn, $data['email'] ?? '');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

// 1. Check if email exists
$check_query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) > 0) {
    // 2. Email exists! Generate a 6-digit OTP
    $otp = rand(100000, 999999);

    // 3. Save the OTP to the database for this user
    $update_sql = "UPDATE users SET otp_code = '$otp' WHERE email = '$email'";
    
    if (mysqli_query($conn, $update_sql)) {
        // 4. Send otp, but we gonna use developer first as email requires a configured mail server)

        // NOTE: We send the OTP in the 'debug' field so you can see it in the Console!
        echo json_encode([
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'debug_otp' => $otp
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error saving OTP']);
    }
} else {
    // Email not found
    echo json_encode(['status' => 'error', 'message' => 'Email does not exist in our system']);
}
?>
