# 4. Technical Documentation - Tools Asset Management System Genting Energy

## Architecture Deep Dive

### Entity-Relationship Model
The system is built on a robust relational schema tailored for Genting Energy's needs:
- **Tools <-> ToolItems**: A one-to-many relationship. A "Tool" record represents the product type, while "ToolItem" represents the specific serialized unit.
- **Loans <-> Employees/Tools**: Links transactions to specific personnel and inventory.
- **Locations <-> Racks**: Hierarchical storage mapping.

### Advanced Search Optimization
To handle thousands of assets without performance degradation, the system implements:
- **GIN Indexes**: Used on PostgreSQL text columns for rapid fuzzy searching.
- **Combined B-Tree Indexes**: Optimized for status filtering and date range queries.

## Backend Components (Laravel)

### 1. Controllers
- `LoanController`: Manages the complex state transitions of an asset (Available -> Ongoing -> Returned).
- `ReportingCenterController`: Aggregates data from multiple tables using efficient Eloquent queries for high-speed Excel export.
- `NotificationComposer`: A View Composer that injects real-time alert data (Overdue/Low Stock) into the Sidebar/Topbar without redundant database calls.

### 2. Security & Middleware
- **Auth Middleware**: Ensures only logged-in administrators can access management routes.
- **CSRF Protection**: Native protection for all form submissions.
- **Data Validation**: Strict Laravel Validation rules for all CSV/Excel imports to prevent database corruption.

### 3. Automated Tasks (Cron)
The system uses the `php artisan schedule:run` command to automate:
- Checking for overdue loans.
- Updating notification caches.
- Cleaning up temporary export files.

## Frontend Architecture
- **Vite Asset Bundler**: Compiles and minifies CSS/JS for production performance.
- **Select2**: Implemented for searchable, high-performance dropdowns in large datasets (e.g., choosing from 2000+ employees).
- **SweetAlert2**: Standardized UI for confirmation dialogs and error notifications.
- **Responsive Layout**: Designed to work on Desktop workstations and Tablet devices for floor staff.
