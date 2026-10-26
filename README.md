# 📦 Inventory & Sales Management System (Laravel)
### Enterprise Stock Control & Point of Sale (POS)

Aplikasi berbasis web terpadu untuk pengelolaan inventaris barang, pengadaan stok (*purchase orders*), penjualan kasir (*point of sale*), mutasi stok aman, serta pencatatan riwayat audit (*audit trail*) secara real-time. Dibangun menggunakan framework **Laravel 12** dengan arsitektur **Model-View-Controller (MVC)**, database relasional **MySQL**, proteksi konkurensi stok tingkat lanjut (*pessimistic locking*), serta pembagian hak akses (*Role-Based Access Control*).

---

## ⚡ PANDUAN CARA MENJALANKAN APLIKASI SECARA MANUAL & OTOMATIS

Untuk menjalankan aplikasi ini di laptop atau komputer lokal (Windows), terutama saat demonstrasi atau ujian di depan dosen, tersedia **Panduan Manual Terperinci** serta skrip otomatis:

---

### 💻 METODE UTAMA: PANDUAN CARA MENJALANKAN SECARA MANUAL

Ikuti langkah-langkah di bawah ini jika Anda diminta oleh dosen untuk menjalankan aplikasi secara langsung melalui terminal (**PowerShell** atau **Command Prompt / CMD**):

#### Langkah 1: Pastikan Database MySQL XAMPP Berjalan
1. Buka aplikasi **XAMPP Control Panel**.
2. Pada baris **MySQL**, klik tombol **Start**.
3. Pastikan indikator modul MySQL berubah menjadi warna hijau dan menampilkan port `3306`.

#### Langkah 2: Buka Terminal (PowerShell atau CMD)
Tekan tombol `Windows + R`, ketik `cmd` atau `powershell`, lalu tekan `Enter`.

#### Langkah 3: Masuk ke Folder Proyek di `htdocs`
Ketik perintah perpindahan direktori:
```cmd
cd C:\xampp\htdocs\inventory-laravel
```

#### Langkah 4: Konfigurasi Environment Path PHP *(Opsional)*
Jika ekstensi PHP MySQL (`pdo_mysql` / `openssl`) belum aktif secara global di terminal Anda:

- **Jika Menggunakan PowerShell:**
  ```powershell
  $env:PHPRC = "$env:USERPROFILE\php"
  ```
- **Jika Menggunakan Command Prompt (CMD):**
  ```cmd
  set PHPRC=%USERPROFILE%\php
  ```
*(Atau jika menggunakan PHP bawaan XAMPP, arahkan ke `C:\xampp\php`)*

#### Langkah 5: Migrasi Database & Seeder Awal *(Cukup sekali saat awal setup)*
Jika database belum terisi atau ingin me-reset ke kondisi awal:
```bash
php artisan migrate:fresh --seed
```

#### Langkah 6: Jalankan Server Pengembangan Laravel
Ketik perintah berikut untuk menyalakan server lokal:
```bash
php artisan serve
```
Tunggu hingga muncul pesan informasi:
```text
INFO Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

#### Langkah 7: Buka Aplikasi di Browser
Buka browser (Google Chrome, Microsoft Edge, atau Firefox), lalu akses alamat:
👉 **`http://127.0.0.1:8000`**

> [!IMPORTANT]
> **PENTING:** Jangan menutup jendela terminal/CMD saat sedang menggunakan aplikasi. Jendela tersebut adalah mesin server web yang sedang aktif melayani request Anda.

---

### 🌐 METODE ALTERNATIF: MENJALANKAN VIA APACHE XAMPP
Karena proyek ini sudah berada di dalam folder `htdocs`, Anda juga dapat membukanya secara langsung melalui web server Apache bawaan XAMPP tanpa perlu menyalakan `php artisan serve`:
1. Buka **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **Apache** dan **MySQL** (keduanya harus berwarna hijau).
3. Buka browser dan ketik alamat:
   👉 **`http://localhost/inventory-laravel/public`**

---

