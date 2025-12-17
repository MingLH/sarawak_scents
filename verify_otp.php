<?php
// verify_otp.php
session_start();
header('Content-Type: application/json');

// If a logged-in user tries to hit this endpoint, stop them
if (isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You are already logged in.']);
    exit();
}

include 'includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = mysqli_real_escape_string($conn, $data['email'] ?? '');
$otp_input = mysqli_real_escape_string($conn, $data['otp'] ?? '');

// Check Database for matching Email AND OTP
$sql = "SELECT * FROM users WHERE email = '$email' AND otp_code = '$otp_input'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // MATCH FOUND!

    // Save email in session so reset_password.html knows who to update
    $_SESSION['reset_email'] = $email;

    // Clear the OTP from DB so it can't be used again
    mysqli_query($conn, "UPDATE users SET otp_code = NULL WHERE email = '$email'");

    echo json_encode(['status' => 'success']);
} else {
    // INVALID OTP
    echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
}
?>
