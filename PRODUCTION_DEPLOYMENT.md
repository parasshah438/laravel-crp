# Laravel Production Deployment Guide

## Environment Changes in Production

### The Problem
When using `php artisan serve` (development server), environment changes cause server restarts and temporary downtime. This is **NOT suitable for production**.

### Production Solution

#### 1. Use Proper Web Server (Apache/Nginx + PHP-FPM)

**Apache Configuration (.htaccess already included)**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Nginx Configuration**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 2. Environment Configuration

**Production .env Settings**
```env
# Application
APP_NAME="Your Application Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Security
APP_KEY=your-32-character-random-string

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_database
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Cache & Sessions (for better performance)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### 3. Production Deployment Steps

**Initial Deployment:**
```bash
# 1. Clone repository
git clone your-repo.git /var/www/your-app
cd /var/www/your-app

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Set permissions
sudo chown -R www-data:www-data /var/www/your-app
sudo chmod -R 755 /var/www/your-app
sudo chmod -R 775 /var/www/your-app/storage
sudo chmod -R 775 /var/www/your-app/bootstrap/cache

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Database setup
php artisan migrate --force
php artisan db:seed --force

# 6. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**For Updates:**
```bash
# 1. Pull latest code
git pull origin main

# 2. Update dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Run migrations (if any)
php artisan migrate --force

# 4. Clear and rebuild caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 5. Restart PHP-FPM (if needed)
sudo systemctl reload php8.2-fpm
```

## How Environment Changes Work in Production

### When You Change Settings via Admin Panel:

1. **Immediate Effect**: Configuration changes are written to `.env` file
2. **Gradual Rollout**: PHP-FPM processes pick up changes as they reload
3. **No Downtime**: Application stays online during the process
4. **Cache Management**: System automatically clears and rebuilds caches

### Production-Safe Features in Your Admin Panel:

✅ **Database Connection Testing**: Tests before applying changes
✅ **Rollback on Failure**: Automatically reverts failed database changes
✅ **Gradual Cache Clearing**: Clears caches safely without downtime
✅ **OPcache Reset**: Clears PHP opcache in production
✅ **Production-Aware Messages**: Different messages for prod vs dev

## Monitoring Environment Changes

### Create Logging for Environment Changes

Add this to your `AppServiceProvider` boot method:

```php
// Log environment changes in production
if (app()->environment('production')) {
    Event::listen('env.updated', function ($changes) {
        Log::info('Environment variables updated', [
            'changes' => $changes,
            'user' => auth()->user()?->email,
            'ip' => request()->ip(),
            'timestamp' => now()
        ]);
    });
}
```

### Health Check Endpoint

Create a health check route to monitor your application:

```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'env' => app()->environment(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::get('health-check') ? 'working' : 'not-working'
    ]);
});
```

## Best Practices for Production

### 1. Always Use Queue Workers
```bash
# Use supervisor to manage queue workers
sudo systemctl start laravel-queue-worker
```

### 2. Use Redis for Better Performance
```bash
# Install Redis
sudo apt install redis-server
```

### 3. Enable OPcache
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 4. Use HTTPS
```bash
# Install SSL certificate (Let's Encrypt)
sudo certbot --nginx -d your-domain.com
```

## Testing the Production Setup

### 1. Load Testing
```bash
# Use Apache Bench for basic load testing
ab -n 1000 -c 10 https://your-domain.com/
```

### 2. Monitor During Environment Changes
```bash
# Monitor PHP-FPM processes
watch -n 1 'ps aux | grep php-fpm'

# Monitor application logs
tail -f storage/logs/laravel.log
```

### 3. Database Connection Monitoring
```bash
# Monitor database connections
mysql -e "SHOW PROCESSLIST;"
```

## Summary

**Development Server (`php artisan serve`):**
- ❌ Restarts on environment changes
- ❌ Causes downtime
- ❌ Not suitable for production

**Production Server (Apache/Nginx + PHP-FPM):**
- ✅ No restarts needed
- ✅ Zero downtime environment updates
- ✅ Gradual configuration reload
- ✅ Better performance and stability

Your admin panel is already optimized for production with:
- Database connection testing
- Safe cache clearing
- Production-aware messaging
- Automatic rollback on failures

The key is using a proper web server stack instead of Laravel's development server.