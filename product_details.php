<?php
// Start Session & Connect
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/db_connect.php';

// 1. Get Product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Fetch Product Data
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// 3. Handle "Product Not Found"
if (!$product) {
    include 'includes/header.php';
    echo "<div style='text-align:center; padding:100px 20px;'>
            <h2 style='color:#333;'>Product not found</h2>
            <p style='color:#666; margin-bottom:20px;'>The product you are looking for does not exist or has been removed.</p>
            <a href='shop.php' class='btn-hero-secondary'>Back to Shop</a>
          </div>";
    include 'includes/footer.php';
    exit();
}

include 'includes/header.php';
?>

<div class="detail-container">
    
    <div class="breadcrumb">
        <a href="shop.php">Shop</a> &nbsp; / &nbsp; 
        <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div class="product-layout">
        
        <div class="product-gallery">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="detail-img">
        </div>

        <div class="product-info">
            
            <span class="detail-category">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </span>

            <h1 class="detail-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="detail-price">
                RM <?php echo number_format($product['price'], 2); ?>
            </div>
            
            <div class="detail-desc">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>

            <form action="cart.php" method="POST" class="cart-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                
                <label class="qty-label">Quantity</label>
                
                <div class="cart-actions">
                    <input type="number" name="quantity" value="1" min="1" max="10" class="qty-input">
                    
                    <button type="submit" class="btn-add-cart">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>