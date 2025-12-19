//THIS FILE IS FOR USER AUTHETICATION AND MODEL BOX (WARNING POP UP)

// REGULAR EXPRESSIONS (Global access)
const namePattern = /^[a-zA-Z\s]+$/; // Allows A-Z, a-z, and spaces only
const phonePattern = /^0[1-9]\d{8,9}$/;
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};':"\\|,.<>/?])(?=\S+$).{6,8}$/;


// SIGNUP VALIDATION (Using Event Listener)
const signupForm = document.getElementById("signupForm");

if (signupForm) {
  signupForm.addEventListener("submit", function handleSignup(e) {
    e.preventDefault(); // Stop the form from submitting normally

    // Use the 'name' attribute collection for easy access (f.name, f.email, etc.)
    const f = e.target;

    // 1. FULL NAME CHECK
    if (f.name.value.trim() === "") {
      alert("Full Name is required");
      f.name.focus();
      return;
    }
    if (!namePattern.test(f.name.value)) {
      alert("Full Name must only contain alphabets (A-Z) and spaces. Numbers and symbols are not allowed.");
      f.name.focus();
      return;
    }

    // 2. PHONE NUMBER CHECK
    if (f.phone.value.trim() === "") {
      alert("Phone number is required");
      f.phone.focus();
      return;
    }
    if (!phonePattern.test(f.phone.value)) {
      alert("Invalid phone number format (must start with 0, 9-10 digits total)");
      f.phone.focus();
      return;
    }

    // 3. EMAIL CHECK
    if (f.email.value.trim() === "") {
      alert("Email is required");
      f.email.focus();
      return;
    }
    if (!emailPattern.test(f.email.value)) {
      alert("Invalid email format (e.g., user@domain.com)");
      f.email.focus();
      return;
    }

    // 4. PASSWORD CHECK
    if (f.password.value.trim() === "") {
      alert("Password is required");
      f.password.focus();
      return;
    }

    if (!passwordPattern.test(f.password.value)) {
      alert("Password must be 6-8 characters, include 1 uppercase, 1 number, 1 special character, and no spaces.");
      f.password.focus();
      return;
    }

    // 5. CONFIRM PASSWORD CHECK
    if (f.repassword.value.trim() === "") {
      alert("Confirm Password is required");
      f.repassword.focus();
      return;
    }
    if (f.password.value !== f.repassword.value) {
      alert("Passwords do not match");
      f.repassword.focus();
      return;
    }

    // 6. TERMS CHECK
    if (!f.accept.checked) {
      alert("You must accept the Terms and Privacy Policy");
      return;
    }

    // SUCCESS: Allow the form to submit to register.php
    f.submit();
  });
}

// RESET PASSWORD LOGIC (For reset_password.html)
const resetForm = document.getElementById("resetForm");
const resetBtn = document.getElementById("resetBtn");

if (resetForm && resetBtn) {
  resetBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const passwordInput = document.getElementById("password");
    const repasswordInput = document.getElementById("repassword");
    const password = passwordInput.value;
    const repassword = repasswordInput.value;

    // 1. PASSWORD EMPTY CHECK
    if (password.trim() === "") {
      alert("Password is required");
      passwordInput.focus();
      return;
    }

    // 2. PASSWORD STRENGTH CHECK
    if (!passwordPattern.test(password)) {
      alert(
        "Password must be 6-8 characters and include at least one uppercase letter, one number, and one special character (no spaces)."
      );
      passwordInput.focus();
      return;
    }

    // 3. CONFIRM PASSWORD EMPTY CHECK
    if (repassword.trim() === "") {
      alert("Confirm Password is required");
      repasswordInput.focus();
      return;
    }

    // 4. MATCH CHECK
    if (password !== repassword) {
      alert("Passwords do not match.");
      repasswordInput.focus();
      return;
    }

    // SUCCESS: Allow the form to submit to update_password.php
    resetForm.submit();
  });
}

// EXIT WARNING MODAL (SAFE)
const backBtn = document.getElementById("backBtn");
const modal = document.getElementById("exitModal");
const closeModal = document.getElementById("closeModal");
const stayBtn = document.getElementById("stayBtn");
const leaveBtn = document.getElementById("leaveBtn");

if (backBtn && modal && closeModal && stayBtn && leaveBtn) {

  backBtn.addEventListener("click", function (e) {
    e.preventDefault();
    modal.style.display = "flex"; // SHOWS THE MODAL
  });

  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
  });

  stayBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  leaveBtn.addEventListener("click", () => {
    window.location.href = "login.php"; // LEAVES TO LOGIN PAGE
  });
}
