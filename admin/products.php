<?php
session_start();
include '../includes/db_connect.php';

// 1. SECURITY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

// 2. CHECK MESSAGES
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'updated') $success_msg = "Product updated successfully!";
    if ($_GET['msg'] == 'added') $success_msg = "Product added successfully!";
    if ($_GET['msg'] == 'deleted') $success_msg = "Product permanently deleted (No sales history found).";
    if ($_GET['msg'] == 'archived') $success_msg = "Product moved to Archive (Hidden from shop to preserve receipts).";
    if ($_GET['msg'] == 'restored') $success_msg = "Product restored successfully.";
}

// 3. HANDLE SMART DELETE
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // STEP 1: Try to Hard Delete (Permanent)
    $query = "DELETE FROM products WHERE product_id = $deleteId";
    
    try {
        if (mysqli_query($conn, $query)) {
            // Success! It was deleted permanently.
            header("Location: products.php?msg=deleted");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // STEP 2: Catch Foreign Key Error (It has sales history)
        if ($e->getCode() == 1451) {
            // Fallback: Soft Delete (Archive)
            $archiveQuery = "UPDATE products SET is_active = 0 WHERE product_id = $deleteId";
            if (mysqli_query($conn, $archiveQuery)) {
                header("Location: products.php?msg=archived");
                exit();
            }
        } else {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}

// 4. HANDLE RESTORE
if (isset($_GET['restore'])) {
    $restoreId = (int)$_GET['restore'];
    $query = "UPDATE products SET is_active = 1 WHERE product_id = $restoreId";
    if (mysqli_query($conn, $query)) {
        header("Location: products.php?msg=restored");
        exit();
    }
}

// 5. HANDLE FORM SUBMISSION (ADD/EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    
    if (empty($name) || $price <= 0 || empty($description) || empty($category_id)) {
        $error_msg = "All fields are required, and Price must be greater than 0.";
    } 
    else {
        $image = "";
        $uploadDir = '../assets/images/'; 
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); 
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                $image = $fileName;
            }
        }

        // A. EDIT EXISTING
        if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
            $editId = (int)$_POST['edit_id'];
            if ($image) {
                $query = "UPDATE products SET name='$name', price=$price, description='$description', category_id=$category_id, image='$image' WHERE product_id=$editId";
            } else {
                $query = "UPDATE products SET name='$name', price=$price, description='$description', category_id=$category_id WHERE product_id=$editId";
            }
            if (mysqli_query($conn, $query)) {
                header("Location: products.php?msg=updated");
                exit();
            } else {
                $error_msg = "Error updating: " . mysqli_error($conn);
            }
        } 
        // B. ADD NEW
        else {
            if (empty($image)) {
                 $error_msg = "Product Image is required for new products.";
            } else {
                $query = "INSERT INTO products (name, price, description, category_id, image, is_active) VALUES ('$name', $price, '$description', $category_id, '$image', 1)";
                if (mysqli_query($conn, $query)) {
                    header("Location: products.php?msg=added");
                    exit();
                } else {
                    $error_msg = "Error adding: " . mysqli_error($conn);
                }
            }
        }
    }
}

// 6. FETCH DATA FOR EDITING
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $editId");
    $editProduct = mysqli_fetch_assoc($res);
}

// 7. FETCH PRODUCTS + SALES COUNT (To know if we can delete or must archive)
// We use a subquery to count how many times each product appears in 'order_items'
$sql = "SELECT p.*, c.category_name, 
        (SELECT COUNT(*) FROM order_items WHERE product_id = p.product_id) as sales_count 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.is_active DESC, p.created_at DESC";
$productsResult = mysqli_query($conn, $sql);
$categoriesResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
?>

<?php include 'includes/header.php'; ?>

