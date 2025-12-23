<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

// --- LOGIC ---
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base Query
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";

// Category Filter
if ($category_id > 0) {
    $sql .= " AND p.category_id = $category_id";
}

// Search Filter (Fixed: Now searches Category Name too!)
if (!empty($search_query)) {
    $safe_search = mysqli_real_escape_string($conn, $search_query);
    $sql .= " AND (p.name LIKE '%$safe_search%' 
              OR p.description LIKE '%$safe_search%' 
              OR c.category_name LIKE '%$safe_search%')";
}

$sql .= " ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container" style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
    
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <form action="shop.php" method="GET" style="display: flex; gap: 20px; flex-wrap: wrap; align-items: end;">
            
            <div style="flex: 2; min-width: 300px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Search Products</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" 
                           placeholder="Type 'Perfume', 'Soap'..." 
                           style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <button type="submit" style="background: #2c3e50; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                        Search
                    </button>
                </div>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Filter by Category</label>
                <div style="display: flex; gap: 10px;">
                    <select name="category" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="0">All Categories</option>
                        <?php 
                        mysqli_data_seek($cat_result, 0);
                        while($cat = mysqli_fetch_assoc($cat_result)): 
                        ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php if($category_id == $cat['category_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" style="background: #064e3b; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                        Apply
                    </button>
                </div>
            </div>

            <?php if($search_query || $category_id > 0): ?>
                <div style="padding-bottom: 2px;">
                    <a href="shop.php" style="color: #c0392b; text-decoration: none; font-weight: bold;">Reset Filters ✕</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <h2 style="margin-bottom: 20px;">Our Collection</h2>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                <div class="product-card" style="background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             style="width: 100%; height: 250px; object-fit: cover;">
                    </a>
                    <div style="padding: 15px;">
                        <div style="color: #999; font-size: 0.8rem; text-transform: uppercase;">
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </div>
                        <h3 style="margin: 5px 0; font-size: 1.1rem;">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        <div style="color: #064e3b; font-weight: bold; margin-top: 10px;">
                            RM <?php echo number_format($product['price'], 2); ?>
                        </div>
                        <a href="product_details.php?id=<?php echo $product['product_id']; ?>" 
                           style="display: block; text-align: center; margin-top: 10px; background: #f8f9fa; padding: 8px; text-decoration: none; color: #333; border-radius: 4px;">
                           View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; padding: 50px; color: #666;">No products found.</p>
    <?php endif; ?>

</div>
<?php include 'includes/footer.php'; ?>