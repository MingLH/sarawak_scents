<?php
session_start();

require_once 'includes/db_connect.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$error = '';
$success = '';
$product = null;

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch current stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $stock = $stmt->fetchColumn();

    if ($stock === false) {
        $error = "Product not found.";
    } elseif ($quantity <= 0) {
        $error = "Quantity must be at least 1.";
    } elseif ($quantity > $stock) {
        $error = "Cannot add $quantity units. Only $stock available in stock.";
    } else {
        // Check if adding to existing cart item would exceed stock
        $current_in_cart = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
        $new_total = $current_in_cart + $quantity;

        if ($new_total > $stock) {
            $available = $stock - $current_in_cart;
            $error = "Cannot add $quantity units. You already have $current_in_cart in cart. Only $available more available.";
        } else {
            // Add to cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $success = "Product added to cart successfully.";
        }
    }
}

// Get product details
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT id, name, price, stock, description, image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $error = "Product not found. The requested product does not exist.";
    }
} else {
    $error = "No product specified. Please provide a valid product identifier.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product Details'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .nav {
            background: #fff;
            padding: 15px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav a {
            color: #007bff;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .product-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .product-image {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-info h1 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .price {
            font-size: 24px;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .stock {
            margin-bottom: 15px;
            color: #666;
        }

        .stock.in-stock {
            color: #28a745;
        }

        .stock.out-of-stock {
            color: #dc3545;
        }

        .description {
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .quantity-selector {
            margin-bottom: 20px;
        }

        .quantity-selector label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .quantity-selector input {
            width: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .quantity-selector small {
            display: block;
            margin-top: 5px;
            color: #666;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .product-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="nav">
        <div class="container">
            <a href="shop.php">← Back to Products</a>
            <a href="cart.php">View Cart (<?php echo array_sum($_SESSION['cart']); ?>)</a>
        </div>
    </div>

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
                <a href="cart.php">Go to cart</a>
            </div>
        <?php endif; ?>

        <?php if ($product): ?>
            <div class="product-container">
                <div class="product-image-container">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image">
                    <?php else: ?>
                        <img src="placeholder.jpg" alt="No image available" class="product-image">
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                    <div class="price">$<?php echo number_format($product['price'], 2); ?></div>

                    <div class="stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                        <?php if ($product['stock'] > 0): ?>
                            <?php echo intval($product['stock']); ?> units in stock
                        <?php else: ?>
                            Out of stock
                        <?php endif; ?>
                    </div>

                    <div class="description">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                        <form method="POST" action="" id="addToCartForm">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                            <div class="quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <input type="number"
                                    id="quantity"
                                    name="quantity"
                                    min="1"
                                    max="<?php echo intval($product['stock']); ?>"
                                    value="1"
                                    required>
                                <small>Maximum: <?php echo intval($product['stock']); ?> units</small>
                            </div>

                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-primary" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>