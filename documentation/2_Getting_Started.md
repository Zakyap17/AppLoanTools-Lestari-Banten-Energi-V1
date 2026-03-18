# 2. Getting Started - Tools Asset Management System Genting Energy

This guide provides exhaustive instructions for setting up the **Tools Asset Management System Genting Energy**.

## Technical Prerequisites

### Server Requirements
- **PHP**: 8.2 or 8.3 (Requires extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `tokenizer`, `xml`, `pgsql`, `gd`).
- **Database**: PostgreSQL 14 or higher (Recommended for search performance).
- **Web Server**: Nginx (Recommended) or Apache.
- **Node.js**: 18.x or 20.x LTS.
- **Composer**: 2.6+.

## Local Development Setup (Windows/XAMPP)

1. **Clone & Initialize**:
   ```powershell
   git clone [repository-url]
   cd SystemGudang_Lbe
   ```

2. **Backend Installation**:
   ```powershell
   composer install
   ```

3. **Frontend Compilation (Vite)**:
   ```powershell
   npm install
   npm run build # or 'npm run dev' for development
   ```

4. **Environment Configuration**:
   - Rename `.env.example` to `.env`.
   - **Database Setup**:
     ```env
     DB_CONNECTION=pgsql
     DB_HOST=127.0.0.1
     DB_PORT=5432
     DB_DATABASE=warehouse_db
     DB_USERNAME=postgres
     DB_PASSWORD=your_password
     ```
   - **Mail Setup**: Configure `MAIL_HOST` and `MAIL_PASSWORD` to enable notifications.

5. **Application Initialization**:
   ```powershell
   php artisan key:generate
   php artisan migrate --seed
   ```

6. **Local Access**:
   - Run `php artisan serve`.
   - Open `http://localhost:8000` in your browser.

## Advanced: Server & Network Configuration

### A. Static IP & Network Bridging (VirtualBox)
If deploying in a virtualized environment for LAN access:
1. Set VirtualBox Adapter 1 to **"Bridged Adapter"**.
2. Configure a Static IP in Ubuntu via `netplan`:
   ```yaml
   addresses: [192.168.1.100/24]
   gateway4: 192.168.1.1
   ```
3. Update `.env` `APP_URL` to the Static IP.

### B. Production Optimization
Run these commands after every deployment to ensure maximum speed:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Production Deployment (Ubuntu LTS)

For a stable production environment on Ubuntu 22.04/24.04 LTS:

1.  **Install PHP-FPM & Nginx**:
    `sudo apt install nginx php8.2-fpm php8.2-pgsql php8.2-gd php8.2-mbstring ...`
2.  **Queue Worker Setup**:
    The system uses asynchronous queues for emails. Use **Supervisor** to keep the worker running:
    ```ini
    [program:laravel-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/SystemGudang_Lbe/artisan queue:work --sleep=3 --tries=3
    autostart=true
    autorestart=true
    user=www-data
    ```
3.  **Scheduler Integration**:
    Add the Laravel scheduler to your crontab:
    `* * * * * cd /var/www/SystemGudang_Lbe && php artisan schedule:run >> /dev/null 2>&1`

### C. File Permissions
Ensure the web user can write to necessary directories:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```
