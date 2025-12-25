<?php
$isInAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$basePath = $isInAdmin ? '../' : ''; 
?>

</div> <footer class="site-footer">
    <div class="footer-container">
        
        <div class="footer-col footer-brand">
            <a href="<?php echo $basePath; ?>index.php" class="footer-logo-link">
                <img src="<?php echo $basePath; ?>assets/images/Sarawak_Scents_Logo.png" alt="Logo">
                <span>Sarawak Scents</span>
            </a>
            <p>Premium artisan botanical fragrances inspired by the unique biodiversity of Borneo.</p>
        </div>

        <div class="footer-col footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $basePath; ?>shop.php">Products</a></li>
                <li><a href="<?php echo $basePath; ?>index.php#story">Our Story</a></li>
                <li><a href="<?php echo $basePath; ?>signup.php">Join Member</a></li>
            </ul>
        </div>

        <div class="footer-col footer-contact">
            <h4>Contact</h4>
            <ul>
                <li><span>📍</span> Kuching, Sarawak</li>
                <li><span>✉️</span> hello@sarawakscents.com</li>
                <li><span>📞</span> +60 12 345 6789</li>
            </ul>
        </div>

    </div>
    
    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> Sarawak Scents. All rights reserved.
    </div>
</footer>

<script src="<?php echo $basePath; ?>js/main.js"></script>

</body>
</html>