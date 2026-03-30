<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler 
|--------------------------------------------------------------------------
| 
| - everyMinute() untuk testing
*/

Schedule::command('loan:send-overdue-emails')
    ->timezone('Asia/Jakarta')
    ->dailyAt('08:15');

Schedule::command('app:send-loan-reminders')
    ->timezone('Asia/Jakarta')
    ->dailyAt('08:15');

Schedule::command('app:send-calibration-reminders')
    ->timezone('Asia/Jakarta')
    ->dailyAt('08:15');

Schedule::command('ldap:sync')
    ->timezone('Asia/Jakarta')
    ->everyFifteenMinutes();

// Run queue worker in the background for emails
Schedule::command('queue:work --stop-when-empty')
    ->everyMinute();

// Backup Database Daily
Schedule::command('db:backup')
    ->timezone('Asia/Jakarta')
    ->dailyAt('23:00');
