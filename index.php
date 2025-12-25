<?php
include 'includes/db_connect.php';
include 'includes/header.php'; 

// Get User Role & Name
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Visitor';

// Fetch Featured Products (Active Only)
$fav_sql = "SELECT * FROM products WHERE is_active = 1 ORDER BY created_at ASC LIMIT 3";
$fav_result = mysqli_query($conn, $fav_sql);
?>

<div class="main-content">

    <?php if ($role === 'admin'): ?>
        <div class="admin-container">
            <section class="admin-alert">
                <h2>👤 Admin Access Detected</h2>
                <p>You are currently logged in as <strong><?php echo htmlspecialchars($fullName); ?></strong>.</p>
                <p>This is the public front-end. To manage products and orders, please use the dashboard.</p>
                
                <a href="admin/dashboard.php" class="btn-admin">
                    Go to Admin Dashboard &rarr;
                </a>
            </section>
        </div>

    <?php else: ?>
        
        <section class="hero-section">
            <?php if ($role === 'member'): ?>
                <p class="hero-welcome">
                    WELCOME BACK, <?php echo htmlspecialchars($fullName); ?>
                </p>
            <?php endif; ?>

            <h1 class="hero-title">Discover the Essence of Sarawak</h1>
            <p class="hero-desc">
                Premium handcrafted botanical fragrances, soaps, and candles made from the heart of Borneo.
            </p>
            
            <div class="hero-buttons">
                <a href="shop.php" class="btn-hero-primary">Browse Catalog</a>
                <?php if ($role !== 'member'): ?>
                    <a href="signup.php" class="btn-hero-secondary">Join Member</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="section-favorites">
            <h2 class="section-title">Our Favorites</h2>
            <p class="section-subtitle">Handpicked essentials from our Genesis Collection.</p>

            <div class="product-grid">
                <?php if (mysqli_num_rows($fav_result) > 0): ?>
                    <?php while ($prod = mysqli_fetch_assoc($fav_result)): ?>
                        <div class="product-card">
                            <a href="product_details.php?id=<?php echo $prod['product_id']; ?>">
                                <img src="assets/images/<?php echo htmlspecialchars($prod['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                     class="product-img">
                            </a>
                            
                            <h3 class="product-title"><?php echo htmlspecialchars($prod['name']); ?></h3>
                            
                            <p class="product-desc">
                                <?php echo htmlspecialchars(substr($prod['description'], 0, 80)) . '...'; ?>
                            </p>
                            
                            <a href="product_details.php?id=<?php echo $prod['product_id']; ?>" class="btn-product">
                                View Details
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No featured products available.</p>
                <?php endif; ?>
            </div>
            
            <a href="shop.php" class="view-all-link">See all products &rarr;</a>
        </section>

        <section id="story" class="section-story">
            <div class="story-container">
                <div class="story-content">
                    <h2 class="story-title">Our Story</h2>
                    <p class="story-text">
                        Sarawak Scents was born from a deep appreciation for the unique biodiversity of Borneo. We saw a gap in the market for authentic, story-driven botanical fragrances that capture the essence of our rainforest home.
                    </p>
                    <p class="story-text">
                        Our mission is to offer a trusted alternative to generic souvenir shops. We curate premium, locally-inspired products that serve as meaningful keepsakes and authentic gifts.
                    </p>
                    <p class="story-quote">
                        "Every product in our Genesis Collection is designed to evoke the sensory memory of Sarawak's natural beauty."
                    </p>
                </div>

                <div class="story-image-col">
                    <img src="assets/images/story.png" alt="Borneo Rainforest Texture" class="story-img">
                </div>
            </div>
        </section>

        <section class="section-features">
            <div class="feature-item">
                <div class="feature-icon">🌱</div>
                <h3 class="feature-title">100% Natural</h3>
                <p class="feature-text">Sourced directly from local Borneo farmers to ensure purity.</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">🚚</div>
                <h3 class="feature-title">Fast Delivery</h3>
                <p class="feature-text">Shipping throughout Malaysia within 3-5 business days.</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">Secure Payment</h3>
                <p class="feature-text">Transactions are safe, verified, and encrypted.</p>
            </div>
        </section>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>