<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('loan:send-overdue-emails')->dailyAt('08:00');
\Illuminate\Support\Facades\Schedule::command('app:send-loan-reminders')->dailyAt('08:00');
