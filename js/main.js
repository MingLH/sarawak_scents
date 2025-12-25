document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. GLOBAL VARIABLES & PATTERNS
    // ==========================================
    const patterns = {
        name: /^[a-zA-Z\s]+$/,
        phone: /^0[1-9]\d{8,9}$/,
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
        password: /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};':"\\|,.<>/?])(?=\S+$).{6,8}$/
    };

    // ==========================================
    // 2. SMART BACK BUTTON
    // ==========================================
    const backBtn = document.getElementById("backBtn");
    const exitModal = document.getElementById("exitModal");

    if (backBtn) {
        backBtn.addEventListener("click", (e) => {
            e.preventDefault();
            // If page has a modal, show it. Otherwise, just go back.
            if (exitModal) {
                exitModal.style.display = "flex";
            } else {
                // Check if there is a specific redirect needed (e.g., from Terms page)
                const fallback = backBtn.getAttribute('href') || '#';
                if(fallback !== '#') window.location.href = fallback;
                else history.back();
            }
        });
    }

    // Modal Actions (Stay/Leave)
    if (exitModal) {
        document.getElementById("closeModal")?.addEventListener("click", () => exitModal.style.display = "none");
        document.getElementById("stayBtn")?.addEventListener("click", () => exitModal.style.display = "none");
        
        document.getElementById("leaveBtn")?.addEventListener("click", function() {
            const dest = this.getAttribute("data-redirect");
            window.location.href = dest ? dest : "login.php";
        });
    }

    // ==========================================
    // 3. SIGNUP FORM VALIDATION
    // ==========================================
    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        signupForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const f = e.target;

            // Name
            if (!f.name.value.trim()) return alertAndFocus("Full Name is required", f.name);
            if (!patterns.name.test(f.name.value)) return alertAndFocus("Name must be alphabets only", f.name);

            // Phone
            if (!f.phone.value.trim()) return alertAndFocus("Phone is required", f.phone);
            if (!patterns.phone.test(f.phone.value)) return alertAndFocus("Invalid phone (e.g. 0123456789)", f.phone);

            // Email
            if (!f.email.value.trim()) return alertAndFocus("Email is required", f.email);
            if (!patterns.email.test(f.email.value)) return alertAndFocus("Invalid email address", f.email);

            // Password
            if (!f.password.value.trim()) return alertAndFocus("Password is required", f.password);
            if (!patterns.password.test(f.password.value)) return alertAndFocus("Password: 6-8 chars, 1 Upper, 1 Number, 1 Special", f.password);

            // Confirm Password
            if (f.password.value !== f.repassword.value) return alertAndFocus("Passwords do not match", f.repassword);

            // Terms
            if (!f.accept.checked) return alert("Please accept Terms & Privacy Policy");

            f.submit();
        });
    }

    function alertAndFocus(msg, field) {
        alert(msg);
        field.focus();
    }

    // ==========================================
    // 4. RESET PASSWORD FORM
    // ==========================================
    const resetForm = document.getElementById("resetForm");
    if (resetForm) {
        resetForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const pass = document.getElementById("password");
            const repass = document.getElementById("repassword");

            if (!patterns.password.test(pass.value)) return alertAndFocus("Password: 6-8 chars, 1 Upper, 1 Number, 1 Special", pass);
            if (pass.value !== repass.value) return alertAndFocus("Passwords do not match", repass);

            resetForm.submit();
        });
    }

    // ==========================================
    // 5. FORGOT PASSWORD (OTP LOGIC)
    // ==========================================
    const otpForm = document.getElementById("otpForm"); // Note: You need to add id="otpForm" to the <form> tag in PHP
    const otpSection = document.getElementById("otpSection");
    const actionBtn = document.getElementById("actionBtn");
    
    if (otpSection && actionBtn) {
        let codeSent = false;
        let timer = null;
        
        // Handle Button Click (Send Code OR Verify Code)
        actionBtn.addEventListener("click", function(e) {
            e.preventDefault();
            const emailInput = document.getElementById("email");
            const emailVal = emailInput.value.trim();

            if (!codeSent) {
                // --- PHASE 1: SEND CODE ---
                if (!patterns.email.test(emailVal)) return alertAndFocus("Valid email required", emailInput);
                
                actionBtn.textContent = "Sending...";
                actionBtn.disabled = true;

                fetch('send_otp.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({email: emailVal})
                })
                .then(res => res.json())
                .then(data => {
                    actionBtn.disabled = false;
                    if (data.status === 'success') {
                        codeSent = true;
                        otpSection.style.display = "block";
                        actionBtn.textContent = "Verify & Continue";
                        document.getElementById("subtitle").textContent = "Enter code sent to " + emailVal;
                        emailInput.readOnly = true;
                        startCountdown();
                        console.log("DEBUG OTP:", data.debug_otp); // For testing
                        alert("Code sent! Check Console (F12) since we are on localhost.");
                    } else {
                        actionBtn.textContent = "Send Code";
                        alert(data.message);
                    }
                })
                .catch(err => {
                    actionBtn.disabled = false;
                    actionBtn.textContent = "Send Code";
                    alert("System Error: Check console.");
                    console.error(err);
                });

            } else {
                // --- PHASE 2: VERIFY CODE ---
                let otp = "";
                document.querySelectorAll(".otp-boxes input").forEach(input => otp += input.value);
                
                if (otp.length < 6) return alert("Please enter complete 6-digit code");

                fetch('verify_otp.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({email: emailVal, otp: otp})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = "reset_password.php";
                    } else {
                        alert(data.message);
                    }
                });
            }
        });

        // OTP Auto-Focus Logic
        const otpInputs = document.querySelectorAll(".otp-boxes input");
        otpInputs.forEach((input, index) => {
            input.addEventListener("input", (e) => {
                input.value = input.value.replace(/[^0-9]/, ''); // Numbers only
                if(input.value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
            });
            input.addEventListener("keydown", (e) => {
                if(e.key === "Backspace" && !input.value && index > 0) otpInputs[index - 1].focus();
            });
        });

        // Resend Timer Logic
        const resendLink = document.getElementById("resendCode");
        function startCountdown() {
            let count = 60;
            resendLink.style.pointerEvents = "none";
            resendLink.style.color = "#999";
            
            clearInterval(timer);
            timer = setInterval(() => {
                resendLink.textContent = `Resend in ${count}s`;
                count--;
                if(count < 0) {
                    clearInterval(timer);
                    resendLink.textContent = "Resend Code";
                    resendLink.style.pointerEvents = "auto";
                    resendLink.style.color = "";
                }
            }, 1000);
        }
        
        resendLink.addEventListener("click", (e) => {
            e.preventDefault();
            // Trigger the Send Logic again
            codeSent = false; 
            actionBtn.click();
        });
    }

    // ==========================================
    // 6. IMAGE MODAL (Tutorial)
    // ==========================================
    const imgModal = document.getElementById("imgModal");
    if(imgModal) {
        document.querySelectorAll(".zoomable").forEach(img => {
            img.addEventListener("click", () => {
                imgModal.style.display = "flex";
                document.getElementById("modalImg").src = img.src;
            });
        });
        imgModal.onclick = () => imgModal.style.display = "none";
    }

    // ==========================================
    // 7. MOBILE NAVBAR TOGGLE
    // ==========================================
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            // Optional: Animate the icon from bars to X
            const icon = hamburger.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
});