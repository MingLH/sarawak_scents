<?php
include 'includes/check_authorization.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset password</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <a href="login.php" class="back-link" id="backBtn"><- <span class="back-text">Back</span></a>

    <div class="login-container">
        <div class="card">
            <h1>Reset your password</h1>
            <p class="subtitle" id="subtitle">
                Please enter your email to receive a verification code
            </p>

            <form name="resetForm" id="resetForm">

                <div id="statusMessage" style="color: red; margin-bottom: 15px; text-align: center;"></div>

                <div class="field">
                    <label class="field-label" for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email here" required />
                </div>

                <div class="otp-section" id="otpSection" style="display: none;">

                    <div class="otp-header">
                        <label class="otp-label">Verification code</label>
                        <p class="otp-text">
                            Please enter the 6-digit code sent to your email
                        </p>
                    </div>

                    <div class="otp-boxes">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" data-otp-index="0" />
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" data-otp-index="1" />
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" data-otp-index="2" />
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" data-otp-index="3" />
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" data-otp-index="4" />
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" data-otp-index="5" />
                    </div>

                    <p class="developer-debug-link">
                        Developer Hint: <a href="otp_tutorial.html">How to view the OTP code</a>
                    </p>

                    <p class="resend">
                        Didn’t receive code?
                        <a href="#" id="resendCode">Resend</a>
                    </p>

                </div>

                <button type="submit" id="actionBtn">Send code</button>
            </form>
        </div>
    </div>
    <script src="js/userAuthfunction.js"></script>
</body>

</html>
