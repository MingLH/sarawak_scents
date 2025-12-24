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
// We need the current password hash to verify it later
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
    // --- 🛡️ NEW: SERVER-SIDE NAME VALIDATION ---
    elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $msg = "Full Name must only contain alphabets (A-Z) and spaces.";
        $msg_type = "error";
    }
    // --- 🛡️ NEW: SERVER-SIDE PHONE VALIDATION ---
    // Matches your JS pattern: Starts with 0, followed by 1-9, then 8 or 9 digits (Total 10-11)
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

<div class="container" style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <h1 style="color: #064e3b; margin: 0;">Edit Profile</h1>
        <a href="profile.php" style="text-decoration: none; color: #666;">&larr; Back to Dashboard</a>
    </div>

    <?php if ($msg): ?>
        <div style="padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; 
            background: <?php echo ($msg_type == 'success') ? '#d1fae5' : '#fee2e2'; ?>; 
            color: <?php echo ($msg_type == 'success') ? '#065f46' : '#991b1b'; ?>;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" id="editProfileForm" style="display: flex; gap: 40px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px;">
            <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; color: #333; margin-bottom: 20px;">Personal Details</h3>

                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required 
                       style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Phone Number</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required 
                       style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Shipping Address</label>
                <textarea name="address" rows="4" required 
                          style="width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ccc; border-radius: 4px;"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
        </div>

        <div style="flex: 1; min-width: 300px;">
            <div style="background: #f9fafb; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h3 style="margin-top: 0; color: #333; margin-bottom: 10px;">Change Password</h3>
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 20px;">
                    Leave blank if you do not want to change your password.
                </p>

                <label style="display: block; margin-bottom: 5px; font-weight: bold;">New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="New Password"
                       style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password"
                       style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px;">

                <hr style="margin: 20px 0; border-color: #ddd;">

                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #d35400;">Current Password (Required to Save)</label>
                <input type="password" name="current_password" required placeholder="Enter current password to save changes"
                       style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #d35400; border-radius: 4px; background: #fff5f0;">
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" style="background: #064e3b; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; font-weight: bold;">
                    Save Changes
                </button>
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
    
    // REGEX PATTERNS (Synced with Backend)
    const namePattern = /^[a-zA-Z\s]+$/;
    const phonePattern = /^0[1-9]\d{8,9}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};':"\\|,.<>/?])(?=\S+$).{6,8}$/;

    // 1. NAME CHECK
    if (!namePattern.test(nameInput.value)) {
        e.preventDefault();
        alert("Full Name must only contain alphabets (A-Z) and spaces. Numbers and symbols are not allowed.");
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

    // 3. PASSWORD CHECK (Only if changing)
    if (newPass !== "") {
        if (!passwordPattern.test(newPass)) {
            e.preventDefault();
            alert("Password must be 6-8 characters, include 1 uppercase, 1 number, 1 special character, and no spaces.");
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