<?php
// Start PHP session to maintain product data across requests (placeholder for database)

//session_start(); to test the logic enable this line

// Initialize products array because not yet have database table
if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        [
            'id' => 1,
            'name' => 'Sample Product 1',
            'price' => 10.99,
            'description' => 'This is a sample product.',
            'image' => 'sample1.jpg'
        ],
        [
            'id' => 2,
            'name' => 'Sample Product 2',
            'price' => 15.49,
            'description' => 'Another sample product.',
            'image' => 'sample2.jpg'
        ]
    ];
}

// Function to get next product ID (simulates auto-increment in database)
function getNextId() {
    $maxId = 0;
    foreach ($_SESSION['products'] as $product) {
        if ($product['id'] > $maxId) {
            $maxId = $product['id'];
        }
    }
    return $maxId + 1;
}

// Handle form submission for adding or editing a product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = '';

    // Placeholder image upload logic
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Placeholder directory
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = $_FILES['image']['name']; // Store filename (in real DB, store path)
        } else {
            // Handle upload error (placeholder)
            $image = 'default.jpg';
        }
    }

    if (isset($_POST['edit_id'])) {
        // Edit existing product
        $editId = (int)$_POST['edit_id'];
        foreach ($_SESSION['products'] as &$product) {
            if ($product['id'] === $editId) {
                $product['name'] = $name;
                $product['price'] = (float)$price;
                $product['description'] = $description;
                if ($image) $product['image'] = $image;
                break;
            }
        }
    } else {
        // Add new product
        $newProduct = [
            'id' => getNextId(),
            'name' => $name,
            'price' => (float)$price,
            'description' => $description,
            'image' => $image ?: 'default.jpg'
        ];
        $_SESSION['products'][] = $newProduct;
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete request via GET
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $_SESSION['products'] = array_filter($_SESSION['products'], function($product) use ($deleteId) {
        return $product['id'] !== $deleteId;
    });
    // Redirect to avoid URL manipulation issues
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit request via GET (populate form)
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($_SESSION['products'] as $product) {
        if ($product['id'] === $editId) {
            $editProduct = $product;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Module</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f5f5f5; }
        .edit-link, .delete-link { color: blue; text-decoration: none; }
        .delete-link { color: red; }
        .header { margin: 20px; text-align: center; }
        .nav { margin: 20px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Add Product</h1>
    </div>

    <div class="nav">
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>

    <!-- Form for adding or editing a product -->
    <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>

    <form action="" method="post" enctype="multipart/form-data">
        <?php if ($editProduct): ?>
            <input type="hidden" name="edit_id" value="<?php echo $editProduct['id']; ?>">
        <?php endif; ?>
        <label for="name">Product Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $editProduct['name'] ?? ''; ?>" required><br><br>

        <label for="price">Price:</label><br>
        <input type="number" id="price" name="price" step="0.01" value="<?php echo $editProduct['price'] ?? ''; ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required><?php echo $editProduct['description'] ?? ''; ?></textarea><br><br>

        <label for="image">Product Image:</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <button type="submit"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
    </form>

    <!-- Display existing products in a table -->
    <h2>Existing Products</h2>
    <?php if (empty($_SESSION['products'])): ?>
        <p>No products found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['products'] as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo htmlspecialchars($product['image']); ?></td>
                        <td>
                            <a href="?edit=<?php echo $product['id']; ?>" class="edit-link">Edit</a> |
                            <a href="?delete=<?php echo $product['id']; ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
