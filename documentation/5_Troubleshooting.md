# 5. Troubleshooting Guide - Tools Asset Management System Genting Energy Group

This guide provides detailed, step-by-step resolution procedures for every specific module failure or user error.

---

## 1. Master Data Module Troubleshooting

### 1.1 Location & Rack Errors
**Problem: Cannot Assign Rack to a Tool**
- **Symptom**: The Rack dropdown is empty when creating a tool.
- **Root Cause**: The Rack is not correctly linked to the selected parent Location.
- **Step-by-Step Resolution:**
    1.  Go to **Master Data > Racks**.
    2.  Find the rack you intended to use.
    3.  Check the "Location" column. If incorrect, click **Edit**.
    4.  Select the correct parent **Location** and click **Save**.
    5.  Return to the Tool entry form and re-select the Location; the Rack will now appear.

---

### 1.2 Tool Master Data Issues
**Problem: "Duplicate Item Code/Serial Number" Error**
- **Symptom**: System rejects the creation of a new asset.
- **Root Cause**: Manual data entry error or data collision with an existing unit.
- **Step-by-Step Resolution:**
    1.  Go to **Master Data > Tools**.
    2.  Use the search bar to enter the *Serial Number* or *Tool Code* you were trying to add.
    3.  If a match is found, verify if the physical tool already has a record.
    4.  If the record belongs to a different tool, check the physical label for errors.
    5.  If it's a legacy entry, edit the existing record instead of creating a duplicate.

---

## 2. Transaction Module Troubleshooting

### 2.1 Loan & Return Errors
**Problem: Tool is Physically Returned but Status is "In Use"**
- **Symptom**: The tool cannot be borrowed again because it is "Locked".
- **Root Cause**: Operator skipped the formal "Return" process.
- **Step-by-Step Resolution:**
    1.  Go to **Transactions > Loan List**.
    2.  Search for the tool via *Serial Number*.
    3.  Locate the entry where the status is **"Ongoing"**.
    4.  Click the **Return** button.
    5.  Confirm the date and quantity, then click **Complete**.
    6.  Status will immediately refresh to **"Available"**.

---

### 2.2 Damage & Lost Errors
**Problem: Repaired Tool Remains in "Damage" Status**
- **Symptom**: Tool shows as available in storage but is blocked in the system.
- **Root Cause**: The Incident Report was never "Resolved".
- **Step-by-Step Resolution:**
    1.  Go to **Transactions > Damage/Lost**.
    2.  Filter the list by **Status: Open**.
    3.  Locate the report for the specific tool.
    4.  Click the **Resolve** button.
    5.  Select **Resolution Method: Repaired**.
    6.  Add a resolution note (e.g., "Part replaced on 05 Feb").
    7.  Click **Submit Resolution**. Tool is now unlocked.

---

### 2.3 Calibration Tracking Issues
**Problem: Tool Stuck in "Calibration" Status after return from Lab**
- **Symptom**: Asset cannot be loaned out after maintenance.
- **Root Cause**: The Calibration Issuing record was not closed.
- **Step-by-Step Resolution:**
    1.  Go to **Transactions > Calibration Issuing**.
    2.  Filter for **Status: In Progress**.
    3.  Click **Receive/Complete** on the tool entry.
    4.  Enter the **Maintenance Cost** and the **Next Calibration Date**.
    5.  Click **Close Transaction**. Tool returns to **"Available"**.

---

## 3. System & Background Troubleshooting

### 3.1 Email & Notification Failures
**Problem: Overdue Reminders are NOT being sent to borrowers**
- **Symptom**: Loans are overdue but no emails arrived.
- **Step-by-Step Resolution:**
    1.  **Check Personnel Data**: Go to **Master Data > Employees**. Ensure the borrower has a valid email address.
    2.  **Verify SMTP Configuration**: Open the `.env` file on the server. Ensure `MAIL_HOST` and `MAIL_PASSWORD` are correct.
    3.  **Check Task Scheduler**: Run `php artisan schedule:list` in the terminal.
    4.  **Manual Pulse**: Run `php artisan schedule:run` once to force an alert check.
    5.  **Test via Tinker**: Run the manual mail test script found in the **Administrative (Tinker) Guide**.

---

### 3.2 Performance & Search Issues
**Problem: Search takes more than 10 seconds**
- **Symptom**: Dashboard or Tool List is unresponsive.
- **Step-by-Step Resolution:**
    1.  **Clear Cache**: Run `php artisan optimize:clear` in the terminal.
    2.  **Rebuild Indexes**: run `php artisan migrate --force` to ensure optimization migrations are applied.
    3.  **Restart Web Server**: Run `sudo systemctl restart nginx` (Production) or restart XAMPP Apache.
