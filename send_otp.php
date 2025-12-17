<?php
// send_otp.php
header('Content-Type: application/json');
include 'includes/db_connect.php';
// Note: If check_authorization.php redirects to login.php, 
// it might break this AJAX request. Usually, guest pages don't need it.

// If a logged-in user tries to hit this endpoint, stop them
if (isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You are already logged in.']);
    exit();
}

// 1. Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Manual Requirement paths for your folder structure
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Get JSON input from the JavaScript fetch request
$data = json_decode(file_get_contents("php://input"), true);
$email = mysqli_real_escape_string($conn, $data['email'] ?? '');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

// 3. Check if email exists
$check_query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) > 0) {
    // 4. Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // 5. Save the OTP to the database
    $update_sql = "UPDATE users SET otp_code = '$otp' WHERE email = '$email'";
    
    if (mysqli_query($conn, $update_sql)) {
        
        // --- SENDING REAL EMAIL START ---
        $mail = new PHPMailer(true);

        try {
            // Server settings using your working configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'javinsim04@gmail.com';
            $mail->Password   = 'qfoxndybjvqdwtkx';    // Your App Password
            $mail->SMTPSecure = 'ssl';                 // SSL for Port 465
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('javinsim04@gmail.com', 'Sarawak Scents');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset OTP Code';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #333;'>Password Reset Request</h2>
                    <p>Hello, use the following code to reset your password:</p>
                    <h1 style='background: #f4f4f4; padding: 10px; display: inline-block; letter-spacing: 5px;'>$otp</h1>
                    <p>This code will expire shortly. If you did not request this, please ignore this email.</p>
                </div>";

            $mail->send();
            
            echo json_encode([
                'status' => 'success',
                'message' => 'OTP has been sent to your email.'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"
            ]);
        }
        // --- SENDING REAL EMAIL END ---

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error saving OTP']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email does not exist in our system']);
}
?>
