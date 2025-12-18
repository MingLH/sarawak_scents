const form = document.getElementById("resetForm");
const otpSection = document.getElementById("otpSection");
const actionBtn = document.getElementById("actionBtn");
const subtitle = document.getElementById("subtitle");
const resendLink = document.getElementById("resendCode");
const emailInput = document.getElementById("email");
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

let codeSent = false;
let countdown = 60;
let timer = null;


// --- MAIN FORM SUBMISSION HANDLER ---

form.addEventListener("submit", function (e) {
    e.preventDefault();

    const emailValue = emailInput.value.trim();

    if (!codeSent) {
        // --- STEP 1: SEND CODE (Initial request) ---
        
        // Validation
        if (emailValue === "") {
            alert("Email is required");
            emailInput.focus();
            return;
        }
        if (!emailPattern.test(emailValue)) {
            alert("Please enter a valid email address");
            emailInput.focus();
            return;
        }

        const originalBtnText = actionBtn.textContent;
        actionBtn.textContent = "Sending...";
        actionBtn.disabled = true;

        sendOtpRequest(emailValue, originalBtnText);

    } else {
        // --- STEP 2: VERIFY CODE ---
        
        const otpInputs = document.querySelectorAll(".otp-boxes input");
        let otp = "";
        otpInputs.forEach(input => otp += input.value);

        if (otp.length < 6) {
            alert("Please enter the complete 6-digit code");
            return;
        }

        // Call PHP to verify OTP
        fetch('verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                email: emailValue,
                otp: otp 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Success! Go to reset password page
                window.location.href = "reset_password.php";
            } else {
                alert(data.message); // Invalid code
            }
        });
    }
});


// --- OTP REQUEST FUNCTION (Used by initial send and resend) ---

function sendOtpRequest(emailValue, originalBtnText) {
    fetch('send_otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: emailValue })
    })
    .then(response => response.text()) // Get raw text first
    .then(text => {
        let data;
        try {
            data = JSON.parse(text); // Try to convert to JSON
        } catch (e) {
            console.error('Error: Server returned invalid JSON. Check PHP file for warnings/errors.', text);
            alert("An internal error occurred. Please check the console (F12).");
            actionBtn.textContent = originalBtnText || "Send code";
            actionBtn.disabled = false;
            return;
        }
        
        if (data.status === 'success') {
            // SUCCESS: Setup OTP state
            codeSent = true;
            otpSection.style.display = "block";
            actionBtn.textContent = "Continue";
            actionBtn.disabled = false;
            subtitle.textContent = "Enter the 6-digit code sent to your email";
            emailInput.readOnly = true;
            
            // Start the visual countdown on the first successful send/resend
            startResendCountdown();

            console.log("OTP RECEIVED (Debug):", data.debug_otp);
            alert("Code sent! Please check your Console F12)");
            
        } else {
            // ERROR: Email not found, etc.
            actionBtn.textContent = originalBtnText || "Send code";
            actionBtn.disabled = false;
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error during fetch:', error);
        actionBtn.textContent = originalBtnText || "Send code";
        actionBtn.disabled = false;
        alert("A network error occurred. Please try again.");
    });
}


// --- RESEND OTP LOGIC (Now calls the main sendOtpRequest function) ---

resendLink.addEventListener("click", function (e) {
    e.preventDefault();

    // 1. Prevent action if countdown is running or email is empty
    if (resendLink.classList.contains("disabled")) return;
    
    const emailValue = emailInput.value.trim();
    if (!emailValue) {
        alert("Please enter your email first.");
        return;
    }

    // 2. Call the main function to send the OTP
    sendOtpRequest(emailValue, resendLink.textContent);
});

// --- COUNTDOWN TIMER FUNCTION ---

function startResendCountdown() {
    // Clear any existing timer
    if (timer) clearInterval(timer);
    
    // Reset the countdown state
    countdown = 60;
    resendLink.classList.add("disabled");
    resendLink.textContent = `Resend in ${countdown}s`;

    timer = setInterval(() => {
        countdown--;
        resendLink.textContent = `Resend in ${countdown}s`;

        if (countdown <= 0) {
            clearInterval(timer);
            resendLink.classList.remove("disabled");
            resendLink.textContent = "Resend";
        }
    }, 1000);
}


// --- OTP INPUT LOGIC (Auto-focus, numeric input) ---

document.querySelectorAll(".otp-boxes input").forEach((input, index, inputs) => {
    input.addEventListener("input", () => {
        const digit = input.value.replace(/[^0-9]/g, "");
        input.value = digit;
        if (digit && index < inputs.length - 1) inputs[index + 1].focus();
    });

    input.addEventListener("keydown", (e) => {
        // Backspace → go back
        if (e.key === "Backspace" && !input.value && index > 0) {
            inputs[index - 1].focus();
        }

        // Block non-numeric keys completely
        if (!/[0-9]/.test(e.key) &&
            !["Backspace", "Tab", "ArrowLeft", "ArrowRight"].includes(e.key)) {
            e.preventDefault();
        }
    });
});


// --- BACK BUTTON LOGIC ---

document.getElementById("backBtn").addEventListener("click", function (e) {
    e.preventDefault();
    history.back();
});
