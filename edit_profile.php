<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// HANDLE FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Update DB
    $sql = "UPDATE users SET full_name='$name', phone_number='$phone', address='$address' WHERE user_id=$user_id";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div style='color: green; margin-bottom: 15px;'>Profile updated successfully!</div>";
    } else {
        $msg = "<div style='color: red; margin-bottom: 15px;'>Error updating profile.</div>";
    }
}

// Fetch Current Data
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));

include 'includes/header.php';
?>

<div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
    <h1 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">Edit Profile</h1>
    <?php echo $msg; ?>
    
    <form method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required 
               style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required 
               style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Shipping Address</label>
        <textarea name="address" rows="4" required 
                  style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px;"><?php echo htmlspecialchars($user['address']); ?></textarea>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #064e3b; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer;">Save Changes</button>
            <a href="profile.php" style="padding: 10px 25px; color: #666; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
