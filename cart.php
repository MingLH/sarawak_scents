<?php
session_start();

require_once 'includes/db_connect.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$error = '';
$success = '';

// Handle remove from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $product_id = intval($_POST['product_id']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $success = "Item removed from cart.";
    }
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Fetch current stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $stock = $stmt->fetchColumn();
    
    if ($stock === false) {
        $error = "Product not found.";
    } elseif ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
        $success = "Item removed from cart.";
    } elseif ($quantity > $stock) {
        $error = "Cannot update to $quantity units. Only $stock available in stock.";
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
        $success = "Cart updated successfully.";
    }
}

// Fetch cart items
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT id, name, price, stock, image FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product_id = $product['id'];
        $quantity = $_SESSION['cart'][$product_id];
        
        // Verify quantity doesn't exceed stock
        if ($quantity > $product['stock']) {
            $_SESSION['cart'][$product_id] = $product['stock'];
            $quantity = $product['stock'];
        }
        
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'stock' => $product['stock'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        h1 {
            font-size: 28px;
            margin-bottom: 30px;
        }
        .cart-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-cart p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .item-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .item-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .item-price {
            color: #28a745;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .item-stock {
            font-size: 14px;
            color: #666;
        }
        .item-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
            gap: 10px;
        }
        .quantity-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-form input {
            width: 70px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
            color: #fff;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .item-subtotal {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .cart-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: right;
        }
        .cart-total {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        .checkout-btn {
            padding: 15px 40px;
            font-size: 18px;
        }
        @media (max-width: 768px) {
            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }
            .item-image {
                width: 80px;
                height: 80px;
            }
            .item-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="container">
            <a href="shop.php">← Continue Shopping</a>
        </div>
    </div>

    <div class="container">
        <h1>Shopping Cart</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="cart-container">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-image-container">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="item-image">
                            <?php else: ?>
                                <img src="placeholder.jpg" alt="No image" class="item-image">
                            <?php endif; ?>
                        </div>

                        <div class="item-details">
                            <div class="item-name">
                                <a href="product_details.php?id=<?php echo $item['id']; ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </div>
                            <div class="item-price">
                                $<?php echo number_format($item['price'], 2); ?> each
                            </div>
                            <div class="item-stock">
                                <?php echo intval($item['stock']); ?> available
                            </div>
                        </div>

                        <div class="item-actions">
                            <form method="POST" action="" class="quantity-form">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" 
                                       name="quantity" 
                                       min="1" 
                                       max="<?php echo intval($item['stock']); ?>" 
                                       value="<?php echo intval($item['quantity']); ?>">
                                <button type="submit" name="update_quantity" class="btn btn-primary btn-sm">Update</button>
                            </form>

                            <div class="item-subtotal">
                                $<?php echo number_format($item['subtotal'], 2); ?>
                            </div>

                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="cart-summary">
                    <div class="cart-total">
                        Total: $<?php echo number_format($total, 2); ?>
                    </div>
                    <a href="checkout.php" class="btn btn-primary checkout-btn">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>