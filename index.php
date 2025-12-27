<?php
// 1. Include Header (Starts session)
include 'includes/header.php'; 

// 2. Get User Role & Name
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Visitor';
?>

<div class="container" style="margin-top: 0; min-height: 80vh;">

    <?php if ($role === 'admin'): ?>
        <div style="padding: 2rem; max-width: 800px; margin: 4rem auto;">
            <section class="admin-alert" style="background: #e2e6ea; padding: 2rem; border-left: 5px solid #007bff; border-radius: 5px;">
                <h2>👤 Admin Access Detected</h2>
                <p>You are currently logged in as <strong><?php echo htmlspecialchars($fullName); ?></strong>.</p>
                <p>This is the public front-end. To manage products and orders, please use the dashboard.</p>
                <br>
                <a href="admin/dashboard.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Go to Admin Dashboard &rarr;
                </a>
            </section>
        </div>

    <?php else: ?>
        
        <section class="hero-section" style="text-align: center; padding: 6rem 1rem; background: linear-gradient(to bottom, #f9f9f9, #ffffff);">
            <?php if ($role === 'member'): ?>
                <p class="fade-in-title" style="color: #27ae60; font-weight: bold; letter-spacing: 1px; margin-bottom: 0.5rem;">
                    WELCOME BACK, <?php echo strtoupper(htmlspecialchars($fullName)); ?>
                </p>
            <?php endif; ?>

            <h1 class="fade-in-title" style="font-size: 3rem; color: #2c3e50; margin-bottom: 1rem;">Discover the Essence of Sarawak</h1>
            <p class="fade-in-title" style="font-size: 1.2rem; color: #555; max-width: 600px; margin: 0 auto 2rem auto;">
                Premium handcrafted botanical fragrances, soaps, and candles made from the heart of Borneo.
            </p>
            
            <div class="hero-buttons">
                <a href="shop.php" class="btn-primary fade-in-title" style="background: #2c3e50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 30px; margin-right: 10px; font-weight: bold; transition: all 0.3s;">
                    Browse Catalog
                </a>
                <?php if ($role !== 'member'): ?>
                    <a href="signup.php" class="btn-secondary" style="border: 2px solid #2c3e50; color: #2c3e50; padding: 13px 28px; text-decoration: none; border-radius: 30px; font-weight: bold;">
                        Join Member
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <section class="favorites" style="background-color: #f9f9f9; padding: 4rem 2rem; text-align: center;">
            <h2 style="color: #2c3e50; margin-bottom: 1rem;">Our Favorites</h2>
            <p style="color: #666; margin-bottom: 3rem;">Handpicked essentials from our Genesis Collection.</p>

            <div style="max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: center; gap: 30px;">
                
                <div class="product-card" style="background: white; padding: 20px; border-radius: 10px; width: 300px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left;">
                    <img src="assets/images/rainforest_mist.png" alt="Rainforest Mist Perfume" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 15px;">
                    
                    <h3 style="font-size: 1.2rem; color: #333; margin-bottom: 5px;">Rainforest Mist Perfume</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px; line-height: 1.4;">
                        Captures the fresh scent of the jungle after a morning rain. Earthy and refreshing.
                    </p>
                    <a href="product_details.php?id=1" style="display: block; text-align: center; background: #2c3e50; color: white; padding: 10px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                        View Details
                    </a>
                </div>

                <div class="product-card" style="background: white; padding: 20px; border-radius: 10px; width: 300px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left;">
                    <img src="assets/images/pepper_soap.png" alt="Pepper Berry Soap" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 15px;">

                    <h3 style="font-size: 1.2rem; color: #333; margin-bottom: 5px;">Pepper Berry Soap</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px; line-height: 1.4;">
                         A hand-milled, luxury bar soap featuring real Sarawak black pepper for gentle exfoliation.
                    </p>
                    <a href="product_details.php?id=2" style="display: block; text-align: center; background: #2c3e50; color: white; padding: 10px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                        View Details
                    </a>
                </div>

                <div class="product-card" style="background: white; padding: 20px; border-radius: 10px; width: 300px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left;">
                    <img src="assets/images/orchid_candle.png" alt="Orchid Bloom Candle" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 15px;">

                    <h3 style="font-size: 1.2rem; color: #333; margin-bottom: 5px;">Orchid Bloom Candle</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px; line-height: 1.4;">
                        Hand-poured soy wax candle scented with a delicate floral blend of native orchids.
                    </p>
                    <a href="product_details.php?id=3" style="display: block; text-align: center; background: #2c3e50; color: white; padding: 10px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                        View Details
                    </a>
                </div>

            </div>
            
            <div style="margin-top: 3rem;">
                <a href="shop.php" style="color: #27ae60; text-decoration: none; font-weight: bold; font-size: 1.1rem;">
                    See all products &rarr;
                </a>
            </div>
        </section>

        <section id="story" class="our-story" style="background-color: #fff; padding: 4rem 2rem;">
            <div style="max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; gap: 40px;">
                
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="color: #27ae60; font-size: 1.8rem; margin-bottom: 1.5rem;">Our Story</h2>
                    <p style="font-size: 1.05rem; line-height: 1.8; color: #444; margin-bottom: 1.5rem;">
                        Sarawak Scents was born from a deep appreciation for the unique biodiversity of Borneo. We saw a gap in the market for authentic, story-driven botanical fragrances that capture the essence of our rainforest home.
                    </p>
                    <p style="font-size: 1.05rem; line-height: 1.8; color: #444; margin-bottom: 1.5rem;">
                        Our mission is to offer a trusted alternative to generic souvenir shops. We curate premium, locally-inspired products that serve as meaningful keepsakes and authentic gifts.
                    </p>
                    <p style="font-size: 1rem; color: #666; font-style: italic;">
                        "Every product in our Genesis Collection is designed to evoke the sensory memory of Sarawak's natural beauty."
                    </p>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <img src="assets/images/story.png" alt="Borneo Rainforest Texture" 
                         style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); object-fit: cover;">
                </div>

            </div>
        </section>

        <section class="features" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; padding: 4rem 2rem; background-color: #fff; border-top: 1px solid #eee;">
            
            <div style="text-align: center; max-width: 250px;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🌱</div>
                <h3 style="margin-bottom: 0.5rem; color: #2c3e50;">100% Natural</h3>
                <p style="color: #666; line-height: 1.5;">Sourced directly from local Borneo farmers to ensure purity.</p>
            </div>

            <div style="text-align: center; max-width: 250px;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🚚</div>
                <h3 class="fade-in-title" style="margin-bottom: 0.5rem; color: #2c3e50;">Fast Delivery</h3>
                <p style="color: #666; line-height: 1.5;">Shipping throughout Malaysia within 3-5 business days.</p>
            </div>

            <div style="text-align: center; max-width: 250px;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔒</div>
                <h3 style="margin-bottom: 0.5rem; color: #2c3e50;">Secure Payment</h3>
                <p style="color: #666; line-height: 1.5;">Transactions are safe, verified, and encrypted.</p>
            </div>
            
        </section>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>