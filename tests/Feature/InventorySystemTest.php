<?php


namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  1. Auth: Guest tidak dapat membuka dashboard; password salah ditolak
     */
    public function test_auth_guest_cannot_access_dashboard_and_invalid_password_is_rejected()
    {
        // Guest akses dashboard -> redirect ke login
        $guestResponse = $this->get(route('dashboard'));
        $guestResponse->assertRedirect(route('login'));

        $user = User::factory()->create([
            'email'    => 'testuser@inventory.com',
            'password' => Hash::make('secret123'),
        ]);

        // Login dengan password salah ditolak
        $invalidLogin = $this->post(route('login.post'), [
            'login'    => 'testuser@inventory.com',
            'password' => 'wrongpassword',
        ]);
        $invalidLogin->assertSessionHasErrors('login');
        $this->assertGuest();

        // Login dengan password benar diterima
        $validLogin = $this->post(route('login.post'), [
            'login'    => 'testuser@inventory.com',
            'password' => 'secret123',
        ]);
        $validLogin->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     *  2. Product: SKU duplikat ditolak; harga negatif ditolak
     */
    public function test_product_validation_duplicate_sku_and_negative_price_rejected()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $this->actingAs($admin);

        Product::factory()->create([
            'sku'         => 'SKU-UNIQUE-01',
            'category_id' => $category->id,
        ]);

        // Test duplicate SKU
        $duplicateResponse = $this->post(route('products.store'), [
            'category_id'    => $category->id,
            'sku'            => 'SKU-UNIQUE-01',
            'name'           => 'Duplicated Item',
            'purchase_price' => 50000,
            'selling_price'  => 75000,
            'stock'          => 10,
            'minimum_stock'  => 5,
        ]);
        $duplicateResponse->assertSessionHasErrors('sku');

        // Test negative price
        $negativePriceResponse = $this->post(route('products.store'), [
            'category_id'    => $category->id,
            'sku'            => 'SKU-NEG-01',
            'name'           => 'Negative Price Item',
            'purchase_price' => -1000,
            'selling_price'  => -5000,
            'stock'          => 10,
            'minimum_stock'  => 5,
        ]);
        $negativePriceResponse->assertSessionHasErrors(['purchase_price', 'selling_price']);
    }

    /**
     *  3. Eloquent: Relasi Kategori dan Produk
     */
    public function test_category_and_product_eloquent_relationship()
    {
        $category = Category::factory()->create(['name' => 'Perangkat Lunak']);
        $product = Product::factory()->create([
            'category_id'    => $category->id,
            'sku'            => 'SW-001',
            'name'           => 'Operating System License',
            'purchase_price' => 500000,
            'selling_price'  => 750000,
            'stock'          => 20,
        ]);

        $this->assertEquals('Perangkat Lunak', $product->category->name);
        $this->assertTrue($category->products->contains($product));
    }

    /**
     *  4. Purchase: Pembelian sukses menambah stok sesuai kuantitas
     */
    public function test_purchase_transaction_increases_stock_correctly()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $supplier = Supplier::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id'    => $category->id,
            'stock'          => 10,
            'purchase_price' => 50000,
            'selling_price'  => 70000,
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('purchases.store'), [
            'supplier_id'  => $supplier->id,
            'purchased_at' => now()->format('Y-m-d H:i:s'),
            'notes'        => 'Restock barang masuk',
            'items'        => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 15,
                    'unit_cost'  => 52000,
                ]
            ]
        ]);

        $response->assertSessionHas('success');
        // Stok bertambah dari 10 + 15 = 25
        $this->assertEquals(25, $product->fresh()->stock);

        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $supplier->id,
            'status'      => 'completed',
            'total'       => 15 * 52000,
        ]);
    }

    /**
     *  5. Sale: Penjualan sukses mengurangi stok sesuai kuantitas
     */
    public function test_sale_transaction_decreases_stock_correctly()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id'    => $category->id,
            'stock'          => 30,
            'selling_price'  => 100000,
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('sales.store'), [
            'sold_at' => now()->format('Y-m-d H:i:s'),
            'notes'   => 'Penjualan kasir online',
            'items'   => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 8,
                ]
            ]
        ]);

        $response->assertSessionHas('success');
        // Stok berkurang dari 30 - 8 = 22
        $this->assertEquals(22, $product->fresh()->stock);

        $this->assertDatabaseHas('sales', [
            'user_id' => $staff->id,
            'status'  => 'completed',
            'total'   => 8 * 100000,
        ]);
    }

    /**
     *  6. Insufficient Stock: Penjualan stok tidak cukup ditolak tanpa perubahan data
     */
    public function test_sale_with_insufficient_stock_is_rejected_without_data_corruption()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id'   => $category->id,
            'stock'         => 5,
            'selling_price' => 100000,
        ]);

        $this->actingAs($staff);

        // Mencoba beli 10 padahal stok hanya 5
        $response = $this->post(route('sales.store'), [
            'sold_at' => now()->format('Y-m-d H:i:s'),
            'items'   => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 10,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('items');
        // Stok tetap 5 (tidak berubah)
        $this->assertEquals(5, $product->fresh()->stock);
        // Tidak ada sale yang dibuat
        $this->assertEquals(0, Sale::count());
    }

    /**
     *  7. Atomicity: Jika satu item gagal, seluruh transaksi di-rollback
     */
    public function test_transaction_atomicity_and_rollback_on_failure()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();

        $productA = Product::factory()->create(['category_id' => $category->id, 'stock' => 10, 'selling_price' => 50000]);
        $productB = Product::factory()->create(['category_id' => $category->id, 'stock' => 2, 'selling_price' => 50000]); // Stok hanya 2

        $this->actingAs($staff);

        // Item 1 beli 3 dari A (stok cukup), item 2 beli 5 dari B (stok kurang!)
        $response = $this->post(route('sales.store'), [
            'sold_at' => now()->format('Y-m-d H:i:s'),
            'items'   => [
                ['product_id' => $productA->id, 'quantity' => 3],
                ['product_id' => $productB->id, 'quantity' => 5],
            ]
        ]);

        $response->assertSessionHasErrors('items');

        // Rollback check: Stok productA tidak boleh berkurang!
        $this->assertEquals(10, $productA->fresh()->stock);
        $this->assertEquals(2, $productB->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    /**
     *  8. Authorization: Staff tidak dapat mengakses fungsi Admin
     */
    public function test_authorization_staff_cannot_access_admin_functions()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        // Staff mencoba mengakses manajemen users -> 403 Forbidden
        $response = $this->get(route('users.index'));
        $response->assertStatus(403);
    }

    /**
     *  9. Search: Filter dan pagination
     */
    public function test_search_and_pagination_filter()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $category = Category::factory()->create(['name' => 'Kategori Khusus']);
        Product::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Barang Alpha Spesial',
            'sku'         => 'ALPHA-001',
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Barang Beta Biasa',
            'sku'         => 'BETA-001',
        ]);

        $response = $this->get(route('products.index', ['search' => 'Alpha']));
        $response->assertStatus(200);
        $response->assertSee('Barang Alpha Spesial');
        $response->assertDontSee('Barang Beta Biasa');
    }

    /**
     * 10. Role Pelanggan: User hanya bisa akses katalog dan pesanan sendiri
     */
    public function test_customer_can_access_catalog_and_orders()
    {
        $customer = User::factory()->create(['role' => 'user']);
        $this->actingAs($customer);

        $responseCatalog = $this->get(route('orders.catalog'));
        $responseCatalog->assertStatus(200);

        $responseOrders = $this->get(route('orders.index'));
        $responseOrders->assertStatus(200);

        $responseCreate = $this->get(route('orders.create'));
        $responseCreate->assertStatus(200);
    }

    /**
     * 11. Role Pelanggan: User tidak dapat mengakses data privat toko (dashboard, pembelian, supplier, users, sales)
     */
    public function test_customer_cannot_access_private_store_data()
    {
        $customer = User::factory()->create(['role' => 'user']);
        $this->actingAs($customer);

        // Dashboard dialihkan ke pesanan saya (tidak bisa intip KPI & omset)
        $this->get(route('dashboard'))->assertRedirect(route('orders.index'));

        // Menu privat toko mengembalikan 403 Forbidden
        $this->get(route('purchases.index'))->assertStatus(403);
        $this->get(route('categories.index'))->assertStatus(403);
        $this->get(route('suppliers.index'))->assertStatus(403);
        $this->get(route('users.index'))->assertStatus(403);
        $this->get(route('stock.history'))->assertStatus(403);
        $this->get(route('sales.index'))->assertStatus(403);
    }

    /**
     * 12. Alur Pemesanan User: Status pesanan pending dan stok belum berkurang sampai kasir konfirmasi
     */
    public function test_customer_order_creates_pending_sale_without_decrementing_stock()
    {
        $customer = User::factory()->create(['role' => 'user', 'name' => 'Budi Santoso']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id'   => $category->id,
            'stock'         => 20,
            'selling_price' => 150000,
        ]);

        $this->actingAs($customer);

        $response = $this->post(route('orders.store'), [
            'notes' => 'Tolong packing rapi ya kak',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 3,
                ]
            ]
        ]);

        $response->assertRedirect(route('orders.index'));

        // Cek data pesanan tersimpan dengan status pending
        $sale = Sale::where('user_id', $customer->id)->first();
        $this->assertNotNull($sale);
        $this->assertEquals('pending', $sale->status);
        $this->assertEquals(450000, (int)$sale->total);
        $this->assertEquals('Budi Santoso', $sale->customer_name);

        // STOK BELUM BERKURANG (karena belum dikonfirmasi kasir)
        $this->assertEquals(20, $product->fresh()->stock);
    }

    /**
     * 13. Alur Kasir: Kasir mengonfirmasi pesanan user -> stok berkurang & status completed
     */
    public function test_cashier_can_confirm_pending_order_and_stock_is_decremented()
    {
        $customer = User::factory()->create(['role' => 'user', 'name' => 'Budi Santoso']);
        $cashier = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id'   => $category->id,
            'stock'         => 20,
            'selling_price' => 100000,
        ]);

        // 1. Customer buat pesanan
        $this->actingAs($customer);
        $this->post(route('orders.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 4]
            ]
        ]);
        $sale = Sale::where('user_id', $customer->id)->first();
        $this->assertEquals('pending', $sale->status);
        $this->assertEquals(20, $product->fresh()->stock);

        // 2. Kasir konfirmasi pesanan
        $this->actingAs($cashier);
        $confirmResponse = $this->post(route('sales.confirm', $sale));
        $confirmResponse->assertRedirect(route('sales.show', $sale));

        // 3. Status selesai dan stok berkurang jadi 16 (20 - 4)
        $this->assertEquals('completed', $sale->fresh()->status);
        $this->assertEquals(16, $product->fresh()->stock);

        // Movement audit record tersimpan
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type'       => 'OUT',
            'quantity'   => 4,
        ]);
    }

    /**
     * 14. Keamanan Privasi: User tidak bisa melihat rincian pesanan milik user lain
     */
    public function test_customer_cannot_view_other_customers_order()
    {
        $customerA = User::factory()->create(['role' => 'user', 'name' => 'User A']);
        $customerB = User::factory()->create(['role' => 'user', 'name' => 'User B']);

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10, 'selling_price' => 50000]);

        // Customer A pesan barang
        $this->actingAs($customerA);
        $this->post(route('orders.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]]
        ]);
        $saleA = Sale::where('user_id', $customerA->id)->first();

        // Customer B mencoba mengakses pesanan milik Customer A -> 403 Forbidden
        $this->actingAs($customerB);
        $forbiddenResponse = $this->get(route('orders.show', $saleA));
        $forbiddenResponse->assertStatus(403);
    }
}