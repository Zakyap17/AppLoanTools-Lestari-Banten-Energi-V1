# 3. User Manual - Tools Asset Management System Genting Energy Group

This manual provides granular, step-by-step operational instructions for every functional module within the Genting Energy Group system.

---

## SECTION 1: Master Data Foundations (Setup)

### 1.1 Locations Management (Storage Level 1)
This module establishes the physical buildings or warehouses where assets are stored.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Locations** on the sidebar.
2.  Click the **Create New** (or +) button.
3.  **Storage Code**: Input a unique identifier (e.g., `WH-A`).
4.  **Storage Name**: Enter the descriptive name (e.g., `Main Logistics Warehouse`).
5.  **Storage Type**: Select the type (e.g., `Indoor`, `Climate Controlled`).
6.  **Warehouse**: Specify the overarching warehouse group.
7.  **Total Bin**: Initial estimation of available bins (optional).
8.  Click **Save**.

---

### 1.2 Racks & Bins Architecture (Storage Level 2 & 3)
Defines the specific shelving units within a Location.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Racks**.
2.  Click **Create New**.
3.  **Location**: Select the parent **Location** (from step 1.1) from the dropdown.
4.  **Rack Code**: Assign a code (e.g., `RACK-01`).
5.  **Room/Building**: Specify the exact room within the warehouse.
6.  **Description**: Add notes about the rack's capacity or specialized use.
7.  Click **Save**.

---

### 1.3 Tools Type (Categorization)
Estapes the taxonomic categories for assets.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Tools Type**.
2.  Click **Create New**.
3.  **Code**: Enter a category code (e.g., `PNEU`).
4.  **Name**: Enter the category name (e.g., `Pneumatic Tools`).
5.  **Description**: Define what kind of assets belong here.
6.  Click **Save**.

---

### 1.4 Material Group & Sub-Groups (Industrial Standard)
Classification for procurement and materials management.

**Step-by-Step Operations (Group):**
1.  Navigate to **Master Data > Material Group**.
2.  Follow the **Create New** process: Enter `Code` (e.g., `MG-01`) and `Description`.
3.  Save the record.

**Step-by-Step Operations (Sub-Group):**
1.  Navigate to **Master Data > Material Sub-Group**.
2.  Click **Create New**.
3.  **Group Code**: Link this sub-group to a parent **Material Group**.
4.  **Sub-Group Code**: Enter a unique child code (e.g., `MG-01-A`).
5.  Save the record.

---

### 1.5 Manufacturers Center (OEM Management)
Tracking the Original Equipment Manufacturers for Genting Energy assets.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Manufacturers**.
2.  Click **Create New**.
3.  Enter the `Name`, `Address`, and `Contact Details`.
4.  Click **Save**.

---

### 1.6 Vendor Calibration (Service Partners)
Management of 3rd-party labs providing calibration services.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Vendor Calibration**.
2.  Click **Create New**.
3.  **Fields**: Register the laboratory's `Name`, `Phone`, and `Email`.
4.  Click **Save**.

---

### 1.7 Employee (Personnel) Master
Governance of users authorized to borrow assets.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Employees**.
2.  **Manual Add**: Click **Create New**, Enter `NIK` (Critical: Unique ID), `Name`, `Department`, and `Position`.
3.  **Bulk Sync**: Click **Import**, download the Template CSV, populate internal HR data, and upload.

---

### 1.8 Master Tools Registry (The Central Inventory)
The primary entry point where assets are formally registered.

**Step-by-Step Operations:**
1.  Navigate to **Master Data > Tools**.
2.  Click **Create New**.
3.  **Core Id**: Enter `Tool Code` and `Serial Number`.
4.  **Reference Data**: Select the `Location`, `Type`, and `Manufacturer` from the dropdowns established in previous steps.
5.  **Maintenance Data**: Set initial `Purchase Date` and current `Calibration Status`.
6.  **Availability**: Set initial `Quantity` and `Status` (Default: `Available`).
7.  Click **Save**.

---

## SECTION 2: Transactional Modules (Operational Activities)

### 2.1 Loan Issuance (Lending Process)
**Step-by-Step Operations:**
1.  Go to **Transactions > New Loan**.
2.  **Asset Find**: Type the tool name or scan its barcode. The tool must be `Available`.
3.  **Identify Borrower**: Type the Employee's `NIK` or `Name`.
4.  **Timeline**: Set the `Expected Return Date`.
5.  **Authorization**: Add any internal notes if required.
6.  Click **Process Loan**. 

---

### 2.2 Loan Returns (Reconciliation)
**Step-by-Step Operations:**
1.  Go to **Transactions > Loan List**.
2.  Find the active loan via the search bar (NIK or Loan Code).
3.  Click the **Process Return** button.
4.  Verify the quantity and condition.
5.  Click **Confirm Return**.

---

### 2.3 Damage & Lost Reporting
**Step-by-Step Operations:**
1.  Go to **Transactions > Damage/Lost**.
2.  Click **Reporting Incident**.
3.  **Select Asset**: Choose the failed tool.
4.  **Classification**: Select `Damage` or `Lost`.
5.  **Severity**: Rate as `High/Medium/Low`.
6.  **Evidence**: Input the chrono-logical description of the incident.
7.  Click **Submit**.

---

### 2.4 Calibration Lifecycle (Service Issuing)
**Step-by-Step Operations:**
1.  Go to **Transactions > Calibration Issuing**.
2.  Click **New Issue**.
3.  Select the `Tool` and the target `Calibration Vendor`.
4.  Set the `Issue Date` and `Expected Completion`.
5.  Click **Send for Calibration**. 

---

### 2.5 Mutation (Asset Transfer)
**Step-by-Step Operations:**
1.  Go to **Transactions > Mutation**.
2.  Click **New Mutation**.
3.  **Route**: Select `Origin Location` and `Target Location`.
4.  **Assets**: Select the items being moved.
5.  **Auth**: Select the Authorized personnel overseeing the move.
6.  Click **Execute Mutation**.

---

## SECTION 3: Analytical Intelligence

### 3.1 Reporting Center
**Step-by-Step Operations:**
1.  Go to **Reporting Center**.
2.  Select the Report Type (e.g., `Inventory Audit`).
3.  **Filters**: Apply `Date Range`, `Manufacturer`, or `Status` filters.
4.  Click **Export To Excel** to get the formal audit document.
