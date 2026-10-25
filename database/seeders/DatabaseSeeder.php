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
        // 1.  Seed Users (Admin/Owner dan Staff)
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
                'name'     => 'Staff Operasional',
                'username' => 'staff',
                'password' => Hash::make('staff123'),
                'role'     => 'staff',
            ]
        );

        // 2.  Seed Categories
        $catElectronics = Category::firstOrCreate(
            ['name' => 'Elektronik'],
            ['description' => 'Peralatan dan peranti elektronik kantor dan rumah']
        );
        $catAccessories = Category::firstOrCreate(
            ['name' => 'Aksesoris Komputer'],
            ['description' => 'Perlengkapan pendukung PC, Laptop, dan periferal']
        );
        $catStationery = Category::firstOrCreate(
            ['name' => 'Alat Tulis Kantor'],
            ['description' => 'Kebutuhan administrasi dan operasional kantor']
        );

        // 3.  Seed Suppliers
        $supplier1 = Supplier::firstOrCreate(
            ['name' => 'PT Tech Distro Utama'],
            [
                'contact_info' => 'Budi Santoso',
                'phone'        => '08123456789',
                'email'        => 'budi@techdistro.id',
            ]
        );
        $supplier2 = Supplier::firstOrCreate(
            ['name' => 'CV Stationary Abadi'],
            [
                'contact_info' => 'Siti Rahma',
                'phone'        => '08987654321',
                'email'        => 'sales@stationaryabadi.com',
            ]
        );

        // 4.  Seed Products dengan purchase_price & selling_price
        $p1 = Product::firstOrCreate(
            ['sku' => 'ELEC-001'],
            [
                'category_id'    => $catElectronics->id,
                'name'           => 'Monitor Gaming 24 Inch',
                'purchase_price' => 1850000.00,
                'selling_price'  => 2250000.00,
                'stock'          => 15,
                'minimum_stock'  => 5,
            ]
        );
        $p2 = Product::firstOrCreate(
            ['sku' => 'ACC-001'],
            [
                'category_id'    => $catAccessories->id,
                'name'           => 'Keyboard Mekanikal RGB',
                'purchase_price' => 380000.00,
                'selling_price'  => 475000.00,
                'stock'          => 8,
                'minimum_stock'  => 5,
            ]
        );
        $p3 = Product::firstOrCreate(
            ['sku' => 'ACC-002'],
            [
                'category_id'    => $catAccessories->id,
                'name'           => 'Mouse Wireless Silent',
                'purchase_price' => 95000.00,
                'selling_price'  => 135000.00,
                'stock'          => 25,
                'minimum_stock'  => 10,
            ]
        );
        $p4 = Product::firstOrCreate(
            ['sku' => 'ATK-001'],
            [
                'category_id'    => $catStationery->id,
                'name'           => 'Kertas HVS A4 80gr (Ream)',
                'purchase_price' => 40000.00,
                'selling_price'  => 52000.00,
                'stock'          => 3,
                'minimum_stock'  => 8,
            ]
        );

        // Many-to-many suppliers
        $p1->suppliers()->syncWithoutDetaching([$supplier1->id => ['supplier_price' => 1850000.00]]);
        $p2->suppliers()->syncWithoutDetaching([$supplier1->id => ['supplier_price' => 380000.00]]);
        $p3->suppliers()->syncWithoutDetaching([$supplier1->id => ['supplier_price' => 95000.00]]);
        $p4->suppliers()->syncWithoutDetaching([$supplier2->id => ['supplier_price' => 40000.00]]);

        // 5.  Seed Transaksi Pembelian (Purchases & Items)
        $purchase1 = Purchase::firstOrCreate(
            ['purchase_number' => 'PO-202610-0001'],
            [
                'supplier_id'  => $supplier1->id,
                'user_id'      => $admin->id,
                'total'        => (1850000 * 5) + (380000 * 10),
                'status'       => 'completed',
                'purchased_at' => now()->subDays(3),
                'notes'        => 'Pengadaan stok awal kuartal',
            ]
        );
        PurchaseItem::firstOrCreate([
            'purchase_id' => $purchase1->id,
            'product_id'  => $p1->id,
        ], [
            'quantity'   => 5,
            'unit_cost'  => 1850000.00,
            'subtotal'   => 1850000.00 * 5,
        ]);
        PurchaseItem::firstOrCreate([
            'purchase_id' => $purchase1->id,
            'product_id'  => $p2->id,
        ], [
            'quantity'   => 10,
            'unit_cost'  => 380000.00,
            'subtotal'   => 380000.00 * 10,
        ]);

        // 6.  Seed Transaksi Penjualan (Sales & Items)
        $sale1 = Sale::firstOrCreate(
            ['sale_number' => 'INV-202610-0001'],
            [
                'user_id'  => $staff->id,
                'total'    => (475000 * 2) + (135000 * 3),
                'status'   => 'completed',
                'sold_at'  => now()->subDays(1),
                'notes'    => 'Penjualan ritel toko',
            ]
        );
        SaleItem::firstOrCreate([
            'sale_id'    => $sale1->id,
            'product_id' => $p2->id,
        ], [
            'quantity'   => 2,
            'unit_price' => 475000.00,
            'subtotal'   => 475000.00 * 2,
        ]);
        SaleItem::firstOrCreate([
            'sale_id'    => $sale1->id,
            'product_id' => $p3->id,
        ], [
            'quantity'   => 3,
            'unit_price' => 135000.00,
            'subtotal'   => 135000.00 * 3,
        ]);

        // Audit Trail Movements
        StockMovement::firstOrCreate([
            'product_id' => $p1->id,
            'user_id'    => $admin->id,
            'type'       => 'IN',
            'quantity'   => 15,
            'notes'      => 'Pembelian stok awal PO-202610-0001',
        ]);
        StockMovement::firstOrCreate([
            'product_id' => $p2->id,
            'user_id'    => $staff->id,
            'type'       => 'OUT',
            'quantity'   => 2,
            'notes'      => 'Penjualan kasir INV-202610-0001',
        ]);

        // 7. Seed Tambahan Dummy Kategori & Produk Realistic
        Category::factory(4)->create();
        Product::factory(25)->create();
    }
}
