<?php
session_start();
require_once 'includes/db_connect.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where = ["1=1"];
$params = [];
$types = "";

if ($search !== '') {
    $where[] = "(p.name LIKE ? OR p.description LIKE ? OR c.category_name LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

$whereClause = implode(" AND ", $where);

// Determine sort order
$orderBy = match($sort) {
    'price_asc' => "p.price ASC",
    'price_desc' => "p.price DESC",
    'name_asc' => "p.name ASC",
    'name_desc' => "p.name DESC",
    'newest' => "p.created_at DESC",
    default => $search !== '' ? "CASE 
        WHEN p.name LIKE ? THEN 1
        WHEN c.category_name LIKE ? THEN 2
        ELSE 3 END, p.name ASC" : "p.product_id DESC"
};

// Add search params for relevance sorting
if ($sort === 'relevance' && $search !== '') {
    array_unshift($params, "%{$search}%", "%{$search}%");
    $types = "ss" . $types;
}

// Get total count
$countSql = "SELECT COUNT(*) as total FROM products p 
             JOIN categories c ON p.category_id = c.category_id 
             WHERE {$whereClause}";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalResults = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalResults / $limit);

// Get products
$sql = "SELECT p.product_id, p.name, p.price, p.image, c.category_name,
        CASE WHEN p.product_id IS NOT NULL THEN 'In Stock' ELSE 'Out of Stock' END as stock_status
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE {$whereClause}
        ORDER BY {$orderBy}
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$params[] = $limit;
$params[] = $offset;
$types .= "ii";
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

// Get categories for filter
$categoriesResult = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Sarawak Scents</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search-form input[type="text"] {
            flex: 1;
            min-width: 250px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-form button {
            padding: 10px 20px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background: #34495e;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .results-info {
            color: #666;
            font-size: 14px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .product-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f5f5f5;
        }
        .product-info {
            padding: 15px;
        }
        .product-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        .product-price {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .product-category {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .stock-status {
            font-size: 13px;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .in-stock {
            background: #d4edda;
            color: #155724;
        }
        .out-stock {
            background: #f8d7da;
            color: #721c24;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #2c3e50;
        }
        .pagination .active {
            background: #2c3e50;
            color: white;
        }
        .pagination a:hover {
            background: #ecf0f1;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <h1>Sarawak Scents</h1>
                <div>
                    <a href="index.php">Home</a>
                    <a href="shop.php">Shop</a>
                    <a href="cart.php">Cart</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="search-bar">
            <form method="GET" action="shop.php" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search products..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <select name="category">
                    <option value="0">All Categories</option>
                    <?php while($cat = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_id']; ?>" 
                                <?php echo $category == $cat['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select name="sort">
                    <option value="relevance" <?php echo $sort === 'relevance' ? 'selected' : ''; ?>>Relevance</option>
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A-Z</option>
                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name: Z-A</option>
                </select>
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="filters">
            <div class="results-info">
                <?php echo $totalResults; ?> product<?php echo $totalResults !== 1 ? 's' : ''; ?> found
                <?php if($search !== ''): ?>
                    for "<?php echo htmlspecialchars($search); ?>"
                <?php endif; ?>
            </div>
        </div>

        <?php if($products->num_rows > 0): ?>
            <div class="product-grid">
                <?php while($product = $products->fetch_assoc()): ?>
                    <div class="product-card" onclick="window.location.href='product_detail.php?id=<?php echo $product['product_id']; ?>'">
                        <img 
                            src="<?php echo htmlspecialchars($product['image'] ?: 'images/placeholder.jpg'); ?>" 
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image"
                        >
                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="product-price">RM <?php echo number_format($product['price'], 2); ?></div>
                            <span class="stock-status in-stock"><?php echo $product['stock_status']; ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if($totalPages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                    <?php endif; ?>

                    <?php for($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <h2>No products found</h2>
                <p>Try adjusting your search or filters</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>