# Deployment Guide for Calibration Reminder Feature (Manual Approach)

Follow these steps to safely deploy the new Calibration Reminder feature manually onto your Ubuntu Server without using `git pull`.

## 1. Create the Database Migration
Navigate to your application directory:
```bash
cd /var/www/SystemGudang_Lbe  # Replace with your actual project path
```
Create a new migration file:
```bash
php artisan make:migration add_last_calibration_reminder_sent_at_to_tools_table --table=tools
```
Open the newly created migration file in `database/migrations/` (the filename will have today's timestamp) using `nano` and ensure the `up` and `down` methods look exactly like this:
```php
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            if (!Schema::hasColumn('tools', 'last_calibration_reminder_sent_at')) {
                $table->timestamp('last_calibration_reminder_sent_at')->nullable()->after('next_calibration_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            if (Schema::hasColumn('tools', 'last_calibration_reminder_sent_at')) {
                $table->dropColumn('last_calibration_reminder_sent_at');
            }
        });
    }
```
*Save and exit (`Ctrl+O`, `Enter`, `Ctrl+X`).*

Run the migration:
```bash
php artisan migrate
```

## 2. Create the Mailable and Email Template
Create the mailable class:
```bash
php artisan make:mail CalibrationReminder
```
Edit the mailable (`app/Mail/CalibrationReminder.php`) with `nano` and replace its contents with the updated code that accepts `$tools`. 

**Make sure you add `implements ShouldQueue`** into the class declaration so it works with your server's queue worker:
```php
class CalibrationReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    // ...
```
```php
    public function content(): Content
    {
        return new Content(
            view: 'emails.calibration_reminder',
            with: [
                'tools' => $this->tools,
            ],
        );
    }
```

Next, create the Blade template file:
```bash
mkdir -p resources/views/emails
nano resources/views/emails/calibration_reminder.blade.php
```
*Paste the HTML code for the email template from your local project, then save and exit.*

## 3. Create the Console Command
Generate the command class:
```bash
php artisan make:command SendCalibrationReminders
```
Edit the command file (`app/Console/Commands/SendCalibrationReminders.php`) with `nano` and paste the logic that checks the H-30 and H-14 dates (copy from your local codebase).

## 4. Update the Scheduler and Topbar (Copy-Paste)
Using `nano`, edit the following files and apply the new lines:

**A. `routes/console.php`**
Add this schedule block:
```php
Schedule::command('app:send-calibration-reminders')
    ->timezone('Asia/Jakarta')
    ->dailyAt('08:15');
```

**B. `app/View/Composers/NotificationComposer.php`**
Update the `compose` method to fetch and count the tools needing calibration, then add them to the `$data` array.

**C. `resources/views/partials/topbar.blade.php`**
Find the notification bell dropdown and paste the new `@if(isset($notifCalibrationCount)...` block under the Overdue Loans or Low Stock sections.

*(You can copy the exact snippets from your local files to the server using nano).*

## 5. Clear Caches
Once all files are updated, run these commands to ensure Laravel registers the new classes and views:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 6. Verify Locally
You can manually run the command on the server to ensure it doesn't throw errors:
```bash
php artisan app:send-calibration-reminders
```

5. **Restart Queue Workers and Supervisor (If Applicable):**
   If you use supervisor or a daemon to run queues and schedule workers, restart them to pick up the new Mailable and commands.
   ```bash
   sudo systemctl restart supervisor
   ```

## Verification
You can manually trigger the command to verify it runs without errors:
```bash
php artisan app:send-calibration-reminders
```
It should output how many tools require reminders or tell you that no tools currently meet the criteria.
