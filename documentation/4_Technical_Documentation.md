# 4. Technical Documentation - Tools Asset Management System Genting Energy

## Architecture Deep Dive

### Entity-Relationship Model
The system is built on a robust relational schema tailored for Genting Energy's needs:
- **Tools <-> ToolItems**: A one-to-many relationship. A "Tool" record represents the product type, while "ToolItem" represents the specific serialized unit.
- **Loans <-> Employees/Tools**: Links transactions to specific personnel and inventory.
- **Locations <-> Racks**: Hierarchical storage mapping.

11. ### Advanced Data Integrity
12. To prevent accidental data loss and maintain a perfect audit trail, the system implements:
13. - **Soft Deletes**: Deleted records are hidden from the UI but remain in the database.
14. - **`deleted_by` Tracking**: Every deletion records the User ID of the administrator who performed the action.
15. - **GIN Indexes**: Used on PostgreSQL text columns for rapid fuzzy searching.

## Backend Components (Laravel)

### 1. Controllers
- `LoanController`: Manages the complex state transitions of an asset (Available -> Ongoing -> Returned).
- `ReportingCenterController`: Aggregates data from multiple tables using efficient Eloquent queries for high-speed Excel export.
- `NotificationComposer`: A View Composer that injects real-time alert data (Overdue/Low Stock) into the Sidebar/Topbar without redundant database calls.

23. ### 2. Security & Middleware
24. - **Auth Middleware**: Ensures only logged-in administrators can access management routes.
25. - **Keep-Alive**: A background session-heartbeat mechanism to prevent session expiration during long entries.
26. - **CSRF Protection**: Native protection for all form submissions.
27. - **Data Validation**: Strict Laravel Validation rules for all CSV/Excel imports to prevent database corruption.

### 3. Automated Tasks (Cron)
The system uses the `php artisan schedule:run` command to automate:
- Checking for overdue loans and sending **Queue-based Emails** via SMTP.
- Updating notification caches.
- Cleaning up temporary export files.

## Frontend Architecture
- **Vite Asset Bundler**: Compiles and minifies CSS/JS for production performance.
- **Select2**: Implemented for searchable, high-performance dropdowns in large datasets (e.g., choosing from 2000+ employees).
- **SweetAlert2**: Standardized UI for confirmation dialogs and error notifications.
- **Responsive Layout**: Designed to work on Desktop workstations and Tablet devices for floor staff.
