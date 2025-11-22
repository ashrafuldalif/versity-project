# Admin Security Setup Instructions

## Overview
Admin security has been added to protect all admin pages and functions. This includes:
- Admin login page
- Session-based authentication
- Security checks on all admin pages
- Protected admin functions

## Setup Steps

### 1. Create Admin Database Table
Run the SQL file to create the admin table:

```sql
-- Execute admin_setup.sql in your database
```

Or manually run:
```sql
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. Create Default Admin Account

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

**⚠️ IMPORTANT: Change this password immediately after first login!**

To create the default admin account, run:
```sql
INSERT INTO admins (username, password, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com');
```

The password hash above is for `admin123`. To create a new admin with a different password, use PHP's `password_hash()` function:

```php
<?php
$password = 'your_secure_password';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash; // Use this in your INSERT statement
?>
```

### 3. Access Admin Panel

1. Navigate to: `http://your-domain/admin/login.php`
2. Login with default credentials (or your custom admin account)
3. You'll be redirected to the admin dashboard

### 4. Change Default Password

After logging in, you should immediately change the default password. You can do this by:

1. Creating a PHP script to update the password:
```php
<?php
include 'funcs/connect.php';
$newPassword = 'your_new_secure_password';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
$username = 'admin';

$stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = ?");
$stmt->bind_param('ss', $hash, $username);
$stmt->execute();
echo "Password updated!";
?>
```

## Protected Pages

The following pages now require admin authentication:
- `/admin/index.php` - Members management
- `/admin/executives.php` - Executives management
- `/admin/requests.php` - Executive requests
- `/admin/edit_gallery.php` - Gallery management
- `/admin/upcomings_crud.php` - Upcomings management

## Protected Functions

The following API endpoints now require admin authentication:
- `/funcs/toggle_executive.php` - Toggle executive active status
- `/funcs/save_gallery_changes.php` - Save gallery changes
- `/funcs/upload_gallery_images.php` - Upload gallery images

## Logout

Click the "Logout" button in the admin navigation to end your session.

## Security Features

1. **Session-based authentication** - Uses PHP sessions
2. **Password hashing** - Uses PHP's `password_hash()` with bcrypt
3. **Database verification** - Verifies admin account exists on each request
4. **Automatic redirect** - Unauthorized users are redirected to login
5. **Session timeout** - Sessions expire when browser is closed (default PHP behavior)

## Troubleshooting

### Can't login?
- Verify the admin table exists
- Check that the admin account was created
- Ensure password hash is correct
- Check PHP session configuration

### Getting redirected to login on every page?
- Check that `session_start()` is called before any output
- Verify session directory is writable
- Check PHP error logs

### Need to reset admin password?
Run the password update script mentioned in step 4 above.

