<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/sarawak_scents/includes/db_connect.php';

// Handle form submission for adding or editing a product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : NULL;
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/sarawak_scents/assets/images/';
        
        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = $fileName;
        } else {
            $image = 'default.jpg';
        }
    }

    if (isset($_POST['edit_id'])) {
        // Edit existing product
        $editId = (int)$_POST['edit_id'];
        
        if ($image) {
            // Update with new image
            $query = "UPDATE products SET 
                      name = '$name', 
                      price = $price, 
                      description = '$description', 
                      category_id = " . ($category_id ? $category_id : "NULL") . ", 
                      image = '$image' 
                      WHERE product_id = $editId";
        } else {
            // Update without changing image
            $query = "UPDATE products SET 
                      name = '$name', 
                      price = $price, 
                      description = '$description', 
                      category_id = " . ($category_id ? $category_id : "NULL") . " 
                      WHERE product_id = $editId";
        }
        
        if (mysqli_query($conn, $query)) {
            $success_msg = "Product updated successfully!";
        } else {
            $error_msg = "Error updating product: " . mysqli_error($conn);
        }
    } else {
        // Add new product
        $imageValue = $image ?: 'default.jpg';
        $query = "INSERT INTO products (name, price, description, category_id, image) 
                  VALUES ('$name', $price, '$description', " . ($category_id ? $category_id : "NULL") . ", '$imageValue')";
        
        if (mysqli_query($conn, $query)) {
            $success_msg = "Product added successfully!";
        } else {
            $error_msg = "Error adding product: " . mysqli_error($conn);
        }
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete request via GET
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $query = "DELETE FROM products WHERE product_id = $deleteId";
    
    if (mysqli_query($conn, $query)) {
        $success_msg = "Product deleted successfully!";
    } else {
        $error_msg = "Error deleting product: " . mysqli_error($conn);
    }
    
    // Redirect to avoid URL manipulation issues
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit request via GET (populate form)
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $query = "SELECT * FROM products WHERE product_id = $editId";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $editProduct = mysqli_fetch_assoc($result);
    }
}

// Fetch all products from database
$query = "SELECT p.*, c.category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          ORDER BY p.created_at DESC";
$productsResult = mysqli_query($conn, $query);

if (!$productsResult) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch all categories for dropdown
$categoriesQuery = "SELECT * FROM categories ORDER BY category_name";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
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
        .success { color: green; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; margin: 10px 0; }
        .error { color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; margin: 10px 0; }
        .product-image { width: 100px; height: auto; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Add Product</h1>
    </div>

    <div class="nav">
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>

    <?php if (isset($success_msg)): ?>
        <div class="success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
        <div class="error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Form for adding or editing a product -->
    <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>

    <form action="" method="post" enctype="multipart/form-data">
        <?php if ($editProduct): ?>
            <input type="hidden" name="edit_id" value="<?php echo $editProduct['product_id']; ?>">
        <?php endif; ?>
        
        <label for="name">Product Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" required><br><br>

        <label for="price">Price (RM):</label><br>
        <input type="number" id="price" name="price" step="0.01" value="<?php echo $editProduct['price'] ?? ''; ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea><br><br>

        <label for="category_id">Category:</label><br>
        <select id="category_id" name="category_id">
            <option value="">-- Select Category (Optional) --</option>
            <?php 
            if ($categoriesResult && mysqli_num_rows($categoriesResult) > 0):
                while ($category = mysqli_fetch_assoc($categoriesResult)): 
            ?>
                <option value="<?php echo $category['category_id']; ?>" 
                    <?php echo (isset($editProduct['category_id']) && $editProduct['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php 
                endwhile;
            endif;
            ?>
        </select><br><br>

        <label for="image">Product Image:</label><br>
        <?php if ($editProduct && $editProduct['image']): ?>
            <p>Current image: <strong><?php echo htmlspecialchars($editProduct['image']); ?></strong></p>
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/*"><br>
        <small>Leave empty to keep current image (when editing)</small><br><br>

        <button type="submit"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
        <?php if ($editProduct): ?>
            <button type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">Cancel Edit</button>
        <?php endif; ?>
    </form>

    <!-- Display existing products in a table -->
    <h2>Existing Products</h2>
    <?php if (!$productsResult || mysqli_num_rows($productsResult) == 0): ?>
        <p>No products found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price (RM)</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = mysqli_fetch_assoc($productsResult)): ?>
                    <tr>
                        <td><?php echo $product['product_id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>RM <?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . (strlen($product['description']) > 50 ? '...' : ''); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($product['image'] && $product['image'] != 'default.jpg'): ?>
                                <img src="/sarawak_scents/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d-m-Y H:i', strtotime($product['created_at'])); ?></td>
                        <td>
                            <a href="?edit=<?php echo $product['product_id']; ?>" class="edit-link">Edit</a> |
                            <a href="?delete=<?php echo $product['product_id']; ?>" class="delete-link" 
                               onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>