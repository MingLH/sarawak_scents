<?php
/*session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}*/

// Placeholder for products (replace with database later)
$products = [
    ['id' => 1, 'name' => 'Sample Product 1', 'price' => 10.99, 'stock' => 50, 'description' => 'Description 1', 'image' => ''],
    ['id' => 2, 'name' => 'Sample Product 2', 'price' => 20.99, 'stock' => 30, 'description' => 'Description 2', 'image' => ''],
];

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission for adding product
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $image = $imagePath;
        }
    }

    // Placeholder: Add to array (replace with database insert)
    $newId = count($products) + 1;
    $products[] = ['id' => $newId, 'name' => $name, 'price' => $price, 'stock' => $stock, 'description' => $description, 'image' => $image];
    $message = 'Product added successfully!';
}

// Handle delete (placeholder)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Placeholder: Remove from array (replace with database delete)
    $products = array_filter($products, function($product) use ($id) {
        return $product['id'] != $id;
    });
    $message = 'Product deleted successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .nav {
            margin: 20px;
            text-align: center;
        }

        .nav button {
            padding: 8px 16px;
            margin: 0 10px;
            cursor: pointer;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        form {
            margin-bottom: 20px;
        }

        form input, form textarea {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .message {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Add Product</h1>
    </div>
    <div class="nav">
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>
    <div class="container">
        <h2>Add New Product</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Add Product</button>
        </form>

        <h2>Existing Products</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php if ($product['image']): ?><img src="<?php echo $product['image']; ?>" width="50"><?php endif; ?></td>
                    <td>
                        <a href="add_product.php?edit=<?php echo $product['id']; ?>">Edit</a> |
                        <a href="add_product.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
