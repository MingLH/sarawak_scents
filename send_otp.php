<?php
// 1. SILENCE HTML ERRORS (Crucial for JSON APIs)
error_reporting(0);
ini_set('display_errors', 0);

// 2. SET HEADER
header('Content-Type: application/json');

try {
    // 3. INCLUDE FILES
    // Use 'require' instead of 'include' to trigger an error if missing
    require 'includes/db_connect.php';
    
    // Note: If check_authorization.php prints HTML (like a redirect), 
    // it will break JSON. Only include if it is strictly code-only.
    // include 'includes/check_authorization.php'; 

    // 4. GET INPUT
    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);
    
    if (!$data) {
        throw new Exception("Invalid JSON received");
    }

    $email = mysqli_real_escape_string($conn, $data['email'] ?? '');

    if (empty($email)) {
        throw new Exception("Email is required");
    }

    // 5. CHECK EMAIL
    $check_query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // 6. GENERATE OTP
        $otp = rand(100000, 999999);
        
        // 7. SAVE TO DB
        $update_sql = "UPDATE users SET otp_code = '$otp' WHERE email = '$email'";
        
        if (mysqli_query($conn, $update_sql)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'OTP sent successfully',
                'debug_otp' => $otp // Show in Console for Localhost testing
            ]);
        } else {
            throw new Exception("Database error: " . mysqli_error($conn));
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email not found in our system']);
    }

} catch (Exception $e) {
    // 8. CATCH ANY CRASH AND RETURN JSON
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// 9. CLEAN UP
if (isset($conn)) mysqli_close($conn);
?>