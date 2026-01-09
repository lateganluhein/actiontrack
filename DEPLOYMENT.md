# ActionTrack - Deployment Guide

## Deployment to Afrihost Shared Hosting

### Prerequisites
- Afrihost shared hosting account (manycents.co.za)
- FTP access or cPanel File Manager
- MySQL database access
- SSH access (optional, for running commands)

---

## Step 1: Prepare Local Environment

```bash
cd /home/lategan/manycents/actions

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Generate application key (copy this to production .env)
php artisan key:generate --show
```

---

## Step 2: Create MySQL Database

1. Log into cPanel: https://cpanel.manycents.co.za
2. Go to **MySQL Databases**
3. Create database: `manycez2q2t3_actions`
4. Create user: `manycez2q2t3_actions`
5. Add user to database with **ALL PRIVILEGES**

---

## Step 3: Upload Files

### Directory Structure on Server

```
/home/manycez2q2t3/
├── public_html/
│   └── actions/              ← Subdomain document root
│       ├── index.php         ← Modified (see below)
│       ├── .htaccess
│       ├── css/
│       │   └── app.css
│       └── js/
│           └── app.js
└── actiontrack/              ← Laravel app (OUTSIDE public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    └── artisan
```

### Upload Steps

1. **Upload Laravel app** (everything except `public/`) to `/home/manycez2q2t3/actiontrack/`

2. **Upload public files** (`public/*`) to `/home/manycez2q2t3/public_html/actions/`

3. **Modify `public_html/actions/index.php`:**

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode check
if (file_exists($maintenance = __DIR__.'/../../actiontrack/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader - MODIFIED PATH
require __DIR__.'/../../actiontrack/vendor/autoload.php';

// Bootstrap - MODIFIED PATH
(require_once __DIR__.'/../../actiontrack/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## Step 4: Create Subdomain

1. In cPanel, go to **Subdomains**
2. Create: `actions.manycents.co.za`
3. Document Root: `/home/manycez2q2t3/public_html/actions`

---

## Step 5: Configure Environment

Create `/home/manycez2q2t3/actiontrack/.env`:

```env
APP_NAME=ActionTrack
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://actions.manycents.co.za

LOG_CHANNEL=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=manycez2q2t3_actions
DB_USERNAME=manycez2q2t3_actions
DB_PASSWORD=YOUR_DATABASE_PASSWORD

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mail.manycents.co.za
MAIL_PORT=465
MAIL_USERNAME=office@manycents.co.za
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=office@manycents.co.za
MAIL_FROM_NAME="ActionTrack"
```

---

## Step 6: Set Permissions

Via SSH or cPanel Terminal:

```bash
cd /home/manycez2q2t3/actiontrack

# Make storage writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create storage directories if missing
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
```

---

## Step 7: Run Migrations

**Option A: Via SSH**
```bash
cd /home/manycez2q2t3/actiontrack
php artisan migrate --force
```

**Option B: Via Web Route** (temporary)

Add to `routes/web.php`:
```php
Route::get('/setup-database', function() {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migrations completed!';
});
```

Visit `https://actions.manycents.co.za/setup-database` then remove the route.

---

## Step 8: Create First User

**Option A: Via Registration**
Visit `https://actions.manycents.co.za/register`

**Option B: Via Tinker (SSH)**
```bash
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@manycents.co.za', 'password' => bcrypt('password')]);
```

---

## Step 9: Set Up Cron Job

1. In cPanel, go to **Cron Jobs**
2. Add new cron job:
   - Schedule: `* * * * *` (every minute)
   - Command: `cd /home/manycez2q2t3/actiontrack && php artisan schedule:run >> /dev/null 2>&1`

This will run the daily summary emails at 7:00 AM SAST.

---

## Step 10: Enable SSL

1. In cPanel, go to **SSL/TLS Status**
2. Enable AutoSSL for `actions.manycents.co.za`
3. Or use **Let's Encrypt** if available

---

## Troubleshooting

### 500 Error
- Check `storage/logs/laravel.log`
- Ensure storage permissions are correct
- Verify `.env` file exists and is configured

### Database Connection Error
- Verify database credentials in `.env`
- Ensure database user has correct permissions

### Email Not Sending
- Test SMTP settings: `php artisan tinker` then `Mail::raw('Test', fn($m) => $m->to('test@example.com'));`
- Check Afrihost email server status
- Verify port 465 is open

### CSS/JS Not Loading
- Check paths in `public_html/actions/`
- Verify `.htaccess` is present
- Clear browser cache

---

## Maintenance Commands

```bash
# Clear all caches
php artisan optimize:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check scheduled tasks
php artisan schedule:list

# Run summaries manually
php artisan activities:send-summaries

# Test email (dry run)
php artisan activities:send-summaries --dry-run
```

---

## Data Migration from Old System

To import data from the old SQLite database:

```bash
# Create migration seeder
php artisan make:seeder MigrateFromSqlite

# Run seeder (after implementing)
php artisan db:seed --class=MigrateFromSqlite
```

---

## File Checklist

### Must Upload to `/home/manycez2q2t3/actiontrack/`:
- [ ] `app/` directory (all PHP files)
- [ ] `bootstrap/` directory
- [ ] `config/` directory
- [ ] `database/` directory
- [ ] `resources/` directory
- [ ] `routes/` directory
- [ ] `storage/` directory (empty, will be populated)
- [ ] `vendor/` directory (from composer install)
- [ ] `.env` file (configured for production)
- [ ] `artisan` file
- [ ] `composer.json` and `composer.lock`

### Must Upload to `/home/manycez2q2t3/public_html/actions/`:
- [ ] `index.php` (modified paths)
- [ ] `.htaccess`
- [ ] `css/app.css`
- [ ] `js/app.js`

---

## Support

For issues with:
- **Hosting**: Contact Afrihost support
- **Application**: Check `storage/logs/laravel.log`
- **Email**: Verify SMTP settings with Afrihost

---

## Quick Reference

| Item | Value |
|------|-------|
| **URL** | https://actions.manycents.co.za |
| **Database** | manycez2q2t3_actions |
| **Timezone** | Africa/Johannesburg |
| **Daily Summary** | 07:00 SAST |
| **PHP Version** | 8.1+ required |
