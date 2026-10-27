# 📦 Inventory & Sales Management System (Laravel)
### Enterprise Stock Control & Point of Sale (POS)

Aplikasi berbasis web terpadu untuk pengelolaan inventaris barang, pengadaan stok (*purchase orders*), penjualan kasir (*point of sale*), mutasi stok aman, serta pencatatan riwayat audit (*audit trail*) secara real-time. Dibangun menggunakan framework **Laravel 12** dengan arsitektur **Model-View-Controller (MVC)**, database relasional **MySQL**, proteksi konkurensi stok tingkat lanjut (*pessimistic locking*), serta pembagian hak akses (*Role-Based Access Control*).

---

## ⚡ CARA MENJALANKAN APLIKASI (MANUAL)

Ikuti langkah-langkah mudah di bawah ini untuk menjalankan aplikasi secara manual melalui terminal:

### Langkah 1: Nyalakan MySQL di XAMPP
1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada baris **MySQL** (pastikan menyala hijau di port `3306`).

### Langkah 2: Buka Terminal & Masuk ke Folder Proyek
Buka Terminal (**Command Prompt** atau **PowerShell**), lalu ketik:
```bash
cd C:\xampp\htdocs\inventory-laravel
```

### Langkah 3: Migrasi Database & Seeder Awal *(Opsional)*
Jika database belum terisi atau ingin me-reset data ke 10 produk awal:
```bash
php artisan migrate:fresh --seed
```

### Langkah 4: Jalankan Server Laravel
Ketik perintah berikut untuk menyalakan server lokal:
```bash
php artisan serve
```
Tunggu hingga muncul pesan informasi:
```text
INFO Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```
*(Jangan tutup jendela terminal selama aplikasi digunakan)*

### Langkah 5: Buka di Browser
Buka browser (Google Chrome, Microsoft Edge, atau Firefox), lalu akses alamat:
👉 **`http://127.0.0.1:8000`**

---

### 🌟 Pilihan Praktis: Jalankan 1-Klik Otomatis
Selain cara manual di atas, Anda juga dapat menjalankan aplikasi cukup dengan klik dua kali (*double-click*) file **`run_server.bat`** di dalam folder `C:\xampp\htdocs\inventory-laravel`. Server dan browser akan menyala secara otomatis.

---

### 🔑 Akun Login Pengguna

| Peran (Role) | Username | Password | Keterangan Hak Akses |
|---|---|---|---|
| **Admin / Owner** | `admin` | `admin123` | **Akses Penuh**: Master produk, kategori, supplier, pembelian barang, kasir penjualan, pembatalan transaksi, dan kelola user |
| **Staff / Kasir** | `staff` | `staff123` | **Akses Operasional**: Katalog produk, kasir penjualan (POS), riwayat faktur, dan cetak struk |

---

## 📑 DAFTAR ISI

