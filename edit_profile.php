<?php
session_start();
include 'includes/db_connect.php';

// 1. SECURITY: Kick out if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$msg_type = ""; // 'success' or 'error'

// 2. FETCH CURRENT USER DATA
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// 3. HANDLE FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize Basic Inputs
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Password Inputs
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // VALIDATION CHECKLIST
    if (empty($name) || empty($phone) || empty($address)) {
        $msg = "Name, Phone, and Address are required.";
        $msg_type = "error";
    } 
    // SERVER-SIDE NAME VALIDATION
    elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $msg = "Full Name must only contain alphabets (A-Z) and spaces.";
        $msg_type = "error";
    }
    // SERVER-SIDE PHONE VALIDATION
    elseif (!preg_match("/^0[1-9]\d{8,9}$/", $phone)) {
        $msg = "Invalid phone number format (must start with 0, 10-11 digits total).";
        $msg_type = "error";
    }
    else {
        // --- LOGIC: PASSWORD CHANGE REQUESTED? ---
        if (!empty($new_password)) {
            // 1. Check if Current Password is correct
            if (!password_verify($current_password, $user['password'])) {
                $msg = "Incorrect current password. Cannot update profile.";
                $msg_type = "error";
            } 
            // 2. Check if New Passwords Match
            elseif ($new_password !== $confirm_password) {
                $msg = "New passwords do not match.";
                $msg_type = "error";
            }
            // 3. Check Password Strength
            elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};\' :\"\\|,.<>\/?])(?=\S+$).{6,8}$/', $new_password)) {
                $msg = "Password must be 6-8 chars, 1 Upper, 1 Number, 1 Special.";
                $msg_type = "error";
            }
            else {
                // SUCCESS: Update EVERYTHING including Password
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET full_name='$name', phone_number='$phone', address='$address', password='$new_hash' WHERE user_id=$user_id";
                
                if (mysqli_query($conn, $update_sql)) {
                    $msg = "Profile and Password updated successfully!";
                    $msg_type = "success";
                    // Refresh user data so the form shows new values
                    $user['full_name'] = $name;
                    $user['phone_number'] = $phone;
                    $user['address'] = $address;
                } else {
                    $msg = "Database error: " . mysqli_error($conn);
                    $msg_type = "error";
                }
            }
        } 
        // --- LOGIC: NO PASSWORD CHANGE (Update Info Only) ---
        else {
            // We still verify current password for security if you wish, 
            // OR strictly require it only for password changes.
            // Based on your previous code, we just update.
            $update_sql = "UPDATE users SET full_name='$name', phone_number='$phone', address='$address' WHERE user_id=$user_id";
            if (mysqli_query($conn, $update_sql)) {
                $msg = "Profile updated successfully!";
                $msg_type = "success";
                $user['full_name'] = $name;
                $user['phone_number'] = $phone;
                $user['address'] = $address;
            } else {
                $msg = "Database error: " . mysqli_error($conn);
                $msg_type = "error";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="edit-container">
    
    <div class="edit-header">
        <h1 class="edit-title">Edit Profile</h1>
        <a href="profile.php" class="back-link-profile">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if ($msg): ?>
        <div class="alert-msg <?php echo ($msg_type == 'success') ? 'alert-success' : 'alert-error'; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" id="editProfileForm" class="edit-grid">
        
        <div class="edit-card">
            <h3 class="edit-subtitle">Personal Details</h3>

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" id="full_name" 
                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required 
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" id="phone" 
                       value="<?php echo htmlspecialchars($user['phone_number']); ?>" required 
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Shipping Address</label>
                <textarea name="address" rows="4" required class="form-input" 
                          style="height: auto;"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <button type="submit" class="btn-save">Save Changes</button>
        </div>

        <div class="edit-card password-card">
            <h3 class="edit-subtitle">Change Password</h3>
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 20px;">
                Leave these blank if you do not want to change your password.
            </p>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" id="new_password" 
                       placeholder="New Password" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" 
                       placeholder="Confirm New Password" class="form-input">
            </div>

            <hr style="margin: 20px 0; border-color: #ddd;">

            <div class="form-group">
                <label class="form-label" style="color: #d35400;">Current Password</label>
                <input type="password" name="current_password" 
                       placeholder="Required for password changes" 
                       class="form-input current-pass-input">
                <small style="color: #888; display:block; margin-top:5px;">
                    Required only if changing password.
                </small>
            </div>
        </div>

    </form>
</div>

<script>
document.getElementById('editProfileForm').addEventListener('submit', function(e) {
    const nameInput = document.getElementById('full_name');
    const phoneInput = document.getElementById('phone');
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    const currentPass = document.querySelector('input[name="current_password"]');
    
    // REGEX PATTERNS
    const namePattern = /^[a-zA-Z\s]+$/;
    const phonePattern = /^0[1-9]\d{8,9}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};':"\\|,.<>/?])(?=\S+$).{6,8}$/;

    // 1. NAME CHECK
    if (!namePattern.test(nameInput.value)) {
        e.preventDefault();
        alert("Full Name must only contain alphabets (A-Z) and spaces.");
        nameInput.focus();
        return;
    }

    // 2. PHONE CHECK
    if (!phonePattern.test(phoneInput.value)) {
        e.preventDefault();
        alert("Invalid phone number format (must start with 0, 10-11 digits total).");
        phoneInput.focus();
        return;
    }

    // 3. PASSWORD CHECK
    if (newPass !== "") {
        if (currentPass.value === "") {
            e.preventDefault();
            alert("Please enter your Current Password to approve this change.");
            currentPass.focus();
            return;
        }

        if (!passwordPattern.test(newPass)) {
            e.preventDefault();
            alert("New Password must be 6-8 characters, include 1 uppercase, 1 number, 1 special character.");
            return;
        }

        if (newPass !== confirmPass) {
            e.preventDefault();
            alert("New passwords do not match.");
            return;
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>