<style>
    .row-inactive { background-color: #f3f4f6; color: #999; }
    .row-inactive img { opacity: 0.5; filter: grayscale(100%); }
    .badge-inactive { background: #9ca3af; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; }
    .badge-active { background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; }
    
    /* Description truncation style */
    .desc-cell { font-size: 0.85rem; color: #666; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>

    <h1 style="color: #333;">Product Management</h1>

    <?php if ($success_msg): ?> <div class="alert alert-success"><?php echo $success_msg; ?></div> <?php endif; ?>
    <?php if ($error_msg): ?> <div class="alert alert-error"><?php echo $error_msg; ?></div> <?php endif; ?>

    <h3 style="color:#555; margin-bottom:10px;">Current Inventory</h3>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Image</th>
                <th>Name</th>
                <th>Sales</th> <th>Description</th> <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($productsResult) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($productsResult)): ?>
                <tr class="<?php echo ($p['is_active'] == 0) ? 'row-inactive' : ''; ?>">
                    
                    <td>
                        <?php if ($p['is_active']): ?>
                            <span class="badge-active">Active</span>
                        <?php else: ?>
                            <span class="badge-inactive">Archived</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php $img = !empty($p['image']) ? $p['image'] : 'default.jpg'; ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($img); ?>" class="product-img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                    </td>

                    <td>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                        <small style="color:#888;"><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></small>
                    </td>

                    <td style="text-align:center;">
                        <?php echo $p['sales_count']; ?> sold
                    </td>

                    <td class="desc-cell" title="<?php echo htmlspecialchars($p['description']); ?>">
                        <?php echo htmlspecialchars($p['description']); ?>
                    </td>

                    <td style="font-weight:bold; color:#064e3b;">RM <?php echo number_format($p['price'], 2); ?></td>

                    <td>
                        <a href="?edit=<?php echo $p['product_id']; ?>#productForm" class="btn-edit" style="color: #2980b9; text-decoration: none; font-weight: bold; margin-right: 10px;">Edit</a>
                        
                        <?php if ($p['is_active']): ?>
                            <?php if ($p['sales_count'] > 0): ?>
                                <a href="?delete=<?php echo $p['product_id']; ?>" style="color: #d97706; text-decoration: none; font-weight: bold;" 
                                   onclick="return confirm('This product has sales history.\nIt will be ARCHIVED (Hidden), not deleted, to preserve receipts.\n\nProceed?');">
                                   Archive
                                </a>
                            <?php else: ?>
                                <a href="?delete=<?php echo $p['product_id']; ?>" style="color: #c0392b; text-decoration: none; font-weight: bold;" 
                                   onclick="return confirm('Permanently DELETE this product?\n(It has never been sold).');">
                                   Delete
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="?restore=<?php echo $p['product_id']; ?>" style="color: #059669; text-decoration: none; font-weight: bold;" 
                               onclick="return confirm('Restore this product to the shop?');">
                               Restore
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding:20px;">No products found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="card" id="productForm" style="border-top: 4px solid #064e3b; margin-top:30px;">
        <h3 style="margin-top:0; color:#064e3b;"><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h3>
        
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <?php if ($editProduct): ?>
                <input type="hidden" name="edit_id" value="<?php echo $editProduct['product_id']; ?>">
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">Product Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" required placeholder="e.g. Lavender Oil" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">Price (RM)</label>
                        <input type="number" step="0.01" min="0.01" name="price" value="<?php echo $editProduct['price'] ?? ''; ?>" required placeholder="0.00" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">Category</label>
                        <select name="category_id" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                            <option value="">-- Select Category --</option>
                            <?php 
                            mysqli_data_seek($categoriesResult, 0);
                            while ($cat = mysqli_fetch_assoc($categoriesResult)): 
                            ?>
                                <option value="<?php echo $cat['category_id']; ?>" 
                                    <?php echo (isset($editProduct['category_id']) && $editProduct['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">Description</label>
                        <textarea name="description" rows="4" required placeholder="Enter product details..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">Product Image</label>
                        <?php if ($editProduct && $editProduct['image']): ?>
                            <div style="margin-bottom:5px;">
                                <img src="../assets/images/<?php echo htmlspecialchars($editProduct['image']); ?>" style="height:50px; vertical-align:middle;"> 
                                <small style="color:#666;">Current</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*" <?php echo $editProduct ? '' : 'required'; ?>>
                    </div>
                </div>
            </div>

            <div style="margin-top: 15px;">
                <button type="submit" class="btn-action" style="background: #064e3b; padding:10px 20px; font-size:1rem;"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                <?php if ($editProduct): ?>
                    <a href="products.php" class="btn-action" style="background: #7f8c8d; margin-left:10px; padding:10px 20px; font-size:1rem;">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

<?php include 'includes/footer.php'; ?>