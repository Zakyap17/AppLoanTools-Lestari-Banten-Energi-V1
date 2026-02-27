# Sistem Manajemen Gudang - ERD Lengkap

Dokumen ini memberikan representasi visual detail dari skema database untuk proyek **WarehouseLoan_lBE**, yang disusun berdasarkan modul fungsional.

## Diagram Hubungan Entitas (ERD)

```mermaid
erDiagram
    %% --- MODUL PERSONEL ---
    USERS ||--o{ MUTATION_LOGS : "melakukan aksi"
    EMPLOYEES ||--o{ LOANS : "meminjam"
    EMPLOYEES ||--o{ DAMAGE_LOSTS : "melaporkan"
    EMPLOYEES ||--o{ RECEIVES : "menerima barang"
    EMPLOYEES ||--o{ MUTATIONS : "otorisasi"

    %% --- MODUL DATA MASTER ---
    TOOLS_MAT_GROUPS ||--o{ TOOLS_MAT_SUB_GROUPS : "memiliki"
    TOOLS_MAT_SUB_GROUPS ||--o{ TOOLS : "mengkategorikan"
    TOOL_TYPES ||--o{ TOOLS : "mendefinisikan tipe"
    MANUFACTURERS ||--o{ TOOLS : "memproduksi"
    LOCATIONS ||--o{ RACKS : "berisi"
    LOCATIONS ||--o{ TOOLS : "penyimpanan default"
    RACKS ||--o{ TOOLS : "rak default"

    %% --- MANAJEMEN ASET ---
    TOOLS ||--o{ TOOL_ITEMS : "terdiri dari"
    TOOL_ITEMS ||--o{ LOANS : "item yang dipinjam"
    TOOL_ITEMS ||--o{ DAMAGE_LOSTS : "item terdampak"
    TOOL_ITEMS ||--o{ MUTATION_LOGS : "item dilacak"

    %% --- LOGISTIK & PEMELIHARAAN ---
    LOCATIONS ||--o{ MUTATIONS : "dari/ke"
    LOCATIONS ||--o{ MUTATION_LOGS : "dari/ke"
    RACKS ||--o{ MUTATION_LOGS : "log tingkat rak"
    VENDOR_CALIBRATIONS ||--o{ CALIBRATION_ISSUINGS : "melayani servis"
    TOOLS ||--o{ CALIBRATION_ISSUINGS : "dikalibrasi"

    USERS {
        bigint id PK
        string username UK
        string name
        string email
        string password
        string ldap_dn
    }

    EMPLOYEES {
        bigint id PK
        string nik UK
        string name
        string department
        string position
    }

    TOOLS {
        bigint id PK
        string code UK
        string name
        string serial_number
        string specification
        bigint type_id FK
        bigint manufacturer_id FK
        bigint sub_group_id FK
        bigint location_id FK
        bigint rack_id FK
        string status
        integer quantity
    }

    TOOL_ITEMS {
        bigint id PK
        bigint tool_id FK
        string serial_number UK "Kode Item (misal: MT-001)"
        string condition
        string status "available/in_use/etc"
    }

    LOCATIONS {
        bigint id PK
        string code UK
        string name
        string warehouse
    }

    RACKS {
        bigint id PK
        bigint location_id FK
        string code
    }

    LOANS {
        bigint id PK
        string loan_code UK
        bigint tool_id FK
        bigint tool_item_id FK
        bigint employee_id FK "Peminjam"
        date loan_date
        date expected_return_date
        date actual_return_date
        string status
    }

    DAMAGE_LOSTS {
        bigint id PK
        string report_code UK
        bigint tool_id FK
        bigint tool_item_id FK
        bigint employee_id FK "Pelapor"
        string type "damage/lost"
        string status "open/resolved"
    }

    MUTATION_LOGS {
        bigint id PK
        bigint tool_id FK
        bigint tool_item_id FK
        bigint from_location_id FK
        bigint to_location_id FK
        bigint user_id FK "Dilakukan Oleh"
        datetime created_at
    }

    CALIBRATION_ISSUINGS {
        bigint id PK
        string issue_code UK
        bigint tool_id FK
        bigint vendor_calibration_id FK
        date issue_date
        string status
    }

```

## Ikhtisar Modul

### 📦 Data Master
Mengelola entitas inti seperti **Tools** (Alat), **Locations** (Lokasi), dan **Manufacturers** (Produsen). Item individual dilacak melalui **Tool Items** untuk kontrol yang lebih granular.

### 👤 Personel
Mengelola **Users** (Akses sistem) dan **Employees** (Interaksi fisik dengan alat seperti peminjaman atau penerimaan).

### 🔄 Transaksi
Mencatat siklus hidup aset:
- **Loans (Peminjaman)**: Peminjaman item saat ini dan riwayatnya.
- **Mutations (Mutasi)**: Jejak audit untuk perpindahan internal.
- **Damage/Lost (Rusak/Hilang)**: Manajemen item yang bermasalah atau hilang.
- **Calibration (Kalibrasi)**: Jadwal pemeliharaan eksternal.
