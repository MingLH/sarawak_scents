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
    if ($_GET['msg'] == 'deleted') $success_msg = "Product permanently deleted.";
    if ($_GET['msg'] == 'archived') $success_msg = "Product moved to Archive (Hidden).";
    if ($_GET['msg'] == 'restored') $success_msg = "Product restored successfully.";
}

// 3. HANDLE SMART DELETE
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // STEP 1: Try to Hard Delete
    $query = "DELETE FROM products WHERE product_id = $deleteId";
    
    try {
        if (mysqli_query($conn, $query)) {
            header("Location: products.php?msg=deleted");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // STEP 2: Fallback to Archive if FK constraint fails
        if ($e->getCode() == 1451) {
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

// 7. FETCH PRODUCTS
$sql = "SELECT p.*, c.category_name, 
        (SELECT COUNT(*) FROM order_items WHERE product_id = p.product_id) as sales_count 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.is_active DESC, p.created_at DESC";
$productsResult = mysqli_query($conn, $sql);
$categoriesResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
?>

<?php include 'includes/header.php'; ?>

    <div class="admin-header">
        <h1 style="color: #333; margin: 0;">Product Management</h1>
        <a href="#productForm" class="btn-action" style="background:#064e3b;">
            <i class="fas fa-plus"></i> Add New
        </a>
    </div>

    <?php if ($success_msg): ?> <div class="alert alert-success"><?php echo $success_msg; ?></div> <?php endif; ?>
    <?php if ($error_msg): ?> <div class="alert alert-error"><?php echo $error_msg; ?></div> <?php endif; ?>

    <div class="card">
        <h3 style="color:#555; margin-top:0; margin-bottom:15px;">Current Inventory</h3>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Sales</th>
                        <th>Price</th>
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
                                <img src="../assets/images/<?php echo htmlspecialchars($img); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                                <small style="color:#888;"><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></small>
                            </td>

                            <td style="text-align:center;">
                                <?php echo $p['sales_count']; ?> sold
                            </td>

                            <td style="font-weight:bold; color:#064e3b;">RM <?php echo number_format($p['price'], 2); ?></td>

                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <a href="?edit=<?php echo $p['product_id']; ?>#productForm" class="action-btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <?php if ($p['is_active']): ?>
                                        <?php if ($p['sales_count'] > 0): ?>
                                            <a href="?delete=<?php echo $p['product_id']; ?>" class="action-btn btn-archive" 
                                               onclick="return confirm('This product has sales history.\nIt will be ARCHIVED (Hidden).\n\nProceed?');">
                                               <i class="fas fa-archive"></i> Archive
                                            </a>
                                        <?php else: ?>
                                            <a href="?delete=<?php echo $p['product_id']; ?>" class="action-btn btn-delete" 
                                               onclick="return confirm('Permanently DELETE this product?\n(It has never been sold).');">
                                               <i class="fas fa-trash"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="?restore=<?php echo $p['product_id']; ?>" class="action-btn btn-restore" 
                                           onclick="return confirm('Restore this product?');">
                                           <i class="fas fa-undo"></i> Restore
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:20px;">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" id="productForm" style="border-top: 4px solid #064e3b;">
        <h3 style="margin-top:0; color:#064e3b; margin-bottom:20px;">
            <?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?>
        </h3>
        
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <?php if ($editProduct): ?>
                <input type="hidden" name="edit_id" value="<?php echo $editProduct['product_id']; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" 
                               required placeholder="e.g. Lavender Oil">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (RM)</label>
                        <input type="number" step="0.01" min="0.01" name="price" class="form-control" 
                               value="<?php echo $editProduct['price'] ?? ''; ?>" 
                               required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control" required>
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
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="5" class="form-control" 
                                  required placeholder="Enter details..."><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product Image</label>
                        <?php if ($editProduct && $editProduct['image']): ?>
                            <div class="current-img-preview">
                                <img src="../assets/images/<?php echo htmlspecialchars($editProduct['image']); ?>" style="height:50px;"> 
                                <small style="color:#666;">Current Image</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*" class="form-control" style="padding: 9px;" 
                               <?php echo $editProduct ? '' : 'required'; ?>>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; display:flex; gap:10px;">
                <button type="submit" class="btn-action" style="background: #064e3b; padding:12px 25px; font-size:1rem;">
                    <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                </button>
                
                <?php if ($editProduct): ?>
                    <a href="products.php" class="btn-action" style="background: #64748b; padding:12px 25px; font-size:1rem;">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

<?php include 'includes/footer.php'; ?>