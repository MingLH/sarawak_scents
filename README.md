<h1>🌿 Sarawak Scents</h1>

<blockquote>
  <strong>A Premium B2C E-Commerce Web Application for Botanical Fragrances.</strong><br>
  <em>University Project for TMF3973 Web Application Development (Group 6)</em>
</blockquote>

<h2>📖 About The Project</h2>
<p>
  <strong>Sarawak Scents</strong> is a "digital-first" artisan brand that sells curated, high-quality botanical home and personal fragrance products inspired by the unique biodiversity of Borneo.
</p>
<p>
  This web application is a fully functional B2C e-commerce platform built from scratch using <strong>PHP and MySQL</strong>. It features a public storefront, a secure member area for customers to manage orders, and a comprehensive admin dashboard for inventory management.
</p>

<hr>

<h2>✨ Features</h2>

<h3>👤 Public User (Guest)</h3>
<ul>
  <li><strong>Browse & Search:</strong> View products by category (Perfume, Soap, Candle) or search by name.</li>
  <li><strong>Shopping Cart:</strong> Add items to cart (session-based) and view total cost.</li>
  <li><strong>Registration:</strong> Create a new account to proceed to checkout.</li>
</ul>

<h3>🛍️ Registered Member</h3>
<ul>
  <li><strong>Secure Login:</strong> Authentication with hashed passwords.</li>
  <li><strong>Profile Management:</strong> Update shipping address and personal details.</li>
  <li><strong>Checkout:</strong> Complete a "dummy" transaction (Bank Transfer simulation).</li>
  <li><strong>Order History:</strong> View past orders and payment receipts.</li>
</ul>

<h3>🛡️ Administrator (Admin)</h3>
<ul>
  <li><strong>Dashboard:</strong> View sales summaries and daily/weekly transaction reports.</li>
  <li><strong>Product Management (CRUD):</strong> Add, Edit, and Delete products.</li>
  <li><strong>Secure File Upload:</strong> Upload product images securely (Admin only).</li>
  <li><strong>Order Management:</strong> Update order status (e.g., from "Pending" to "Shipped").</li>
</ul>

<hr>

<h2>🛠️ Tech Stack</h2>
<ul>
  <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript (Vanilla).</li>
  <li><strong>Backend:</strong> PHP (Native).</li>
  <li><strong>Database:</strong> MySQL (via phpMyAdmin).</li>
  <li><strong>Server Environment:</strong> XAMPP (Apache).</li>
</ul>

<hr>

<h2>🚀 Installation & Setup Guide</h2>
<p>To run this project locally, follow these steps exactly:</p>

<h3>1. Clone the Repository</h3>
<p>Open your terminal (Git Bash) and navigate to your XAMPP <code>htdocs</code> folder:</p>
<pre><code>cd C:/xampp/htdocs
git clone https://github.com/&lt;YOUR-USERNAME&gt;/sarawak-scents.git</code></pre>

<h3>2. Setup the Database</h3>
<ol>
  <li>Open <strong>XAMPP Control Panel</strong> and start <strong>Apache</strong> and <strong>MySQL</strong>.</li>
  <li>Go to <code>http://localhost/phpmyadmin</code>.</li>
  <li>Create a new database named <strong><code>sarawak_scents_db</code></strong>.</li>
  <li>Click <strong>Import</strong> and select the <code>database.sql</code> file located in the root of this project folder.</li>
</ol>

<h3>3. Configure Connection</h3>
<p>Ensure the <code>includes/db_connect.php</code> file matches your local XAMPP settings (default password is usually empty):</p>
<pre><code>$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "sarawak_scents_db";</code></pre>

<h3>4. Run the Project</h3>
<p>Open your browser and visit:<br>
<a href="http://localhost/sarawak-scents/">http://localhost/sarawak-scents/</a></p>

<hr>

<h2>📂 Folder Structure</h2>
<pre><code>/sarawak-scents
│
├── /admin           # Admin dashboard and logic
├── /assets          # Images, logos, and icons
├── /css             # Global stylesheets (style.css)
├── /includes        # Reusable components (header, footer, db_connect)
├── /js              # JavaScript files for interactivity
├── /uploads         # Product images uploaded by Admin
│
├── database.sql     # Database import file
├── index.php        # Homepage
├── shop.php         # Product catalog
├── cart.php         # Shopping cart logic
├── login.php        # User login page
├── register.php     # User registration page
└── README.md        # Project documentation</code></pre>

<hr>

<h2>👥 The Team (Group 6)</h2>
<table border="1">
  <thead>
    <tr>
      <th>Role</th>
      <th>Name</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>Project Manager / Team Lead</strong></td>
      <td>Lee Hao Ming</td>
    </tr>
    <tr>
      <td><strong>Content Writer</strong></td>
      <td>Mohamad Shafiq Bin Muhamad Zakaria</td>
    </tr>
    <tr>
      <td><strong>Business Analyst</strong></td>
      <td>Isaac Shagal Anak Tinggal</td>
    </tr>
    <tr>
      <td><strong>Team Coordinator</strong></td>
      <td>Javin Sim Chuin Cai</td>
    </tr>
    <tr>
      <td><strong>Financial Planner</strong></td>
      <td>Mohamad Shahfizul Bin Mohd Suhaimi</td>
    </tr>
    <tr>
      <td><strong>Market Researcher</strong></td>
      <td>Neasthy Laade</td>
    </tr>
    <tr>
      <td><strong>Marketing Strategist</strong></td>
      <td>Asmaul Afif Bin Morny</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>📜 License & Acknowledgments</h2>
<ul>
  <li>Developed for <strong>TMF3973 Web Application Development</strong> at UNIMAS (Faculty of Computer Science & Information Technology).</li>
  <li><em>Disclaimer: This is a fictional business created for educational purposes.</em></li>
</ul>
