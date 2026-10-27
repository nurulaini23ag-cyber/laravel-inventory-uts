# 📦 Inventory & Sales Management System (Laravel)
### Enterprise Stock Control, Point of Sale (POS) & Customer Ordering System

Aplikasi berbasis web terpadu untuk pengelolaan inventaris barang, pengadaan stok (*purchase orders*), penjualan kasir (*point of sale*), pemesanan mandiri oleh pelanggan (*customer order*), mutasi stok aman, serta pencatatan riwayat audit (*audit trail*) secara real-time. Dibangun menggunakan framework **Laravel 12** dengan arsitektur **Model-View-Controller (MVC)**, database relasional **MySQL**, proteksi konkurensi stok tingkat lanjut (*pessimistic locking*), serta pembagian hak akses berlapis (*Role-Based Access Control*).

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
Jika database belum terisi atau ingin me-reset data ke 10 produk awal dan akun pengguna:
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

Sistem menyediakan 3 akun dengan tingkatan hak akses yang terisolasi secara ketat:

| Peran (Role) | Username | Password | Keterangan Hak Akses |
|---|---|---|---|
| **Admin / Owner** | `admin` | `admin123` | **Akses Penuh**: Master produk, kategori, supplier, pembelian barang, kasir penjualan, konfirmasi pesanan, pembatalan transaksi, dan kelola user |
| **Staff / Kasir** | `staff` | `staff123` | **Akses Operasional Kasir**: Kasir penjualan (POS), konfirmasi & tolak pesanan pelanggan, riwayat faktur, dan cetak struk |
| **User / Pelanggan** | `user` | `user123` | **Akses Khusus Pemesanan**: Melihat katalog produk, membuat pesanan belanja mandiri, memantau status konfirmasi pesanan sendiri. **Seluruh data privat toko (omset, KPI, harga beli/modal, pembelian supplier, data user lain) 100% diproteksi dan tidak dapat diakses.** |

---

## 📑 DAFTAR ISI

