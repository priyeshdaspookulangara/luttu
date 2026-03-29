# PHP MySQLi E-Commerce & PV Wallet System

A secure, modular e-commerce platform with a Point Value (PV) reward system.

## Setup Instructions
1. Import `sql/schema.sql` into your MySQL database.
2. Configure `includes/db_connect.php` with your database credentials.
3. Default Admin credentials:
   - Username: `admin`
   - Password: `admin123`

## Features
- **Dynamic Category Attributes:** Define custom properties for products per category.
- **PV Wallet System:** Earn PV points on every purchase based on product margin.
- **Conversion Ledger:** All PV transactions are recorded in a ledger table for audit trails.
- **Withdrawal Requests:** Users can request to withdraw PV as cash once they hit the admin-defined threshold.
- **Role-Based Access:** Separate panels for Admins and Users.
- **Security:** Prepared statements for all SQL queries and password hashing.