### 🌟 METODE CEPAT: SKRIP OTOMATIS 1-KLIK (`run_server.bat`)
Jika ingin cara yang paling praktis dan cepat:
1. Buka folder `C:\xampp\htdocs\inventory-laravel`.
2. Klik ganda (*double-click*) pada file: **`run_server.bat`**.
3. Skrip ini secara otomatis akan:
   - Memeriksa dan menyalakan MySQL XAMPP jika belum aktif.
   - Mengonfigurasi path environment PHP.
   - Membuka browser ke `http://127.0.0.1:8000`.
   - Menjalankan server Laravel development.

---

### 🔑 Kredensial Akun Percobaan (Demo & Penilaian Dosen)

> [!NOTE]
> **PEMBERITAHUAN KEAMANAN & PRIVASI:**
> Kredensial di bawah ini adalah **data tiruan (*dummy seeder*)** yang disediakan khusus untuk simulasi hak akses (*Role-Based Access Control*), pengujian lokal (*local development*), dan kemudahan penilaian saat ujian/demonstrasi dosen. Data ini **BUKAN** kata sandi atau akun pribadi pemilik sistem. Seluruh konfigurasi rahasia (*environment secrets*) pada file `.env` telah diamankan secara ketat oleh `.gitignore` dan tidak diunggah ke publik repository.

| Peran (Role) | Username Demo | Password Demo | Cakupan Hak Akses |
|---|---|---|---|
| **Admin / Owner** | `admin` | `admin123` | **Akses Penuh**: Kelola master produk (tambah, edit, hapus), kategori, supplier, transaksi pembelian barang masuk (PO), kasir penjualan, pembatalan transaksi (*cancel* & *reversal* stok), manajemen pengguna (*user management*), dan audit log mutasi stok. |
| **Staff / Kasir** | `staff` | `staff123` | **Akses Operasional**: Melihat katalog produk, transaksi kasir penjualan (POS), melihat riwayat faktur transaksi, dan melihat riwayat audit stok. *(Dibatasi: Tidak dapat menambah/mengedit master produk, dilarang membatalkan transaksi, dan tidak dapat mengelola akun pengguna)*. |

---

## 📑 DAFTAR ISI

1. [Panduan Cara Menjalankan Aplikasi Secara Manual & Otomatis](#-panduan-cara-menjalankan-aplikasi-secara-manual--otomatis)
2. [Kredensial Akun Login Default](#-kredensial-akun-login-default)
3. [Fitur Utama & Modul Sistem](#-fitur-utama--modul-sistem)
4. [Arsitektur Sistem & Alur Bisnis](#-arsitektur-sistem--alur-bisnis)
5. [Skema Database & Entity Relationship Diagram (ERD)](#-skema-database--entity-relationship-diagram-erd)
6. [Matriks Peran & Hak Akses Pengguna (RBAC)](#-matriks-peran--hak-akses-pengguna-rbac)
7. [Integritas Data, Concurrency, & Aturan Bisnis Stok](#-integritas-data-concurrency--aturan-bisnis-stok)
8. [Daftar Endpoint Routing & HTTP Methods](#-daftar-endpoint-routing--http-methods)
9. [Laporan Pengujian Otomatis (Feature Test Suite)](#-laporan-pengujian-otomatis-feature-test-suite)
10. [Struktur Folder & Penempatan Kode](#-struktur-folder--penempatan-kode)

---

## ✨ FITUR UTAMA & MODUL SISTEM

1. **Dashboard Agregasi Metrik & KPI**:
   - Total produk aktif, total kategori, dan total supplier terdaftar.
   - Total omzet penjualan bulan ini dan rincian transaksi hari ini.
   - Total akumulasi transaksi operasional (pembelian dan penjualan).
   - Widget peringatan dini produk dengan stok menipis (*low-stock alert*) di bawah batas minimum.
   - Tabel transaksi penjualan terakhir dan aksi cepat *restock*.

2. **Manajemen Master Data Terintegrasi**:
   - **Produk**: Dilengkapi kode SKU unik, nama, kategori, harga beli modal (HPP / *purchase price*), harga jual konsumen (*selling price*), stok aktual, dan ambang batas stok minimum.
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
