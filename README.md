# Online Second-Hand Marketplace

A simple full-stack web application for buying and selling second-hand items - electronics, vinyl, books, clothing, collectibles, and more. Built with **HTML, CSS, JavaScript, PHP, and MySQL** for the term project.

## Features

### User features
- Register / login / logout (secure password hashing)
- Browse products by category
- Search products
- View product details
- List your own items for sale (with optional image upload)
- Add/remove items from cart, update quantities
- Checkout and place orders
- View personal order history

### Admin features
- Dashboard with stats (users, products, orders, revenue)
- Manage all product listings (edit/delete)
- View and update order statuses
- Manage user accounts (promote to admin / delete)

### Security
- Passwords hashed with `password_hash()` / `password_verify()`
- All database queries use **prepared statements** (SQL injection protection)
- Output escaped via `htmlspecialchars()` (XSS protection)
- Server-side validation in PHP
- Client-side validation in JavaScript

## Tech Stack
- **HTML5** - page structure
- **CSS** - simple custom styling, responsive grid
- **JavaScript** - client-side form validation
- **PHP** - server-side processing (PDO)
- **MySQL** - database
- **XAMPP / LAMP** - local development

## Setup Instructions

### 1. Install XAMPP (or LAMP/MAMP)
Download from https://www.apachefriends.org

### 2. Copy project to htdocs
Copy the entire `marketplace/` folder into your XAMPP `htdocs/` directory:
```
C:\xampp\htdocs\marketplace\
```

### 3. Start Apache + MySQL
Open the XAMPP Control Panel and start both services.

### 4. Import the database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click **Import**
3. Choose `sql/database.sql` from this project
4. Click **Go**

This creates the `marketplace` database with all tables and sample data.

### 5. Check database credentials
Open `includes/db.php` and confirm the settings match your setup:
```php
$host = 'localhost';
$dbname = 'marketplace';
$user = 'root';
$pass = '';
```
(Default XAMPP credentials - adjust if needed.)

### 6. Make uploads folder writable
The `uploads/` folder must be writable by the web server so users can upload product images.
On Linux/Mac:
```bash
chmod 755 uploads/
```

### 7. Open in browser
Navigate to: **http://localhost/marketplace/**

## Default Admin Account
```
Email:    admin@marketplace.com
Password: admin123
```
Use this to log in and access the admin panel at `/marketplace/admin/`.

**⚠ Change the admin password immediately in a real deployment.**

## Database Schema

- **users** - id, name, email, password, is_admin, created_at
- **products** - id, name, description, price, image_url, category, condition, stock, seller_id, created_at
- **cart** - id, user_id, product_id, quantity
- **orders** - id, user_id, total_price, order_date, status
- **order_items** - id, order_id, product_id, product_name, price, quantity

## Notes
- The checkout is a demo - no real payment is processed.
- If `/marketplace/` is not your base path, edit the absolute paths in `includes/header.php`, `includes/footer.php`, and the admin headers (look for `/marketplace/`).
- All prices are in USD for display only.

## Student Information

- **Name:** Jyoti Rani
- **Email:** Jyorani@algomau.ca
- **Student ID:** 5147310
- **Course:** COSC 2956
- **Instructor:**
- **Submission Date:**
- **Demo Video:** (YouTube unlisted / Google Drive link)