1. [Cara Menjalankan Aplikasi (Manual)](#-cara-menjalankan-aplikasi-manual)
2. [Akun Login Pengguna](#-akun-login-pengguna)
3. [Alur Pemesanan Pelanggan & Konfirmasi Kasir](#-alur-pemesanan-pelanggan--konfirmasi-kasir)
4. [Fitur Utama & Modul Sistem](#-fitur-utama--modul-sistem)
5. [Arsitektur Sistem & Alur Bisnis](#-arsitektur-sistem--alur-bisnis)
6. [Skema Database & Entity Relationship Diagram (ERD)](#-skema-database--entity-relationship-diagram-erd)
7. [Matriks Peran & Hak Akses Pengguna (RBAC)](#-matriks-peran--hak-akses-pengguna-rbac)
8. [Integritas Data, Concurrency, & Aturan Bisnis Stok](#-integritas-data-concurrency--aturan-bisnis-stok)
9. [Daftar Endpoint Routing & HTTP Methods](#-daftar-endpoint-routing--http-methods)
10. [Laporan Pengujian Otomatis (Feature Test Suite)](#-laporan-pengujian-otomatis-feature-test-suite)
11. [Struktur Folder & Penempatan Kode](#-struktur-folder--penempatan-kode)
12. [Batasan Sistem (Known Limitations)](#-batasan-sistem-known-limitations)

---

## 🛍️ ALUR PEMESANAN PELANGGAN & KONFIRMASI KASIR

Untuk memberikan pengalaman berbelanja yang aman dan tertib tanpa mengorbankan privasi data toko, alur kerja pemesanan dirancang sebagai berikut:

```text
[ PELANGGAN (User) ]                                  [ KASIR / STAFF / ADMIN ]
       │                                                         │
       ▼                                                         │
1. Login sebagai 'user'                                          │
   (Langsung diarahkan ke Katalog Produk)                        │
       │                                                         │
       ▼                                                         │
2. Pilih Produk & Kuantitas                                      │
   (Tombol "Pesan Sekarang" atau menu "Pesan Barang")            │
       │                                                         │
       ▼                                                         │
3. Kirim Pesanan                                                 │
   • Status Transaksi: PENDING (⏳ Menunggu Konfirmasi)          │
   • STOK BELUM BERKURANG (Mencegah hold stok tak terverifikasi) │
       │                                                         │
       │                                                         ▼
       │                                          4. Kasir Menerima Notifikasi Pesanan
       │                                             • Muncul badge pesanan pending di menu Penjualan
       │                                             • Kasir memeriksa ketersediaan fisik barang
       │                                                         │
       │                                         ┌───────────────┴───────────────┐
       │                                         ▼                               ▼
       │                                 [ OPSI A: KONFIRMASI ]           [ OPSI B: TOLAK ]
       │                                 • Klik "Konfirmasi Pesanan"      • Klik "Tolak Pesanan"
       │                                 • Transaksi DB Atomik            • Berikan alasan tolak
       │                                 • Stok otomatis berkurang        • Status: CANCELLED
       │                                 • Status: COMPLETED              • Stok utuh
       │                                 • Cetak struk POS resmi                 │
       │                                         │                               │
       ▼                                         ▼                               ▼
5. Pelanggan Memeriksa Riwayat Pesanan Saya
   • Melihat pembaruan status: "✅ Dikonfirmasi / Selesai" atau "❌ Ditolak"
   • Nota rincian pesanan dapat dilihat secara transparan
```

---

## ✨ FITUR UTAMA & MODUL SISTEM

1. **Dashboard Agregasi Metrik & KPI (Khusus Admin & Staff)**:
   - Total produk aktif, total kategori, dan total supplier terdaftar.
   - Total omzet penjualan bulan ini dan rincian transaksi hari ini.
   - Total akumulasi transaksi operasional (pembelian dan penjualan).
   - Widget peringatan dini produk dengan stok menipis (*low-stock alert*) di bawah batas minimum.
   - Tabel transaksi penjualan terakhir dan aksi cepat *restock*.
   - *Proteksi Privasi*: Pengguna dengan peran `user` (pelanggan) otomatis diblokir/dialihkan dari dashboard sehingga data finansial toko tetap rahasia.

2. **Katalog & Pemesanan Mandiri Pelanggan (Customer Portal)**:
   - **Katalog Ramah Pelanggan**: Menampilkan nama produk, foto representatif, kategori, harga jual resmi, ketersediaan stok, dan tombol cepat "Pesan Sekarang".
   - **Form Pemesanan Multi-Item**: Pelanggan dapat memesan beberapa produk sekaligus dengan perhitungan subtotal otomatis.
   - **Riwayat Pesanan Saya**: Pelanggan dapat memantau status pesanan (`Pending`, `Completed`, `Cancelled`) beserta nomor pesanan dan rincian biaya.
   - **Isolasi Privasi Antar-Pelanggan**: Pelanggan dilarang keras membuka data pesanan milik pelanggan lain (HTTP 403 Forbidden).

3. **Manajemen Master Data Terintegrasi (Katalog 10 Produk Laptop)**:
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

4. **Modul Transaksi Kasir & Point of Sale (POS)**:
   - Form kasir multi-item dinamis dengan pencatatan nama pelanggan (pelanggan umum / walk-in / nama akun).
   - Indikator badge merah untuk pesanan pelanggan yang butuh konfirmasi segera.
   - Penguncian baris data produk secara pesimistik (*pessimistic locking*) saat transaksi atau konfirmasi pesanan berlangsung.
   - Struk siap cetak (POS Thermal Receipt 80mm) yang mencantumkan nama pelanggan dan kasir penanggung jawab.

5. **Modul Transaksi Pengadaan / Pembelian (Purchase Orders)**:
   - Form pencatatan barang masuk dari rekanan supplier resmi.
   - Penambahan stok otomatis dan pencatatan riwayat audit log mutasi stok masuk (`IN`).

6. **Fitur Pembatalan Transaksi & Reversal Stok Aman**:
   - Pembatalan transaksi penjualan maupun pembelian oleh **Admin**.
   - Reversal stok otomatis dengan konsistensi penuh dan validasi batas aman.

7. **Audit Trail Mutasi Stok (Stock Movements)**:
   - Rekam jejak seluruh pergerakan barang (pembelian masuk, penjualan kasir, konfirmasi pesanan pelanggan, dan pembatalan) lengkap dengan timestamp dan nama penanggung jawab.

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
   ├── AuthController (Login, Logout, Role-based Redirection)
   ├── OrderController (Customer Catalog, Order Placement, Order History)
   ├── ProductController, CategoryController, SupplierController
   ├── PurchaseController (DB::transaction + Stock Increment + Audit Log)
   ├── SaleController (POS Sales, Cashier Confirmation/Rejection, Cancel Reversal)
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
   Blade Presentation Views (Tailwind CSS, Responsive HTML5 UI)
```

---

## 🗄️ SKEMA DATABASE & ENTITY RELATIONSHIP DIAGRAM (ERD)

Database menggunakan storage engine **InnoDB** pada MySQL dengan integritas relasional *foreign key constraints*:

```
+---------------+        1:N        +---------------+        N:M        +---------------+
|  categories   |<------------------|   products    |------------------>|   suppliers   |
+---------------+                   +---------------+                   +---------------+
| id (PK)       |                   | id (PK)       |                   | id (PK)       |
| name          |                   | category_id FK|                   | name          |
| code          |                   | sku (unique)  |                   | phone, email  |
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
+---------------+                           │            +---------------+ total         |
| purchase_items|                           │            |  sale_items   | status        |
+---------------+                           │            +---------------+ purchased_at  |
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
                                    | user_id FK    |    | sale_number   |
                                    | type (IN/OUT) |    | user_id FK    |
                                    | quantity      |    | customer_name |
                                    | notes         |    | total         |
                                    +---------------+    | status        |
                                                         | sold_at       |
                                                         +---------------+
```

---

## 👥 MATRIKS PERAN & HAK AKSES PENGGUNA (RBAC)

Penerapan *Role-Based Access Control* (RBAC) menjamin pemisahan tugas (*separation of duties*) dan keamanan data sensitif:

| Modul / Tindakan Sistem | Admin / Owner | Staff / Kasir | User / Pelanggan | Mekanisme Proteksi |
|---|:---:|:---:|:---:|---|
| **Login & Logout Sesi** | ✅ Ya | ✅ Ya | ✅ Ya | Sesi aman via username/email |
| **Katalog Produk Belanja** | ✅ Ya | ✅ Ya | ✅ Ya | Tampilan katalog publik & stok |
| **Pemesanan Mandiri (Customer Order)** | ❌ (Gunakan POS) | ❌ (Gunakan POS) | ✅ Ya | Form khusus pelanggan |
| **Lihat Riwayat Pesanan Sendiri** | ❌ (Lihat Penjualan) | ❌ (Lihat Penjualan) | ✅ Ya | Khusus pesanan milik user login |
| **Akses Dashboard Metrik Toko** | ✅ Ya | ✅ Ya | ❌ **Ditolak** | Redirect otomatis ke Pesanan Saya |
| **Kasir & Penjualan Langsung (POS)** | ✅ Ya | ✅ Ya | ❌ **Ditolak** | Middleware `role:admin,staff` |
| **Konfirmasi / Tolak Pesanan Pelanggan**| ✅ Ya | ✅ Ya | ❌ **Ditolak** | Middleware `role:admin,staff` |
| **Lihat Semua Faktur Penjualan** | ✅ Ya | ✅ Ya | ❌ **Ditolak** | Middleware `role:admin,staff` |
| **Master Produk (Tambah, Edit, Hapus)**| ✅ Ya | ❌ Tidak | ❌ **Ditolak** | Middleware `role:admin` |
| **Kelola Kategori & Supplier** | ✅ Ya | ❌ Tidak | ❌ **Ditolak** | Middleware `role:admin` |
| **Transaksi Pengadaan Barang (PO)** | ✅ Ya | ❌ Tidak | ❌ **Ditolak** | Middleware `role:admin` |
| **Batalkan Transaksi Penjualan/PO** | ✅ Ya | ❌ Tidak | ❌ **Ditolak** | Middleware `role:admin` |
| **Manajemen Pengguna (User Management)**| ✅ Ya | ❌ Tidak | ❌ **Ditolak** | Middleware `role:admin` |
| **Audit Trail Mutasi Stok** | ✅ Ya | ✅ Ya | ❌ **Ditolak** | Middleware `role:admin,staff` |

---

## 🔒 INTEGRITAS DATA, CONCURRENCY, & ATURAN BISNIS STOK

1. **Atomisitas Transaksi (`DB::transaction`)**:
   - Pembuatan pesanan pelanggan, transaksi kasir, dan pengadaan dieksekusi secara utuh (*all-or-nothing*). Kegagalan pada baris mana pun akan membatalkan seluruh proses (*rollback*).

2. **Pessimistic Concurrency Locking (`lockForUpdate()`)**:
   - Saat kasir mengonfirmasi pesanan pelanggan atau memproses transaksi POS, baris data produk dikunci di level MySQL guna mencegah inkonsistensi stok saat banyak transaksi terjadi bersamaan.

3. **Status Pesanan Dua Tahap (Two-Phase Order Lifecycle)**:
   - Pesanan dari pelanggan berstatus `pending` dan **tidak memotong stok gudang** sebelum dikonfirmasi oleh kasir.
   - Ketika kasir menekan tombol "Konfirmasi", sistem memvalidasi ketersediaan stok fisik kembali, memotong kuantitas stok produk, mencatat buku besar `stock_movements`, dan mengubah status menjadi `completed`.

4. **Integritas Harga Sisi Server (Server-Side Pricing)**:
   - Perhitungan total biaya pesanan selalu mengambil harga satuan resmi (`selling_price`) dari basis data server, bukan dari input formulir HTML sisi browser.

5. **Isolasi Privasi Antar-Pengguna**:
   - Pelanggan hanya diizinkan melihat faktur pesanannya sendiri. Permintaan akses ke ID pesanan pelanggan lain langsung dicegat dengan status `HTTP 403 Forbidden`.

---

## 🌐 DAFTAR ENDPOINT ROUTING & HTTP METHODS

Semua rute terdaftar rapi pada file [`routes/web.php`](file:///C:/xampp/htdocs/inventory-laravel/routes/web.php):

| Method | URI | Controller Action | Hak Akses (Role) | Deskripsi |
|---|---|---|---|---|
| `GET` | `/login` | `AuthController@showLoginForm` | Tamu (Guest) | Halaman login pengguna |
| `POST` | `/login` | `AuthController@login` | Tamu (Guest) | Autentikasi kredensial |
| `POST` | `/logout` | `AuthController@logout` | Semua Role Terautentikasi | Keluar dari sistem |
| `GET` | `/` | `DashboardController@index` | Semua Role | Beranda (Pelanggan dialihkan ke pesanan) |
| `GET` | `/dashboard` | `DashboardController@index` | Admin, Staff | Dashboard metrik finansial & KPI |
| `GET` | `/catalog` | `OrderController@catalog` | Semua Role | Katalog produk belanja pelanggan |
| `GET` | `/orders` | `OrderController@index` | User (Pelanggan) | Daftar riwayat pesanan milik pelanggan login |
| `GET` | `/orders/create` | `OrderController@create` | User (Pelanggan) | Form pemesanan belanja mandiri pelanggan |
| `POST` | `/orders` | `OrderController@store` | User (Pelanggan) | Kirim pesanan baru (Status: Pending) |
| `GET` | `/orders/{order}` | `OrderController@show` | User (Pelanggan) | Rincian detail pesanan milik pelanggan |
| `GET` | `/sales` | `SaleController@index` | Admin, Staff | Daftar transaksi kasir & pesanan masuk |
| `GET` | `/sales/create` | `SaleController@create` | Admin, Staff | Form kasir penjualan POS langsung |
| `POST` | `/sales` | `SaleController@store` | Admin, Staff | Simpan transaksi kasir langsung |
| `GET` | `/sales/{sale}` | `SaleController@show` | Admin, Staff | Rincian transaksi faktur penjualan |
| `POST` | `/sales/{sale}/confirm` | `SaleController@confirm` | Admin, Staff | Konfirmasi pesanan pelanggan & kurangi stok |
| `POST` | `/sales/{sale}/reject` | `SaleController@reject` | Admin, Staff | Tolak pesanan pelanggan |
| `GET` | `/sales/{sale}/receipt` | `SaleController@receipt` | Admin, Staff | Struk kasir thermal POS siap cetak |
| `POST` | `/sales/{sale}/cancel` | `SaleController@cancel` | Admin Only | Batalkan transaksi penjualan & reversal stok |
| `GET` | `/purchases` | `PurchaseController@index` | Admin, Staff | Daftar riwayat pengadaan (PO) |
| `GET` | `/purchases/create` | `PurchaseController@create` | Admin Only | Form transaksi pengadaan baru |
| `POST` | `/purchases` | `PurchaseController@store` | Admin Only | Simpan pengadaan barang masuk |
| `GET` | `/purchases/{purchase}` | `PurchaseController@show` | Admin, Staff | Rincian faktur pengadaan |
| `POST` | `/purchases/{purchase}/cancel` | `PurchaseController@cancel` | Admin Only | Batalkan pengadaan & reversal stok |
| `GET` | `/products` | `ProductController@index` | Admin, Staff | Master produk (Admin) |
| `GET` | `/products/create` | `ProductController@create` | Admin Only | Form tambah produk |
| `POST` | `/products` | `ProductController@store` | Admin Only | Simpan master produk baru |
| `GET` | `/products/{product}/edit` | `ProductController@edit` | Admin Only | Form edit master produk |
| `PUT` | `/products/{product}` | `ProductController@update` | Admin Only | Perbarui data produk |
| `DELETE` | `/products/{product}` | `ProductController@destroy` | Admin Only | Hapus master produk |
| `GET` | `/categories` | `CategoryController@index` | Admin Only | Master kategori barang |
| `GET` | `/suppliers` | `SupplierController@index` | Admin Only | Master rekanan supplier |
| `GET` | `/users` | `UserController@index` | Admin Only | Kelola akun pengguna |
| `GET` | `/stock/history` | `StockMovementController@history` | Admin, Staff | Audit log mutasi stok fisik |

---

## 🧪 LAPORAN PENGUJIAN OTOMATIS (FEATURE TEST SUITE)

Seluruh fungsionalitas, hak akses RBAC, dan alur pemesanan pelanggan telah diverifikasi menggunakan pengujian unit & fitur otomatis (`tests/Feature/InventorySystemTest.php`):

```text
 PASS  Tests\Feature\InventorySystemTest
 ✓ 01 auth guest cannot access dashboard and invalid password is rejected
 ✓ 02 product validation duplicate sku and negative price rejected
 ✓ 03 category and product eloquent relationship
 ✓ 04 purchase increments stock and logs stock movement
 ✓ 05 sale decrements stock with pessimistic locking and creates movement
 ✓ 06 sale fails when quantity exceeds available stock
 ✓ 07 transaction atomicity and rollback on failure
 ✓ 08 authorization staff cannot access admin functions
 ✓ 09 search and pagination filter
 ✓ 10 customer can access catalog and orders
 ✓ 11 customer cannot access private store data
 ✓ 12 customer order creates pending sale without decrementing stock
 ✓ 13 cashier can confirm pending order and stock is decremented
 ✓ 14 customer cannot view other customers order

  Tests:    16 passed (64 assertions)
  Duration: 1.28s
  Status:   100% SUCCESS (ALL TESTS PASSING)
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
│   │   │   ├── AuthController.php             # Login, logout, role-based redirect
│   │   │   ├── CategoryController.php         # Master kategori + delete restrict
│   │   │   ├── DashboardController.php        # Metrik agregasi KPI & low stock (staff/admin)
│   │   │   ├── OrderController.php            # Katalog pelanggan, pesan barang, pesanan saya
│   │   │   ├── ProductController.php          # CRUD produk, search, filter, sort
│   │   │   ├── PurchaseController.php         # Multi-item PO & cancel reversal
│   │   │   ├── SaleController.php             # Kasir POS, konfirmasi/tolak pesanan pelanggan
│   │   │   ├── StockMovementController.php    # Audit trail mutasi stok
│   │   │   ├── SupplierController.php         # Master supplier + delete restrict
│   │   │   └── UserController.php             # Kelola akun pengguna (Admin only)
│   │   └── Middleware/
│   │       └── EnsureRole.php                 # Role authorization guard (admin, staff, user)
│   ├── Models/
│   │   ├── Category.php                       # Model kategori (hasMany Product)
│   │   ├── Product.php                        # Model produk (purchase & selling price)
│   │   ├── Purchase.php                       # Model pengadaan header
│   │   ├── PurchaseItem.php                   # Model rincian item pengadaan
│   │   ├── Sale.php                           # Model penjualan kasir / pesanan pelanggan
│   │   ├── SaleItem.php                       # Model rincian item transaksi
│   │   ├── StockMovement.php                  # Model audit log pergerakan stok
│   │   ├── Supplier.php                       # Model rekanan pemasok
│   │   └── User.php                           # Model pengguna & helper hak akses (isCustomer, etc.)
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
│       └── DatabaseSeeder.php                 # Seeder 3 akun (admin, staff, user) & 10 produk
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php                # Tampilan login modern
│       ├── categories/                        # Views CRUD kategori
│       ├── components/
│       │   └── alert.blade.php                # Reusable flash alert component
│       ├── dashboard.blade.php                # Dashboard agregasi metrik & stok menipis
│       ├── layouts/
│       │   ├── app.blade.php                  # Master layout
│       │   └── navigation.blade.php           # Navigasi dinamis terpisah sesuai role
│       ├── orders/                            # Views Portal Pelanggan
│       │   ├── catalog.blade.php              # Katalog produk ramah pelanggan
│       │   ├── create.blade.php               # Form pemesanan mandiri pelanggan
│       │   ├── index.blade.php                # Riwayat pesanan saya & status konfirmasi
│       │   └── show.blade.php                 # Rincian pesanan pelanggan
│       ├── products/                          # Views master produk
│       ├── purchases/                         # Views pengadaan multi-item
│       ├── sales/                             # Views kasir POS, konfirmasi pesanan, & struk
│       ├── stock/
│       │   └── history.blade.php              # Views riwayat mutasi stok
│       ├── suppliers/                         # Views CRUD supplier
│       └── users/
│           └── index.blade.php                # Views manajemen pengguna (Admin)
├── routes/
│   └── web.php                                # Definisi endpoint routing aplikasi
├── tests/
│   └── Feature/
│       └── InventorySystemTest.php            # Test suite 14 skenario pengujian komprehensif
├── .env.example                               # Template konfigurasi environment (aman dari rahasia)
├── .env                                       # Konfigurasi lokal (terproteksi .gitignore, tidak di-push)
├── run_server.bat                             # Skrip otomatis 1-klik di Windows
└── README.md                                  # Dokumentasi komprehensif sistem
```

---

## ⚠️ BATASAN SISTEM (KNOWN LIMITATIONS)

Ruang lingkup dan batasan fitur sistem ini mencakup:

1. **Simulasi Transaksi Kasir & Pelanggan Lokal**: Tidak menggunakan *Payment Gateway* perbankan pihak ketiga (seperti Midtrans/Xendit nyata). Alur pemesanan diselesaikan dengan konfirmasi verifikasi kasir di toko, auto-potong stok real-time, dan pencetakan struk kasir thermal POS 80mm siap cetak (`window.print()`).
2. **Mata Uang Tunggal (Single Currency)**: Seluruh pencatatan harga dan transaksi beroperasi menggunakan mata uang Rupiah Indonesia (IDR / Rp) tanpa konversi valuta asing (*multi-currency*).
3. **Satu Gudang Utama (Single Warehouse)**: Pengelolaan stok barang berfokus pada satu lokasi operasional toko/gudang utama tanpa fitur mutasi antar-cabang (*inter-warehouse transfer*).
4. **Fokus Inti Konsistensi & Integritas**: Nilai utama sistem terletak pada **konsistensi stok**, pencegahan penjualan minus (*zero-overselling*), transaksi atomik (`DB::transaction`), penguncian baris konkurensi (`Product::lockForUpdate()`), dan pembagian hak akses terisolasi (*Role-Based Access Control*).
