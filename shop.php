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
        WHERE p.is_active = 1";

if ($category_id > 0) {
    $sql .= " AND p.category_id = $category_id";
}

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

<div class="shop-container">
    
    <h1 class="shop-title">Our Collection</h1>

    <div class="shop-controls">
        <form action="shop.php" method="GET" class="filter-form">
            
            <div class="search-group">
                <label class="filter-label">Search</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-input" 
                           value="<?php echo htmlspecialchars($search_query); ?>" 
                           placeholder="Type to search...">
                    <button type="submit" class="btn-filter">Search</button>
                </div>
            </div>

            <div class="category-group">
                <label class="filter-label">Category</label>
                <div class="input-group">
                    <select name="category" class="form-select">
                        <option value="0">All Categories</option>
                        <?php 
                        mysqli_data_seek($cat_result, 0);
                        while($cat = mysqli_fetch_assoc($cat_result)): 
                        ?>
                            <option value="<?php echo $cat['category_id']; ?>" 
                                <?php if($category_id == $cat['category_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn-filter btn-apply">Filter</button>
                </div>
            </div>

        </form>

        <?php if($search_query || $category_id > 0): ?>
            <div class="reset-container">
                <a href="shop.php" class="reset-link">
                    <i class="fas fa-times-circle"></i> Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="product-grid">
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                
                <div class="product-card">
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="product-img">
                    </a>
                    
                    <div style="padding: 15px; flex-grow: 1; display: flex; flex-direction: column;">
                        <span style="color: #999; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </span>
                        
                        <h3 style="margin: 0 0 10px 0; font-size: 1.1rem; color: #333;">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        
                        <div style="margin-top: auto; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="color: #064e3b; font-weight: bold; font-size: 1.1rem;">
                                RM <?php echo number_format($product['price'], 2); ?>
                            </div>
                            
                            <a href="product_details.php?id=<?php echo $product['product_id']; ?>" 
                               style="background: #064e3b; color: #ffffff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: 0.2s;">
                               View
                            </a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; color: #666;">
            <i class="fas fa-search" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
            <p style="font-size: 1.2rem;">No products found matching your criteria.</p>
            <a href="shop.php" style="margin-top: 20px; display: inline-block; text-decoration: underline; color: #064e3b;">View All Products</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>