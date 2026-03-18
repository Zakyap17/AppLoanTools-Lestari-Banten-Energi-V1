# 7. Ubuntu LTS Administration Guide

This guide is for System Administrators managing the production environment on Ubuntu 22.04 or 24.04 LTS.

## 1. Service Management
Control the core services required for the application.

- **Nginx (Web Server)**:
  - Status: `sudo systemctl status nginx`
  - Restart: `sudo systemctl restart nginx`
- **PHP-FPM**:
  - Status: `sudo systemctl status php8.2-fpm`
  - Restart: `sudo systemctl restart php8.2-fpm`
- **PostgreSQL (Database)**:
  - Status: `sudo systemctl status postgresql`
  - Access CLI: `sudo -u postgres psql`

## 2. Queue & Background Tasks
The system relies on background processes for sending emails and processing large data.

### 2.1 Supervisor (Queue Worker)
- **Check Status**: `sudo supervisorctl status`
- **Restart Worker**: `sudo supervisorctl restart laravel-worker:*`
- **Log File**: `/var/log/supervisor/laravel-worker.log`

### 2.2 Laravel Scheduler (Cron)
- **Verify Cron**: `crontab -l -u www-data`
- Ensure this line exists:
  `* * * * * cd /var/www/SystemGudang_Lbe && php artisan schedule:run >> /dev/null 2>&1`

## 3. Logs & Monitoring
Monitor system health and errors in real-time.

- **Application Logs**: `tail -f storage/logs/laravel.log`
- **Nginx Error Logs**: `sudo tail -f /var/log/nginx/error.log`

## 4. Database Maintenance
- **Backup Database**:
  `pg_dump -U postgres warehouse_db > backup_$(date +%Y%m%d).sql`
- **Restore Database**:
  `psql -U postgres warehouse_db < backup_file.sql`

## 5. Directory Permissions Fix
If you encounter "Permission Denied" errors:
```bash
cd /var/www/SystemGudang_Lbe
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```
