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
    $role = mysqli_real_escape_string($conn, $roleFilter);
    $query .= " WHERE role = '$role'";
}

$query .= " ORDER BY user_id DESC"; 
$result = mysqli_query($conn, $query);

$totalUsers = mysqli_num_rows($result);
?>

<?php include 'includes/header.php'; ?>

    <div class="admin-header">
        <h1 style="color: #333; margin: 0;">Members List</h1>
        
        <form method="get" class="filter-group">
            <span style="font-weight:bold; color:#555;">Filter by Role:</span>
            <select name="role" onchange="this.form.submit()" class="filter-select">
                <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>All Users</option>
                <option value="member" <?php echo $roleFilter === 'member' ? 'selected' : ''; ?>>Members Only</option>
                <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admins Only</option>
            </select>
        </form>
    </div>

    <div class="card">
        <div style="padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 15px;">
            <h3 style="margin:0; color:#555;">Registered Users (<?php echo $totalUsers; ?>)</h3>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Contact Info</th>
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
                                <td><strong>#<?php echo htmlspecialchars($user['user_id']); ?></strong></td>
                                
                                <td>
                                    <div style="font-weight:bold; font-size:1rem; color:#333;">
                                        <?php echo htmlspecialchars($user['full_name']); ?>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge-admin">ADMIN</span>
                                    <?php else: ?>
                                        <span class="badge-member">MEMBER</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="user-contact">
                                        <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                                        <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone_number'] ?? '-'); ?></div>
                                    </div>
                                </td>

                                <td style="white-space: nowrap;">
                                    <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                                </td>

                                <td style="font-size:0.85rem; color:#666; max-width: 250px;">
                                    <?php 
                                        $addr = $user['address'] ?? '';
                                        echo (strlen($addr) > 50) ? htmlspecialchars(substr($addr, 0, 50)) . '...' : htmlspecialchars($addr); 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>