# ShopWithAlfred 🛒

**Shop Smart. Shop With Alfred.**

A complete PHP/MySQL e-commerce platform with WhatsApp ordering, multi-theme system, and admin dashboard — built for InfinityFree hosting.

---

## ✨ Features

- **Customer-facing storefront** — Browse products, filter by category/gender/price, view product details
- **WhatsApp ordering** — Orders are sent directly to WhatsApp (no payment gateway needed)
- **4 color themes + dark mode** — Navy & Gold, Soft Blue, Soft Purple, Warm Orange
- **Customer accounts** — Optional registration, order tracking
- **Newsletter subscriptions** — Email signups with CSV export
- **Restock notifications** — Customers can sign up to be notified when OOS products are restocked
- **Full admin dashboard** — Products, categories, orders, customers, subscribers, settings
- **Secret admin access** — 4 rapid taps on the logo navigates to admin login
- **Jumia product extraction** — Auto-fill product details from Jumia Kenya URLs
- **Responsive design** — Mobile-first, works on all screen sizes
- **InfinityFree compatible** — No Composer, no Node.js, PHP 7.4+ and MySQL only

---

## 🏗 Project Structure

```
ShopWith/
├── admin/              # Admin dashboard pages
│   ├── login.php       # Admin login
│   ├── index.php       # Dashboard with stats
│   ├── products.php    # Product management (CRUD)
│   ├── categories.php  # Category management
│   ├── orders.php      # Order management
│   ├── customers.php   # View registered customers
│   ├── subscribers.php # Newsletter & restock alerts
│   ├── settings.php    # Store, social, WhatsApp, features
│   └── logout.php
├── api/                # JSON API endpoints
│   ├── products.php    # Products CRUD
│   ├── categories.php  # Categories CRUD
│   ├── orders.php      # Order creation & management
│   ├── subscribers.php # Newsletter & restock signups
│   ├── settings.php    # Settings management
│   ├── auth.php        # Admin password change
│   ├── extract-jumia.php # Jumia product scraper
│   ├── send-email.php  # Email sending (PHPMailer/mail())
│   └── customers.php   # Customer list
├── assets/
│   ├── css/
│   │   ├── themes.css  # Theme CSS variables
│   │   ├── style.css   # Main frontend styles
│   │   └── admin.css   # Admin dashboard styles
│   └── js/
│       ├── theme-switcher.js   # Theme selection & dark mode
│       ├── form-validation.js  # Client-side form validation
│       ├── main.js             # Frontend JavaScript
│       └── admin.js            # Admin dashboard JavaScript
├── includes/
│   ├── config.php      # Database, site constants, email config
│   ├── functions.php   # Helper functions
│   ├── auth.php        # Authentication & session management
│   ├── header.php      # Shared HTML head & navbar
│   ├── footer.php      # Shared footer & scripts
│   ├── admin-header.php    # Admin HTML head
│   ├── admin-sidebar.php   # Admin sidebar & topbar
│   └── admin-footer.php    # Admin footer & scripts
├── vendor/phpmailer/   # PHPMailer (optional, see README inside)
├── index.php           # Homepage
├── shop.php            # All products with filters
├── product.php         # Product detail & order modal
├── contact.php         # Contact page
├── login.php           # Customer login/register
├── account.php         # Customer account/orders
├── logout.php          # Customer logout
├── database.sql        # MySQL schema
├── .htaccess           # Security & caching rules
└── README.md           # This file
```

---

## 🚀 Deployment (InfinityFree)

### 1. Create Database
1. Log in to InfinityFree control panel
2. Go to **MySQL Databases** → Create a new database
3. Note your database name, username, and password

### 2. Import Schema
1. Go to **phpMyAdmin** in control panel
2. Select your new database
3. Click **Import** → Choose `database.sql` → Click **Go**

### 3. Update Config
Edit `includes/config.php` with your database credentials:
```php
define('DB_HOST', 'sqlXXX.epizy.com');  // Your InfinityFree host
define('DB_NAME', 'epiz_XXXXXXXX_shopwithalfred');
define('DB_USER', 'epiz_XXXXXXXX');
define('DB_PASS', 'your_password_here');
define('BASE_URL', 'https://yourdomain.epizy.com');
```

### 4. Upload Files
1. Connect via **FileZilla** or use the **Online File Manager**
2. Upload ALL project files to the `htdocs/` folder
3. Make sure `.htaccess` is uploaded (it's a hidden file)

### 5. First Login
- **Admin URL:** `yourdomain.com/admin/login.php`
- **Username:** `Guruadmin`
- **Password:** `admin@guru123`

> ⚠️ **Change the admin password immediately** in Settings → Admin Account!

---

## 🎨 Themes

Customers can switch between 4 themes via the palette icon in the navbar:

| Theme | Primary | Accent |
|-------|---------|--------|
| Navy & Gold | `#1E3A5F` | `#D4A853` |
| Soft Blue | `#4A90D9` | `#6BB5FF` |
| Soft Purple | `#7C3AED` | `#A855F7` |
| Warm Orange | `#EA580C` | `#FB923C` |

All themes include a dark mode toggle.

---

## 📱 WhatsApp Integration

Orders are formatted as structured WhatsApp messages including:
- Customer details (name, phone, email, gender)
- Delivery info (county, address, preferred date)
- Order details (product, quantity, price, subtotal)
- Order reference number

The admin WhatsApp number is configured in `includes/config.php`.

---

## 🔒 Security

- All inputs sanitized with `htmlspecialchars()`
- CSRF token protection on forms
- Passwords hashed with `password_hash()` (bcrypt)
- `.htaccess` disables directory listing and hides sensitive files
- Admin authentication required for all admin pages and APIs
- PDO prepared statements prevent SQL injection

---

## 📧 Email (Optional)

To enable email notifications:
1. Download PHPMailer from [GitHub](https://github.com/PHPMailer/PHPMailer)
2. Copy `PHPMailer.php`, `SMTP.php`, `Exception.php` to `vendor/phpmailer/`
3. Configure SMTP in `includes/config.php`

Without PHPMailer, the system falls back to PHP's `mail()`.

---

## 🛠 Tech Stack

| Component | Technology |
|-----------|-----------|
| Frontend | HTML5, CSS3, Vanilla JS (ES6+) |
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ |
| Icons | Font Awesome 6 |
| Fonts | Google Fonts (Poppins) |
| Email | PHPMailer (optional) |
| Hosting | InfinityFree |

---

**Built with ❤️ for ShopWithAlfred**
