# PHP Bookstore - MVC Application

A comprehensive online bookstore management application. Provides features from product management, order processing, VNPay payment integration to an intelligent review moderation system.

---

## Main Features

### User System

- User registration, login, password recovery
- Personal profile management
- User permissions and roles (Admin, Moderator, User)

### Product Management

- Product categories by type
- Advanced search and filtering
- Wishlist functionality

### Shopping Cart & Orders

- Add/remove/update cart items
- Complete order management
- Purchase history

### Review & Rating System

- 5-star product ratings
- Detailed review writing
- Automatic spam detection and moderation

### Content Moderation (AI-Based)

Automatic detection and classification of reviews:

- Spam score analysis (0-100)
- Detect banned words, URLs, suspicious emails
- Classification: Pending / Approved / Rejected
- Admin batch moderation dashboard

### Payment

- VNPay online payment integration
- Automatic payment confirmation
- Order status management

### News & Promotions

- News management
- Promotional campaigns
- Featured products

### Customer Support

- Live chat support
- Chat history storage
- New message notifications

---

## Directory Structure

```
MVC/
├── app/                              # Core Framework
│   ├── App.php                       # Application bootstrap
│   ├── config.php                    # Global configuration
│   ├── DB.php                        # Database connection
│   ├── Controller.php                # Base controller
│   ├── ContentModerationService.php  # Content moderation
│   ├── EmailService.php              # Email service
│   └── helpers.php                   # Utility functions
│
├── controllers/                      # Request handlers
│   ├── Home.php                      # Homepage
│   ├── AuthController.php            # Login/Registration
│   ├── Product.php                   # Products
│   ├── ReviewController.php          # Reviews & moderation
│   ├── OrderController.php           # Orders
│   ├── CartController.php            # Shopping cart
│   ├── Admin.php                     # Admin dashboard
│   ├── VnpayReturnController.php     # Payment handler
│   └── ...
│
├── models/                           # Database layer
│   ├── ReviewModel.php
│   ├── OrderModel.php
│   ├── ProductModel.php
│   └── ...
│
├── views/                            # User interface
│   ├── homePage.php
│   ├── adminPage.php
│   └── ...
│
├── public/                           # Assets
│   ├── css/                          # Stylesheets
│   ├── js/                           # JavaScript
│   └── images/                       # Images
│
├── vnpay_php/                        # VNPay Gateway
├── vendor/                           # PHP libraries
├── index.php                         # Entry point
└── composer.json                     # Dependencies
```

---

## Installation & Setup

### 1. System Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache (with mod_rewrite)
- Composer

### 2. Installation

```bash
# Clone/download the project
cd d:\xamcc\htdocs\phpnangcao\MVC

# Install dependencies
composer install

# Create database
# Import SQL from migrations/ folder
```

### 3. Configuration

Edit `app/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore_db');
define('BASE_URL', 'http://localhost/phpnangcao/MVC/');
```

### 4. Run Application

```
http://localhost/phpnangcao/MVC/
```

---

## Content Moderation System

### How It Works

1. Customer writes a review and submits
2. System automatically analyzes:
   - Detects spam and banned words
   - Calculates spam score (0-100)
   - Suggests approval/rejection
3. Admin moderates on Dashboard
4. Approved reviews display on product page

### Review States

|  Status  | Meaning         |
| :------: | :-------------- |
| pending  | Awaiting review |
| approved | Approved        |
| rejected | Rejected        |
|   spam   | Spam/Abuse      |

### Admin Dashboard

- Access: `/admin/reviews`
- Required role: Moderator or higher
- Features: Filter, view details, batch moderation

Details: [CONTENT_MODERATION_GUIDE.md](CONTENT_MODERATION_GUIDE.md)

---

## Technology Stack

|   Layer    | Technology    |
| :--------: | :------------ |
|  Backend   | PHP 7.4+ OOP  |
|  Database  | MySQL/MariaDB |
|  Frontend  | HTML, CSS, JS |
| Framework  | Bootstrap 5   |
|  Payment   | VNPay Gateway |
|   Email    | PHPMailer     |
| Versioning | Composer      |

---

## Database - Main Tables

### User Tables

| Table  | Description      |
| :----- | :--------------- |
| users  | Users, customers |
| admins | Administrators   |

### Sales Tables

| Table          | Description     |
| :------------- | :-------------- |
| products       | Book catalog    |
| product_types  | Book categories |
| orders         | Orders          |
| order_details  | Order items     |
| shopping_carts | Shopping carts  |

### Supporting Tables

| Table          | Description         |
| :------------- | :------------------ |
| reviews        | Reviews, moderation |
| wishlist       | Favorites           |
| news           | News articles       |
| promotions     | Promotions          |
| discount_codes | Discount codes      |
| chatbox        | Chat support        |

---

## Quick Start Guide

### For Admin

1. Log in with admin account
2. Go to Admin Dashboard
3. Select Review Management
4. View, filter, and moderate reviews
5. Add rejection reason if needed

### For Customers

1. Create account
2. Browse and purchase products
3. Write product reviews
4. Wait for approval
5. View your approved reviews

---

## Documentation

| File                                                       | Content               |
| :--------------------------------------------------------- | :-------------------- |
| [QUICKSTART.md](QUICKSTART.md)                             | 5-minute quick start  |
| [CONTENT_MODERATION_GUIDE.md](CONTENT_MODERATION_GUIDE.md) | Moderation details    |
| [DEPLOYMENT_SUMMARY.md](DEPLOYMENT_SUMMARY.md)             | Production deployment |

---

## Security

- Password Hashing: bcrypt
- SQL Injection Prevention: Prepared statements
- CSRF Protection: Authentication tokens
- Input Validation: Sanitize all inputs
- Session Security: Secure session management

---

## Notes

- Automatic moderation system helps prevent spam
- VNPay payment requires API key configuration
- Admins have full system control
- Regular database backups recommended

---

## License

This project is developed for educational purposes.

**Last Updated**: December 25, 2025
