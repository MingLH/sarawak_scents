<?php
session_start();
include '../includes/db_connect.php';

// 1. SECURITY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. FILTER LOGIC
$roleFilter = $_GET['role'] ?? 'all';

$query = "SELECT user_id, full_name, email, phone_number, role, address, created_at 
          FROM users";

if ($roleFilter !== 'all') {
    // Secure input just in case
    $role = mysqli_real_escape_string($conn, $roleFilter);
    $query .= " WHERE role = '$role'";
}

$query .= " ORDER BY user_id DESC"; // Show newest members first
$result = mysqli_query($conn, $query);

$totalUsers = mysqli_num_rows($result);
?>

<?php include 'includes/header.php'; ?>

    <h1 style="color: #333;">Members List</h1>

    <div class="card" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 25px;">
        <h3 style="margin:0; color:#555;">Registered Users (<?php echo $totalUsers; ?>)</h3>
        
        <form method="get" style="margin:0;">
            <select name="role" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border:1px solid #ddd;">
                <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>All Roles</option>
                <option value="member" <?php echo $roleFilter === 'member' ? 'selected' : ''; ?>>Members Only</option>
                <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admins Only</option>
            </select>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Contact Info</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalUsers == 0): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 20px;">No users found.</td></tr>
                <?php else: ?>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                            </td>
                            <td>
                                <div style="font-size:0.9rem;">📧 <?php echo htmlspecialchars($user['email']); ?></div>
                                <div style="font-size:0.9rem; color:#666;">📞 <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></div>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span style="background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:15px; font-weight:bold; font-size:0.8rem;">ADMIN</span>
                                <?php else: ?>
                                    <span style="background:#e0f2fe; color:#0369a1; padding:4px 10px; border-radius:15px; font-weight:bold; font-size:0.8rem;">MEMBER</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                            <td style="font-size:0.9rem; color:#666; max-width: 200px;">
                                <?php echo htmlspecialchars(substr($user['address'] ?? '', 0, 50)) . '...'; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include 'includes/footer.php'; ?>