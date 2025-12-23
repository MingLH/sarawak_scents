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
    echo "<div style='text-align:center; padding:50px;'>
            <h2>Product not found</h2>
            <a href='shop.php'>Back to Shop</a>
          </div>";
    include 'includes/footer.php';
    exit();
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    
    <div style="margin-bottom: 20px; color: #666;">
        <a href="shop.php" style="text-decoration:none; color:#666;">Shop</a> > 
        <span style="color:#064e3b; font-weight:bold;"><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 style="width: 100%; border-radius: 10px; border: 1px solid #eee;">
        </div>

        <div style="flex: 1; min-width: 300px;">
            <h1 style="color: #333; margin-top: 0;"><?php echo htmlspecialchars($product['name']); ?></h1>
            <h2 style="color: #064e3b; margin-bottom: 20px;">RM <?php echo number_format($product['price'], 2); ?></h2>
            
            <p style="line-height: 1.6; color: #555; margin-bottom: 30px;">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <form action="cart.php" method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                
                <label style="font-weight:bold; display:block; margin-bottom:5px;">Quantity</label>
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <input type="number" name="quantity" value="1" min="1" max="10" 
                           style="padding: 10px; width: 60px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <button type="submit" style="background: #064e3b; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; width: 100%;">
                    Add to Cart
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>