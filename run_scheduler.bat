@echo off
cd /d "%~dp0"
title Laravel Scheduler - Keep window OPEN
echo Starting Laravel Scheduler...
echo Emails will be sent at 08:00 (8 AM).
echo Keep this window open. 
echo.
php artisan schedule:work
pause
