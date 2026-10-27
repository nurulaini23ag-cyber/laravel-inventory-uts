<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin/Owner, Staff Kasir, dan User/Pelanggan)
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventory.com'],
            [
                'name'     => 'Administrator / Owner',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@inventory.com'],
            [
                'name'     => 'Staff Kasir',
                'username' => 'staff',
                'password' => Hash::make('staff123'),
                'role'     => 'staff',
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'user@inventory.com'],
            [
                'name'     => 'Budi (Pelanggan)',
                'username' => 'user',
                'password' => Hash::make('user123'),
                'role'     => 'user',
            ]
        );

        // 2. Seed Kategori Khusus Aksesoris & Periferal Laptop
        $catPeripherals = Category::firstOrCreate(
            ['name' => 'Periferal Komputer'],
            ['description' => 'Keyboard, mouse, dan perangkat input komputer/laptop']
        );
        $catAccessories = Category::firstOrCreate(
            ['name' => 'Aksesoris Laptop'],
            ['description' => 'Perlengkapan pendukung operasional dan produktivitas laptop']
        );
        $catProtection = Category::firstOrCreate(
            ['name' => 'Pelindung & Proteksi Laptop'],
            ['description' => 'Perlindungan fisik laptop: case, screen protector, dan keyboard cover']
        );

        // 3. Seed Suppliers
        $supplier1 = Supplier::firstOrCreate(
            ['name' => 'PT Tech Distro Utama'],
            [
                'contact_info' => 'Budi Santoso',
                'phone'        => '08123456789',
                'email'        => 'budi@techdistro.id',
            ]
        );
        $supplier2 = Supplier::firstOrCreate(
            ['name' => 'CV Surya Proteksi Komputer'],
            [
                'contact_info' => 'Siti Rahma',
                'phone'        => '08987654321',
                'email'        => 'sales@suryaproteksi.com',
            ]
        );

        // 4. Seed TEPAT 10 PRODUK (Keyboard, Mouse, Aksesoris Laptop, Pelindung, Anti Gores)
        $productsData = [
            // 1 & 2: Keyboard
            [
                'sku'            => 'KB-001',
                'category_id'    => $catPeripherals->id,
                'name'           => 'Keyboard Mekanikal Gaming RGB',
                'purchase_price' => 350000.00,
                'selling_price'  => 450000.00,
                'stock'          => 15,
                'minimum_stock'  => 5,
                'supplier_id'    => $supplier1->id,
            ],
            [
                'sku'            => 'KB-002',
                'category_id'    => $catPeripherals->id,
                'name'           => 'Keyboard Wireless Slim Portable',
                'purchase_price' => 180000.00,
                'selling_price'  => 245000.00,
                'stock'          => 20,
                'minimum_stock'  => 5,
                'supplier_id'    => $supplier1->id,
            ],
            // 3 & 4: Mouse
            [
                'sku'            => 'MS-001',
                'category_id'    => $catPeripherals->id,
                'name'           => 'Mouse Wireless Silent Click',
                'purchase_price' => 85000.00,
                'selling_price'  => 125000.00,
                'stock'          => 25,
                'minimum_stock'  => 8,
                'supplier_id'    => $supplier1->id,
            ],
            [
                'sku'            => 'MS-002',
                'category_id'    => $catPeripherals->id,
                'name'           => 'Mouse Gaming RGB Ergonomis',
                'purchase_price' => 150000.00,
                'selling_price'  => 210000.00,
                'stock'          => 18,
                'minimum_stock'  => 5,
                'supplier_id'    => $supplier1->id,
            ],
            // 5, 6 & 7: Aksesoris Laptop
            [
                'sku'            => 'ACC-001',
                'category_id'    => $catAccessories->id,
                'name'           => 'Stand Laptop Aluminium Adjustable',
                'purchase_price' => 120000.00,
                'selling_price'  => 175000.00,
                'stock'          => 12,
                'minimum_stock'  => 4,
                'supplier_id'    => $supplier1->id,
            ],
            [
                'sku'            => 'ACC-002',
                'category_id'    => $catAccessories->id,
                'name'           => 'Cooling Pad Laptop RGB Dual Fan',
                'purchase_price' => 110000.00,
                'selling_price'  => 160000.00,
                'stock'          => 10,
                'minimum_stock'  => 4,
                'supplier_id'    => $supplier1->id,
            ],
            [
                'sku'            => 'ACC-003',
                'category_id'    => $catAccessories->id,
                'name'           => 'USB Type-C Hub Multiport 6-in-1',
                'purchase_price' => 195000.00,
                'selling_price'  => 275000.00,
                'stock'          => 14,
                'minimum_stock'  => 5,
                'supplier_id'    => $supplier1->id,
            ],
            // 8: Pelindung Laptop
            [
                'sku'            => 'PROT-001',
                'category_id'    => $catProtection->id,
                'name'           => 'Sleeve Case Pelindung Laptop 14 Inch',
                'purchase_price' => 75000.00,
                'selling_price'  => 115000.00,
                'stock'          => 20,
                'minimum_stock'  => 6,
                'supplier_id'    => $supplier2->id,
            ],
            // 9: Anti Gores Laptop
            [
                'sku'            => 'PROT-002',
                'category_id'    => $catProtection->id,
                'name'           => 'Anti Gores Layar Laptop 14 Inch (Screen Protector)',
                'purchase_price' => 35000.00,
                'selling_price'  => 60000.00,
                'stock'          => 30,
                'minimum_stock'  => 10,
                'supplier_id'    => $supplier2->id,
            ],
            // 10: Pelindung Keyboard
            [
                'sku'            => 'PROT-003',
                'category_id'    => $catProtection->id,
                'name'           => 'Pelindung Keyboard Silikon Universal',
                'purchase_price' => 25000.00,
                'selling_price'  => 45000.00,
                'stock'          => 28,
                'minimum_stock'  => 10,
                'supplier_id'    => $supplier2->id,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $supplierId = $data['supplier_id'];
            unset($data['supplier_id']);

            $product = Product::firstOrCreate(['sku' => $data['sku']], $data);
            $product->suppliers()->syncWithoutDetaching([$supplierId => ['supplier_price' => $data['purchase_price']]]);
            $createdProducts[$product->sku] = $product;
        }

        // 5. Seed Transaksi Pembelian Awal (Purchase Order)
        $pKb1 = $createdProducts['KB-001'];
        $pMs1 = $createdProducts['MS-001'];
        $pAcc1 = $createdProducts['ACC-001'];

        $purchase1 = Purchase::firstOrCreate(
            ['purchase_number' => 'PO-202610-0001'],
            [
                'supplier_id'  => $supplier1->id,
                'user_id'      => $admin->id,
                'total'        => (350000 * 5) + (85000 * 10),
                'status'       => 'completed',
                'purchased_at' => now()->subDays(3),
                'notes'        => 'Pengadaan stok awal aksesoris laptop & periferal',
            ]
        );
        PurchaseItem::firstOrCreate([
            'purchase_id' => $purchase1->id,
            'product_id'  => $pKb1->id,
        ], [
            'quantity'   => 5,
            'unit_cost'  => 350000.00,
            'subtotal'   => 350000.00 * 5,
        ]);
        PurchaseItem::firstOrCreate([
            'purchase_id' => $purchase1->id,
            'product_id'  => $pMs1->id,
        ], [
            'quantity'   => 10,
            'unit_cost'  => 85000.00,
            'subtotal'   => 85000.00 * 10,
        ]);

        // 6. Seed Transaksi Penjualan Awal (Kasir POS Selesai)
        $pProt1 = $createdProducts['PROT-001'];

        $sale1 = Sale::firstOrCreate(
            ['sale_number' => 'INV-202610-0001'],
            [
                'user_id'       => $staff->id,
                'customer_name' => 'Pelanggan Umum (Walk-in)',
                'total'         => (450000 * 1) + (115000 * 1),
                'status'        => 'completed',
                'sold_at'       => now()->subDays(1),
                'notes'         => 'Penjualan kasir toko fisik',
            ]
        );
        SaleItem::firstOrCreate([
            'sale_id'    => $sale1->id,
            'product_id' => $pKb1->id,
        ], [
            'quantity'   => 1,
            'unit_price' => 450000.00,
            'subtotal'   => 450000.00 * 1,
        ]);
        SaleItem::firstOrCreate([
            'sale_id'    => $sale1->id,
            'product_id' => $pProt1->id,
        ], [
            'quantity'   => 1,
            'unit_price' => 115000.00,
            'subtotal'   => 115000.00 * 1,
        ]);

        // 7. Seed Pesanan Pelanggan (Status Pending Menunggu Konfirmasi Kasir)
        $pendingOrder = Sale::firstOrCreate(
            ['sale_number' => 'ORD-202610-0001'],
            [
                'user_id'       => $customerUser->id,
                'customer_name' => $customerUser->name,
                'total'         => (175000 * 1) + (125000 * 1),
                'status'        => 'pending',
                'sold_at'       => now(),
                'notes'         => 'Pesanan online: mohon disiapkan untuk diambil di toko',
            ]
        );
        SaleItem::firstOrCreate([
            'sale_id'    => $pendingOrder->id,
            'product_id' => $pAcc1->id,
        ], [
            'quantity'   => 1,
            'unit_price' => 175000.00,
            'subtotal'   => 175000.00 * 1,
        ]);
        SaleItem::firstOrCreate([
            'sale_id'    => $pendingOrder->id,
            'product_id' => $pMs1->id,
        ], [
            'quantity'   => 1,
            'unit_price' => 125000.00,
            'subtotal'   => 125000.00 * 1,
        ]);

        // 8. Audit Trail Riwayat Mutasi Stok
        StockMovement::firstOrCreate([
            'product_id' => $pKb1->id,
            'user_id'    => $admin->id,
            'type'       => 'IN',
            'quantity'   => 15,
            'notes'      => 'Pengadaan awal PO-202610-0001',
        ]);
        StockMovement::firstOrCreate([
            'product_id' => $pKb1->id,
            'user_id'    => $staff->id,
            'type'       => 'OUT',
            'quantity'   => 1,
            'notes'      => 'Penjualan kasir INV-202610-0001',
        ]);
    }
}
