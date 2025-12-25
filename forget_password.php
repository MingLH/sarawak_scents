<?php include 'includes/check_authorization.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password - Sarawak Scents</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

    <a href="login.php" class="back-link">
        <span>&larr;</span> Back
    </a>

    <div class="auth-container">
        <div class="card">
            <h1>Reset Password</h1>
            <p class="subtitle" id="subtitle">Enter your email to receive a verification code</p>

            <form id="otpForm"> 

                <div class="field">
                    <label>Email Address</label>
                    <input type="email" id="email" name="email" placeholder="user@example.com" required />
                </div>

                <div class="otp-section" id="otpSection" style="display: none;">
                    
                    <div style="margin: 20px 0;">
                        <label style="font-weight:600;">Verification Code</label>
                        <p style="font-size:0.9rem; color:#666;">Enter the 6-digit code sent to your email.</p>
                    </div>

                    <div class="otp-boxes">
                        <input type="tel" maxlength="1">
                        <input type="tel" maxlength="1">
                        <input type="tel" maxlength="1">
                        <input type="tel" maxlength="1">
                        <input type="tel" maxlength="1">
                        <input type="tel" maxlength="1">
                    </div>

                    <p class="developer-debug-link">
                        Developer Hint: <a href="otp_tutorial.html" target="_blank" style="text-decoration:underline;">How to view code (Localhost)</a>
                    </p>

                    <p class="resend">
                        Didn’t receive code? <a href="#" id="resendCode">Resend</a>
                    </p>
                </div>

                <button type="button" id="actionBtn" class="btn-primary">Send Code</button>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>