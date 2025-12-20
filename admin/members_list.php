<?php
// Placeholder member data array (simulates database table)
//Would be fetched from a database using MySQL
$members = [
    [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'registration_date' => '2023-01-15'
    ],
    [
        'id' => 2,
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'registration_date' => '2023-02-20'
    ],
    [
        'id' => 3,
        'name' => 'Bob Johnson',
        'email' => 'bob.johnson@example.com',
        'registration_date' => '2023-03-10'
    ],
    [
        'id' => 4,
        'name' => 'Alice Brown',
        'email' => 'alice.brown@example.com',
        'registration_date' => '2023-04-05'
    ],
    [
        'id' => 5,
        'name' => 'Charlie Wilson',
        'email' => 'charlie.wilson@example.com',
        'registration_date' => '2023-05-12'
    ]
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f5f5f5; }
        .header { margin: 20px; text-align: center; }
        .nav { margin: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Member List</h1>
    </div>

    <div class="nav">
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>

    <!-- Display member count -->
    <p>Total Members: <?php echo count($members); ?></p>

    <!-- Member list table -->
    <?php if (empty($members)): ?>
        <p>No members found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['id']); ?></td>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['registration_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
