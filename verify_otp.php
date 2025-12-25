<?php
// 1. SILENCE HTML ERRORS
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    require 'includes/db_connect.php';

    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);

    $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
    $otp_input = mysqli_real_escape_string($conn, $data['otp'] ?? '');

    if (empty($email) || empty($otp_input)) {
        throw new Exception("Email and Code are required");
    }

    // 2. CHECK MATCH
    $sql = "SELECT * FROM users WHERE email = '$email' AND otp_code = '$otp_input'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // 3. SUCCESS
        $_SESSION['reset_email'] = $email;
        
        // Clear OTP
        mysqli_query($conn, "UPDATE users SET otp_code = NULL WHERE email = '$email'");

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired code']);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

if (isset($conn)) mysqli_close($conn);
?>