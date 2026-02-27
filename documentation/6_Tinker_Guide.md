# 6. Administrative Guide (Laravel Tinker) - Genting Energy

Laravel Tinker is a powerful REPL (Read-Eval-Print Loop) that allows administrators to interact with the system's database and logic via the command line.

## 1. Accessing Tinker
Open your terminal on the server and run:
```bash
php artisan tinker
```
You will enter an interactive PHP environment where you can execute code directly.

---

## 2. User Management Commands

### 2.1 Creating a New Administrator
If the web UI is inaccessible, you can create a user manually:
```php
// Execute inside Tinker
\App\Models\User::create([
    'name'     => 'Administrator Name',
    'username' => 'admin_username',
    'password' => \Illuminate\Support\Facades\Hash::make('your_secure_password'),
]);
```

### 2.2 Resetting a Password
```php
$user = \App\Models\User::where('username', 'admin_username')->first();
$user->password = \Illuminate\Support\Facades\Hash::make('new_password');
$user->save();
```

---

## 3. Manual Notification Testing
Use these commands to verify that your email server (SMTP) is working correctly without waiting for the scheduler.

### 3.1 Test "Loan Created" Email
```php
$loan = \App\Models\Loan::first(); // Pick any existing loan
\Illuminate\Support\Facades\Mail::to('your-email@example.com')->send(new \App\Mail\LoanCreated($loan));
```

### 3.2 Test "Overdue Notification" Email
This command simulates an overdue alert for testing.
```php
$borrowerName = "Test Employee";
$loans = \App\Models\Loan::where('status', 'ongoing')->take(3)->get();
\Illuminate\Support\Facades\Mail::to('your-email@example.com')->send(new \App\Mail\OverdueLoanNotification($borrowerName, $loans));
```

### 3.3 Test "Loan Reminder" (Scheduled Alert)
```php
$loan = \App\Models\Loan::where('status', 'ongoing')->first();
$daysLeft = 2; // Simulate a 2-day reminder
\Illuminate\Support\Facades\Mail::to('your-email@example.com')->send(new \App\Mail\LoanReminder($loan, $daysLeft));
```

---

## 4. Advanced Database Operations

### 4.1 Manual Asset Status Reset
If an asset is "stuck" in a status (e.g., 'In Use' but the loan is gone):
```php
$tool = \App\Models\Tool::where('code', 'TOOL-KODE-123')->first();
$tool->status = 'available';
$tool->save();
```

### 4.2 Clearing Notification Cache
The system caches topbar notifications to improve speed. To force a refresh:
```php
\Illuminate\Support\Facades\Cache::forget('topbar_notifications');
```

---

## 5. Security Warning
> [!CAUTION]
> Commands executed in Tinker are permanent and directly affect the production database. Always double-check your code (especially `where` clauses) before running `$model->save()` or `$model->delete()`.
