<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/sarawak_scents/includes/db_connect.php';

// Get filter from GET parameter, default to 'all'
$roleFilter = $_GET['role'] ?? 'all';

// Function to get filtered users from database
function getFilteredUsers($conn, $roleFilter) {
    $query = "SELECT user_id, full_name, email, phone_number, role, address, created_at 
              FROM users";
    
    // Add role filter if not 'all'
    if ($roleFilter !== 'all') {
        $query .= " WHERE role = '" . mysqli_real_escape_string($conn, $roleFilter) . "'";
    }
    
    // Order by created date descending (newest first)
    $query .= " ORDER BY created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}

// Get filtered users
$users = getFilteredUsers($conn, $roleFilter);

// Count total users
$totalUsers = count($users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members List - Admin Module</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f5f5f5; }
        .nav { margin: 20px; text-align: center; }
        .header { margin: 20px; text-align: center; }
        .role-admin { background-color: #ffe6e6; font-weight: bold; }
        .role-member { background-color: #e6f3ff; }
        .filter-form { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Members List</h1>
    </div>

    <div class="nav">
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>

    <h2>User Management</h2>

    <!-- Filter form -->
    <div class="filter-form">
        <form method="get" action="">
            <label for="role">Filter by Role:</label>
            <select name="role" id="role">
                <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>All Users</option>
                <option value="member" <?php echo $roleFilter === 'member' ? 'selected' : ''; ?>>Members Only</option>
                <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admins Only</option>
            </select>
            <button type="submit">Apply Filter</button>
        </form>
    </div>

    <!-- Display user count -->
    <p><strong>Total Users: <?php echo $totalUsers; ?></strong></p>

    <!-- Users table -->
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Role</th>
                <th>Address</th>
                <th>Registered Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr class="role-<?php echo strtolower($user['role']); ?>">
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></td>
                        <td><strong><?php echo strtoupper($user['role']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($user['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>