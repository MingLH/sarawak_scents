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

<h1>✨ Features</h1>

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
  <li><strong>Transaction Receipts:</strong> View on-screen payment receipts and receive <strong>email notifications</strong> after purchase.</li>
  <li><strong>Order History:</strong> View detailed logs of past orders.</li>
</ul>

<h3>🛡️ Administrator (Admin)</h3>
<ul>
  <li><strong>Dashboard:</strong> View sales summaries with <strong>Daily, Weekly, and Monthly</strong> transaction reports.</li>
  <li><strong>Product Management (CRUD):</strong> Add, Edit, and Delete products.</li>
  <li><strong>User Management:</strong> View a read-only list of all registered members.</li>
  <li><strong>Secure File Upload:</strong> Upload product images securely (Admin only).</li>
</ul>

<hr>

<h1>🛠️ Tech Stack</h1>
<ul>
  <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript (Vanilla).</li>
  <li><strong>Visualization:</strong> Chart.js (for Admin Dashboard).</li>
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
├── /admin                   # (Member 6's Workspace)
│   ├── .gitkeep
│   ├── login.php            # Separate login for Admins
│   ├── dashboard.php        # The main admin control panel + Transaction Reports
│   ├── add_product.php      # Form to upload products & images
│   ├── manage_orders.php    # Table to view/update customer orders
│   └── members_list.php     # Requirement: Read-only view of members
│
├── /assets                  # (Member 3's Workspace)
│   ├── .gitkeep
│   ├── /images              # Product photos go here
│   └── /logo                # Branding files
│
├── /css                     # (Member 3's Workspace)
│   ├── .gitkeep
│   └── style.css            # The Master Stylesheet (Colors, Fonts)
│
├── /includes                # (Member 1 & 2's Workspace)
│   ├── .gitkeep
│   ├── db_connect.php       # Database connection (Member 2)
│   ├── header.php           # Navigation Bar (Member 1)
│   └── footer.php           # Copyright & Links (Member 1)
│
├── /js                      # (Shared Workspace)
│   └── .gitkeep             # (Empty for now, unless you need custom scripts)
│
├── /uploads                 # (Storage for Product Images)
│   └── .gitignore           # (The special file that ignores images)
│
├── database.sql             # (Member 2 - The Blueprint)
├── README.md                # (Project Documentation)
│
├── index.php                # (Member 1 - Homepage)
│
├── register.php             # (Member 4 - Sign Up)
├── login.php                # (Member 4 - Sign In)
├── logout.php               # (Member 4 - End Session)
│
├── shop.php                 # (Member 5 - Product Catalog)
├── product_details.php      # (Member 5 - Single Product View)
├── cart.php                 # (Member 5 - Shopping Cart)
├── receipt.php 	           # (Member 5 - Show Payment Receipt) 
│
├── profile.php              # (Member 7 - View User Info)
├── edit_profile.php         # (Member 7 - Update Address/Phone)
├── change_password.php      # (Member 7 - Security Update)
└── order_history.php        # (Member 7 - Past Purchases)</code></pre>

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
