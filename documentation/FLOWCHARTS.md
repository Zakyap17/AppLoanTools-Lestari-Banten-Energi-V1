# Sistem Manajemen Gudang - Flowchart Detail (V2)

Dokumen ini menyediakan alur kerja teknis dan operasional yang mendalam untuk sistem **WarehouseLoan_lBE**.

## 1. Alur Teknis Peminjaman Alat (Loan)
Menjelaskan proses dari input pengguna hingga operasi database dan antrean email.

```mermaid
graph TD
    A[Mulai: Input Peminjaman Baru] --> B[Input: Peminjam, Penanggung Jawab, Tgl Pinjam, Tgl Kembali]
    B --> C[Cari & Pilih Alat - AJAX Search]
    C --> D[Pilih Item Serial Spesifik - Tersaring 'Available']
    D --> E[Klik 'Simpan Transaksi']
    
    subgraph Validasi & Backend
    E --> F{Validasi Form?}
    F -- Gagal --> G[Kembali ke Form + Pesan Error]
    F -- Berhasil --> H[Mulai: DB Transaction]
    H --> I{Cek Status Real-time: <br/>Apakah semua Item masih 'Available'?}
    I -- Ada yang Tidak --> J[Rollback: Tampilkan Pesan Bentrok Stok]
    I -- Ya, Semua OK --> K[Bulk Insert: Tabel 'loans']
    K --> L[Update Status: Tabel 'tool_items' <br/>Set 'status' = 'in_use']
    L --> M[Update Stok: Tabel 'tools' <br/>Decrement 'quantity']
    M --> N{Stok Alat = 0?}
    N -- Ya --> O[Update Status: Tabel 'tools' <br/>Set 'status' = 'in_use']
    N -- Tidak --> P[Commit: DB Transaction]
    O --> P
    end
    
    P --> Q[Sistem: Antrekan Email Notifikasi - Queued Mail]
    Q --> R[Kirim Email ke Peminjam & BCC Management]
    R --> S[Selesai: Redirect ke Index dengan Pesan Sukses]
```

## 2. Alur Pengembalian & Penanganan Masalah (Return & Damage)
Menjelaskan logika percabangan saat alat dikembalikan oleh peminjam.

```mermaid
graph TD
    A[Mulai: Pilih Data Pinjaman Aktif 'Ongoing'] --> B[Klik Tombol 'Proses Kembali']
    B --> C{Pilih Kondisi <br/>Pengembalian?}
    
    subgraph Kondisi Bagus
    C -- "Bagus" --> D[Masukan Jumlah Kembali]
    D --> E[Mulai: DB Transaction]
    E --> F[Update: Tabel 'loans' <br/>Set status='returned', Tgl Kembali Aktual]
    F --> G[Restore Stok: Tabel 'tools' <br/>Increment 'quantity']
    G --> H[Update Item: Tabel 'tool_items' <br/>Set status='available', condition='Good']
    end
    
    subgraph Kondisi Bermasalah
    C -- "Rusak / Hilang" --> I[Masukan Alasan & Bukti]
    I --> J[Mulai: DB Transaction]
    J --> K[Generasi Kode Laporan: <br/>TYPE-YYYYMMDD-SEQ]
    K --> L[Insert: Tabel 'damage_losts' <br/>Status='open']
    L --> M[Update: Tabel 'loans' <br/>Status='returned_damage' / 'returned_lost']
    M --> N[Update Item: Tabel 'tool_items' <br/>Set status='damage' / 'lost', condition='Non-Good']
    end
    
    H & N --> O[Commit: DB Transaction]
    O --> P[Selesai: Update Dashboard & Notifikasi]
```

## 3. Alur Perbaikan Alat (Damage Resolution)
Proses memulihkan aset yang rusak kembali ke status siap pakai.

```mermaid
graph TD
    A[Mulai: Daftar Laporan Kerusakan 'Open'] --> B[Pilih Item & Klik 'Resolusi']
    B --> C{Dapat Diperbaiki?}
    C -- Tidak --> D[Update Laporan: Status='unrepairable']
    D --> E[Item Tetap di Status 'Damage' atau Disposal]
    
    C -- Ya --> F[Proses Perbaikan Selesai]
    F --> G[Unggah Bukti Selesai Perbaikan]
    G --> H[Mulai: DB Transaction]
    H --> I[Update Laporan: Status='resolved']
    I --> J[Restore Stok: Tabel 'tools' <br/>Increment 'quantity']
    J --> K[Update Item: Tabel 'tool_items' <br/>Set status='available', condition='Good']
    K --> L[Commit: DB Transaction]
    L --> M[Selesai: Alat Siap Dipinjam Kembali]
```

## Ringkasan Detail Teknis

| Proses | Validasi Kunci | Perubahan Database Utama |
| :--- | :--- | :--- |
| **Peminjaman** | Cek duplikasi status item (Concurrency). | `loans` (Ins), `tool_items` (Upd), `tools` (Upd). |
| **Pengembalian** | Validasi jumlah kembali <= jumlah sisa pinjam. | `loans` (Upd), `tools` (Inc), `tool_items` (Upd). |
| **Kerusakan** | Wajib melampirkan alasan dokumen referensi. | `damage_losts` (Ins), `tool_items` (Upd status). |
| **Kalibrasi** | Cek vendor aktif. | `calibration_issuings` (Ins), `tools` (Upd status). |