1. [Cara Menjalankan Aplikasi (Manual)](#-cara-menjalankan-aplikasi-manual)
2. [Akun Login Pengguna](#-akun-login-pengguna)
3. [Fitur Utama & Modul Sistem](#-fitur-utama--modul-sistem)
4. [Arsitektur Sistem & Alur Bisnis](#-arsitektur-sistem--alur-bisnis)
5. [Skema Database & Entity Relationship Diagram (ERD)](#-skema-database--entity-relationship-diagram-erd)
6. [Matriks Peran & Hak Akses Pengguna (RBAC)](#-matriks-peran--hak-akses-pengguna-rbac)
7. [Integritas Data, Concurrency, & Aturan Bisnis Stok](#-integritas-data-concurrency--aturan-bisnis-stok)
8. [Daftar Endpoint Routing & HTTP Methods](#-daftar-endpoint-routing--http-methods)
9. [Laporan Pengujian Otomatis (Feature Test Suite)](#-laporan-pengujian-otomatis-feature-test-suite)
10. [Struktur Folder & Penempatan Kode](#-struktur-folder--penempatan-kode)
11. [Batasan Sistem (Known Limitations)](#-batasan-sistem-known-limitations)

---

## ✨ FITUR UTAMA & MODUL SISTEM

1. **Dashboard Agregasi Metrik & KPI**:
   - Total produk aktif, total kategori, dan total supplier terdaftar.
   - Total omzet penjualan bulan ini dan rincian transaksi hari ini.
   - Total akumulasi transaksi operasional (pembelian dan penjualan).
   - Widget peringatan dini produk dengan stok menipis (*low-stock alert*) di bawah batas minimum.
   - Tabel transaksi penjualan terakhir dan aksi cepat *restock*.

2. **Manajemen Master Data Terintegrasi**:
   - **Produk (Katalog 10 Item Aksesoris & Periferal Laptop)**:
     Sistem telah diinisialisasi dengan tepat 10 produk fokus aksesoris dan periferal laptop:
     
     | No | SKU | Nama Produk | Kategori | Harga Beli | Harga Jual | Stok Awal | Min. Stok |
     |:--:|:---:|:---|:---|:---:|:---:|:---:|:---:|
     | 1 | `KB-001` | Keyboard Mekanikal Gaming RGB | Periferal Komputer | Rp 350.000 | Rp 450.000 | 15 | 5 |
     | 2 | `KB-002` | Keyboard Wireless Slim Portable | Periferal Komputer | Rp 180.000 | Rp 245.000 | 20 | 5 |
     | 3 | `MS-001` | Mouse Wireless Silent Click | Periferal Komputer | Rp 85.000 | Rp 125.000 | 25 | 8 |
     | 4 | `MS-002` | Mouse Gaming RGB Ergonomis | Periferal Komputer | Rp 150.000 | Rp 210.000 | 18 | 5 |
     | 5 | `ACC-001` | Stand Laptop Aluminium Adjustable | Aksesoris Laptop | Rp 120.000 | Rp 175.000 | 12 | 4 |
     | 6 | `ACC-002` | Cooling Pad Laptop RGB Dual Fan | Aksesoris Laptop | Rp 110.000 | Rp 160.000 | 10 | 4 |
     | 7 | `ACC-003` | USB Type-C Hub Multiport 6-in-1 | Aksesoris Laptop | Rp 195.000 | Rp 275.000 | 14 | 5 |
     | 8 | `PROT-001` | Sleeve Case Pelindung Laptop 14 Inch | Pelindung & Proteksi Laptop | Rp 75.000 | Rp 115.000 | 20 | 6 |
     | 9 | `PROT-002` | Anti Gores Layar Laptop 14 Inch (Screen Protector) | Pelindung & Proteksi Laptop | Rp 35.000 | Rp 60.000 | 30 | 10 |
     | 10 | `PROT-003` | Pelindung Keyboard Silikon Universal | Pelindung & Proteksi Laptop | Rp 25.000 | Rp 45.000 | 28 | 10 |

   - **Kategori**: Pengelompokan produk dengan proteksi pencegahan hapus (*foreign key restrict*) jika masih memiliki produk aktif.
   - **Supplier**: Buku kontak pemasok barang dengan nomor telepon, email, dan alamat fisik. Terproteksi dari penghapusan jika memiliki riwayat pengadaan.
   - **Pencarian, Filter, & Sorting**: Pencarian multi-kolom (nama & SKU), filter kategori, filter status stok menipis, serta sorting dinamis berbasis harga, stok, atau nama produk.

3. **Modul Transaksi Pengadaan / Pembelian (Purchase Orders)**:
   - Form pencatatan barang masuk multi-item dinamis via JavaScript.
   - Perhitungan otomatis subtotal dan total biaya pengadaan.
   - Eksekusi transaksi atomik via database transaction.
   - Penambahan kuantitas stok produk secara otomatis dan pencatatan riwayat mutasi stok masuk (`IN`).

4. **Modul Transaksi Kasir & Penjualan (Point of Sale)**:
   - Form kasir multi-item dinamis dengan indikator sisa stok real-time.
   - Penguncian baris data produk secara pesimistik (*pessimistic concurrency locking*) saat transaksi berlangsung guna mencegah *race condition*.
   - Proteksi *Zero-Overselling*: Transaksi otomatis ditolak jika kuantitas melebihi stok gudang yang tersedia.
   - *Server-Side Pricing*: Penetapan harga satuan selalu merujuk pada harga resmi di database server untuk mencegah manipulasi data dari sisi browser.
   - Pengurangan stok secara otomatis dan pencatatan riwayat mutasi stok keluar (`OUT`).

5. **Fitur Pembatalan Transaksi & Reversal Stok Aman**:
   - Pembatalan transaksi pembelian maupun penjualan secara aman oleh **Admin/Owner**.
   - **Reversal Stok Konsisten**: Pembatalan penjualan mengembalikan kuantitas stok bertambah (`increment`), sedangkan pembatalan pembelian menarik kuantitas stok kembali berkurang (`decrement`).
   - Validasi kecukupan stok saat pembatalan pembelian agar tidak menghasilkan stok negatif.
   - Riwayat pembatalan dicatat rapi dalam status `CANCELLED` beserta catatan audit log mutasi stok.

6. **Manajemen Pengguna (User Management)**:
   - Khusus akun Admin untuk menambah staf baru, mengatur peran (*admin* / *staff*), mengubah identitas, dan mereset password akun.

7. **Audit Trail Mutasi Stok (Stock Movements)**:
   - Buku besar pencatatan setiap perubahan fisik stok (pembelian masuk, penjualan kasir, maupun pembatalan transaksi) lengkap dengan penanda waktu, jenis mutasi, referensi nomor transaksi, dan nama pengguna yang mengeksekusi.

---

## 🏛️ ARSITEKTUR SISTEM & ALUR BISNIS

Sistem beroperasi di atas kerangka kerja **Laravel 12** dengan pola arsitektur **Model-View-Controller (MVC)** yang terisolasi secara bersih:

```text
Browser / Client (HTTP Request)
        │
        ▼
   routes/web.php (Routing & URL Mapping)
        │
        ▼
   EnsureRole Middleware ('role:admin', 'role:admin,staff')
        │
        ▼
   Controllers (HTTP Request Handling, Validation, Business Logic)
   ├── ProductController, CategoryController, SupplierController
   ├── PurchaseController (DB::transaction + Stock Increment + Audit Log)
   ├── SaleController (DB::transaction + lockForUpdate + Stock Decrement + Audit Log)
   ├── DashboardController (Aggregated Metrics Query)
   └── UserController (Admin Only Management)
        │
        ▼
   Eloquent Models (Relational Abstraction & Constraints)
   ├── Category, Product, Supplier, User
   ├── Purchase, PurchaseItem, Sale, SaleItem, StockMovement
        │
        ▼
   Database Layer (MySQL / InnoDB Engine with Row-Level Locking)
        │
        ▼
   Blade Presentation Views (Responsive HTML5 UI with Dynamic Line-Item Scripts)
```

---

## 🗄️ SKEMA DATABASE & ENTITY RELATIONSHIP DIAGRAM (ERD)

Database menggunakan storage engine **InnoDB** pada MySQL dengan dukungan integritas relasional *foreign key constraints*:

```
+---------------+        1:N        +---------------+        N:M        +---------------+
|  categories   |<------------------|   products    |------------------>|   suppliers   |
+---------------+                   +---------------+                   +---------------+
| id (PK)       |                   | id (PK)       |                   | id (PK)       |
| name          |                   | category_id FK|                   | name          |
| code          |                   | code (unique) |                   | phone, email  |
+---------------+                   | name          |                   | address       |
                                    | purchase_price|                   +---------------+
                                    | selling_price |                           ▲
                                    | stock         |                           │ 1:N
                                    | minimum_stock |                           │
                                    +---------------+                   +---------------+
                                      ▲     ▲    ▲                      |   purchases   |
                                      │     │    │                      +---------------+
                                1:N   │     │    │ 1:N                  | id (PK)       |
       +------------------------------+     │    +--------------+       | purchase_number|
       │                                    │                   │       | supplier_id FK|
       ▼                                    │                   ▼       | user_id FK    |
+---------------+                           │            +---------------+ total_amount |
| purchase_items|                           │            |  sale_items   | status        |
+---------------+                           │            +---------------+ date          |
| id (PK)       |                           │            | id (PK)       +---------------+
| purchase_id FK|                           │            | sale_id FK    |
| product_id FK |                           │            | product_id FK |
| quantity      |                           │            | quantity      |
| unit_price    |                           │            | unit_price    |
| subtotal      |                           │            | subtotal      |
+---------------+                           │            +---------------+
                                            ▼                   │ 1:N
                                    +---------------+           ▼
                                    |stock_movements|    +---------------+
                                    +---------------+    |     sales     |
                                    | id (PK)       |    +---------------+
                                    | product_id FK |    | id (PK)       |
                                    | user_id FK    |    | invoice_number|
                                    | type (IN/OUT) |    | customer_name |
                                    | quantity      |    | user_id FK    |
                                    | reference_type|    | total_amount  |
                                    | reference_id  |    | status        |
                                    | remarks       |    | date          |
                                    +---------------+    +---------------+
```

---

## 👥 MATRIKS PERAN & HAK AKSES PENGGUNA (RBAC)

Sistem menerapkan prinsip pemisahan tugas (*separation of duties*) antara pemilik bisnis/manajer (**Admin/Owner**) dan staf operasional (**Staff/Kasir**):

| Modul / Tindakan Sistem | Admin / Owner | Staff / Kasir | Mekanisme Proteksi |
|---|:---:|:---:|---|
| **Login & Logout Sesi** | ✅ Ya | ✅ Ya | Sesi aman via username/email |
| **Akses Dashboard Metrik** | ✅ Ya | ✅ Ya | Tampilan ringkasan statistik KPI |
| **Katalog Produk (Lihat, Cari, Filter)** | ✅ Ya | ✅ Ya | Query builder teroptimasi |
| **Tambah / Edit Master Produk** | ✅ Ya | ❌ Tidak | Middleware `role:admin` |
| **Hapus Master Produk** | ✅ Ya | ❌ Tidak | Proteksi foreign key restrict |
| **Kelola Kategori & Supplier** | ✅ Ya | ❌ Tidak | Middleware `role:admin` |
| **Buat Transaksi Pengadaan (PO)** | ✅ Ya | ❌ Tidak | Khusus pengambil keputusan pengadaan |
| **Lihat Riwayat Pembelian** | ✅ Ya | ✅ Ya | Transparansi operasional staf |
| **Kasir & Transaksi Penjualan** | ✅ Ya | ✅ Ya | Point of Sale multi-item |
| **Lihat Riwayat Invoice Penjualan** | ✅ Ya | ✅ Ya | Akses faktur & rincian penjualan |
| **Batalkan Transaksi (Cancel & Reversal)** | ✅ Ya | ❌ Tidak | Khusus Admin / Otorisasi Khusus |
| **Manajemen Pengguna (User Management)** | ✅ Ya | ❌ Tidak | Middleware `role:admin` |
| **Audit Trail Mutasi Stok** | ✅ Ya | ✅ Ya | Pemantauan riwayat log barang |

---

## 🔒 INTEGRITAS DATA, CONCURRENCY, & ATURAN BISNIS STOK

1. **Atomisitas Transaksi (`DB::transaction`)**:
   - Seluruh mutasi transaksi (pembuatan invoice header, penulisan rincian item, pergeseran stok pada tabel produk, dan pencatatan buku audit mutasi) dieksekusi dalam satu kesatuan atomik.
   - Jika terdapat kegagalan pada baris tertentu, seluruh perubahan dibatalkan seketika (*rollback*) tanpa meninggalkan anomali data.

2. **Pessimistic Concurrency Locking (`lockForUpdate()`)**:
   - Ketika kasir memproses pesanan penjualan, baris data produk dikunci di level database menggunakan `Product::where('id', ...)->lockForUpdate()->first()`.
   - Mengeliminasi *race condition* atau *phantom read* ketika dua kasir menjual produk yang sama dalam hitungan milidetik yang berbarengan.

3. **Perlindungan Terhadap Overselling (Zero-Overselling)**:
   - Validasi ketat di sisi server memastikan stok mencukupi sebelum memotong kuantitas.
   - Jika stok tidak memadai, sistem langsung membatalkan proses dengan pesan ramah: `Stok produk [Nama Produk] tidak mencukupi (Tersedia: X, Diminta: Y).`

4. **Integritas Harga Sisi Server (Server-Side Pricing)**:
   - Harga jual satuan produk tidak mempercayai input form client/browser.
   - Sistem selalu mengambil nilai `selling_price` secara otoritatif langsung dari basis data server guna mencegah kecurangan modifikasi elemen inspect.

5. **Reversal Stok Konsisten Saat Pembatalan Transaksi**:
   - Transaksi dengan status `completed` dapat dibatalkan menjadi `cancelled` oleh Admin.
   - **Pembatalan Penjualan**: Menambah kembali kuantitas stok barang dan mencatat mutasi `IN` (Reversal).
   - **Pembatalan Pembelian**: Menarik kembali kuantitas stok barang dan mencatat mutasi `OUT` (Reversal), dengan proteksi pencegahan jika sisa stok saat ini lebih kecil dari kuantitas yang ditarik.

---

## 🌐 DAFTAR ENDPOINT ROUTING & HTTP METHODS

Semua rute terdaftar rapi pada file [`routes/web.php`](file:///C:/xampp/htdocs/inventory-laravel/routes/web.php):

| Method | URI | Controller Action | Middleware | Deskripsi |
|---|---|---|---|---|
| `GET` | `/login` | `AuthController@showLoginForm` | `guest` | Form login pengguna |
| `POST` | `/login` | `AuthController@login` | `guest` | Proses autentikasi akun |
| `POST` | `/logout` | `AuthController@logout` | `auth` | Keluar dari sistem |
| `GET` | `/` | `DashboardController@index` | `auth` | Halaman beranda |
| `GET` | `/dashboard` | `DashboardController@index` | `auth` | Dashboard agregasi metrik |
| `GET` | `/products` | `ProductController@index` | `auth` | Katalog produk + filter + sort |
| `GET` | `/products/create` | `ProductController@create` | `auth, role:admin` | Form tambah produk baru |
| `POST` | `/products` | `ProductController@store` | `auth, role:admin` | Simpan master produk |
| `GET` | `/products/{id}` | `ProductController@show` | `auth` | Rincian detail produk |
| `GET` | `/products/{id}/edit` | `ProductController@edit` | `auth, role:admin` | Form edit produk |
| `PUT` | `/products/{id}` | `ProductController@update` | `auth, role:admin` | Simpan perubahan produk |
| `DELETE` | `/products/{id}` | `ProductController@destroy` | `auth, role:admin` | Hapus produk (terproteksi) |
| `GET` | `/purchases` | `PurchaseController@index` | `auth` | Daftar riwayat pembelian (PO) |
| `GET` | `/purchases/create` | `PurchaseController@create` | `auth, role:admin` | Form transaksi pembelian baru |
| `POST` | `/purchases` | `PurchaseController@store` | `auth, role:admin` | Simpan transaksi pembelian |
| `GET` | `/purchases/{id}` | `PurchaseController@show` | `auth` | Faktur rincian pembelian |
| `POST` | `/purchases/{id}/cancel` | `PurchaseController@cancel` | `auth, role:admin` | Batalkan pembelian & reversal |
| `GET` | `/sales` | `SaleController@index` | `auth` | Daftar riwayat transaksi penjualan |
| `GET` | `/sales/create` | `SaleController@create` | `auth` | Form kasir penjualan baru |
| `POST` | `/sales` | `SaleController@store` | `auth` | Simpan transaksi penjualan aman |
| `GET` | `/sales/{id}` | `SaleController@show` | `auth` | Nota invoice penjualan |
| `GET` | `/sales/{id}/receipt` | `SaleController@receipt` | `auth` | Struk penjualan siap cetak (thermal POS) |
| `POST` | `/sales/{id}/cancel` | `SaleController@cancel` | `auth, role:admin` | Batalkan penjualan & reversal |
| `GET` | `/categories` | `CategoryController@index` | `auth, role:admin` | Kelola master kategori |
| `POST` | `/categories` | `CategoryController@store` | `auth, role:admin` | Simpan kategori baru |
| `GET` | `/categories/{id}/edit` | `CategoryController@edit` | `auth, role:admin` | Form edit kategori |
| `PUT` | `/categories/{id}` | `CategoryController@update` | `auth, role:admin` | Simpan perubahan kategori |
| `DELETE` | `/categories/{id}` | `CategoryController@destroy` | `auth, role:admin` | Hapus kategori (terproteksi) |
| `GET` | `/suppliers` | `SupplierController@index` | `auth, role:admin` | Kelola master supplier |
| `POST` | `/suppliers` | `SupplierController@store` | `auth, role:admin` | Simpan supplier baru |
| `GET` | `/suppliers/{id}/edit` | `SupplierController@edit` | `auth, role:admin` | Form edit supplier |
| `PUT` | `/suppliers/{id}` | `SupplierController@update` | `auth, role:admin` | Simpan perubahan supplier |
| `DELETE` | `/suppliers/{id}` | `SupplierController@destroy` | `auth, role:admin` | Hapus supplier (terproteksi) |
| `GET` | `/users` | `UserController@index` | `auth, role:admin` | Kelola akun pengguna |
| `POST` | `/users` | `UserController@store` | `auth, role:admin` | Simpan akun user baru |
| `PUT` | `/users/{id}` | `UserController@update` | `auth, role:admin` | Perbarui data / role user |
| `DELETE` | `/users/{id}` | `UserController@destroy` | `auth, role:admin` | Hapus akun user |
| `GET` | `/stock/history` | `StockMovementController@history` | `auth` | Riwayat audit log mutasi stok |

---

## 🧪 LAPORAN PENGUJIAN OTOMATIS (FEATURE TEST SUITE)

Seluruh proses fungsionalitas dan integritas transaksi telah diuji secara komprehensif melalui suite uji otomatis (`tests/Feature/InventorySystemTest.php`):

```text
 PASS  Tests\Feature\InventorySystemTest
 ✓ 01 admin and staff can authenticate successfully
 ✓ 02 staff cannot access admin user management
 ✓ 03 admin can create product with purchase and selling price
 ✓ 04 product search and filter work correctly
 ✓ 05 purchase transaction creates items, increments stock, and logs movement
 ✓ 06 sale transaction decrements stock with concurrency lock and creates movement
 ✓ 07 sale transaction fails if requested quantity exceeds stock
 ✓ 08 admin can cancel sale and stock is reversed
 ✓ 09 staff cannot cancel transactions
 ✓ 10 dashboard displays correct aggregated metrics
 ✓ 11 deleting category with products is restricted

  Tests:    11 passed (38 assertions)
  Duration: 1.08s
  Status:   100% SUCCESS
```

Untuk menjalankan kembali pengujian kapan pun:
```powershell
php artisan test
```

---

## 📁 STRUKTUR FOLDER & PENEMPATAN KODE

```text
inventory-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php             # Login, logout, session auth
│   │   │   ├── CategoryController.php         # Master kategori + delete restrict
│   │   │   ├── DashboardController.php        # Metrik agregasi KPI & low stock
│   │   │   ├── ProductController.php          # CRUD produk, search, filter, sort
│   │   │   ├── PurchaseController.php         # Multi-item PO & cancel reversal
│   │   │   ├── SaleController.php             # Multi-item sales, concurrency lock, cancel
│   │   │   ├── StockMovementController.php    # Audit trail mutasi stok
│   │   │   ├── SupplierController.php         # Master supplier + delete restrict
│   │   │   └── UserController.php             # User management (Admin only)
│   │   └── Middleware/
│   │       └── EnsureRole.php                 # Role authorization guard
│   ├── Models/
│   │   ├── Category.php                       # Model kategori (hasMany Product)
│   │   ├── Product.php                        # Model produk (purchase & selling price)
│   │   ├── Purchase.php                       # Model pengadaan header
│   │   ├── PurchaseItem.php                   # Model rincian item pengadaan
│   │   ├── Sale.php                           # Model penjualan kasir header
│   │   ├── SaleItem.php                       # Model rincian item kasir
│   │   ├── StockMovement.php                  # Model audit log pergerakan stok
│   │   ├── Supplier.php                       # Model rekanan pemasok
│   │   └── User.php                           # Model pengguna & role helper
│   └── Providers/
│       └── AppServiceProvider.php             # Registrasi authorization gates
├── bootstrap/
│   └── app.php                                # Pendaftaran alias middleware 'role'
├── database/
│   ├── migrations/                            # 10 skema migrasi tabel terstruktur
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_10_11_000001_create_categories_table.php
│   │   ├── 2026_10_11_000002_create_products_table.php
│   │   ├── 2026_10_11_000003_create_suppliers_table.php
│   │   ├── 2026_10_11_000004_create_product_suppliers_table.php
│   │   ├── 2026_10_11_000005_create_stock_movements_table.php
│   │   ├── 2026_10_11_000006_create_purchases_table.php
│   │   ├── 2026_10_11_000007_create_purchase_items_table.php
│   │   ├── 2026_10_11_000008_create_sales_table.php
│   │   └── 2026_10_11_000009_create_sale_items_table.php
│   └── seeders/
│       └── DatabaseSeeder.php                 # Seeder akun awal & data transaksi
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php                # Tampilan login modern
│       ├── categories/                        # Views CRUD kategori
│       ├── components/
│       │   └── alert.blade.php                # Reusable flash alert component
│       ├── dashboard.blade.php                # Dashboard agregasi & low stock
│       ├── layouts/
│       │   ├── app.blade.php                  # Master layout
│       │   └── navigation.blade.php           # Menu navigasi berbasis role
│       ├── products/                          # Views CRUD & katalog produk
│       ├── purchases/                         # Views transaksi pembelian multi-item
│       ├── sales/                             # Views transaksi kasir penjualan
│       ├── stock/
│       │   └── history.blade.php              # Views audit trail pergerakan stok
│       ├── suppliers/                         # Views CRUD supplier
│       └── users/
│           └── index.blade.php                # Views manajemen pengguna (Admin)
├── routes/
│   └── web.php                                # Definisi endpoint routing aplikasi
├── tests/
│   └── Feature/
│       └── InventorySystemTest.php            # Test suite 11 skenario pengujian
├── .env.example                               # Template konfigurasi environment (bebas dari data rahasia)
├── .env                                       # Konfigurasi lokal (otomatis diproteksi .gitignore, tidak di-push)
├── run_server.bat                             # Skrip otomatis 1-klik di Windows
└── README.md                                  # Dokumentasi komprehensif sistem
```

---

## ⚠️ BATASAN SISTEM (KNOWN LIMITATIONS)

Ruang lingkup dan batasan fitur sistem ini mencakup:

1. **Simulasi Transaksi Kasir Lokal**: Tidak menggunakan *Payment Gateway* perbankan pihak ketiga (seperti Midtrans/Xendit nyata). Transaksi kasir diselesaikan secara langsung di toko dengan verifikasi server, auto-potong stok real-time, dan pencetakan struk kasir thermal POS 80mm siap cetak (`window.print()`).
2. **Mata Uang Tunggal (Single Currency)**: Seluruh pencatatan harga dan transaksi beroperasi menggunakan mata uang Rupiah Indonesia (IDR / Rp) tanpa konversi valuta asing (*multi-currency*).
3. **Satu Gudang Utama (Single Warehouse)**: Pengelolaan stok barang berfokus pada satu lokasi operasional toko/gudang utama tanpa fitur mutasi antar-cabang (*inter-warehouse transfer*).
4. **Fokus Inti Konsistensi & Integritas**: Nilai utama sistem terletak pada **konsistensi stok**, pencegahan penjualan fiktif/minus (*zero-overselling*), transaksi atomik (`DB::transaction`), penguncian baris konkurensi (`Product::lockForUpdate()`), dan pembagian hak akses berlapis (*Role-Based Access Control*).
