<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Katalog Produk untuk Pelanggan / User
     */
    public function catalog(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('orders.catalog', compact('products', 'categories'));
    }

    /**
     * Daftar Riwayat Pesanan Saya (Khusus User)
     */
    public function index()
    {
        $user = auth()->user();

        $orders = Sale::with(['items.product'])
            ->where('user_id', $user->id)
            ->latest('sold_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Form Buat Pemesanan Baru
     */
    public function create(Request $request)
    {
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $selectedProductId = $request->query('product_id');

        return view('orders.create', compact('products', 'selectedProductId'));
    }

    /**
     * Simpan Pemesanan Baru (Status: Pending Menunggu Konfirmasi Kasir)
     */
    public function store(Request $request)
    {
        $request->validate([
            'notes'              => ['nullable', 'string', 'max:500'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Minimal pilih 1 produk untuk melakukan pemesanan.',
            'items.min'      => 'Minimal pilih 1 produk untuk melakukan pemesanan.',
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                $orderNumber = 'ORD-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -5));

                $total = 0;
                $orderItemsData = [];

                foreach ($request->items as $itemData) {
                    $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                    $qty = (int) $itemData['quantity'];

                    // Cek ketersediaan stok
                    if ($qty < 1 || $product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'items' => "Stok produk '{$product->name}' tidak mencukupi! Tersedia: {$product->stock}, Anda meminta: {$qty}."
                        ]);
                    }

                    $unitPrice = $product->selling_price;
                    $subtotal = $unitPrice * $qty;
                    $total += $subtotal;

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal'   => $subtotal,
                    ];
                }

                // Simpan transaksi penjualan berstatus PENDING (stok belum dipotong sampai dikonfirmasi kasir)
                $sale = Sale::create([
                    'user_id'       => auth()->id(),
                    'customer_name' => auth()->user()->name,
                    'sale_number'   => $orderNumber,
                    'total'         => $total,
                    'status'        => 'pending',
                    'sold_at'       => now(),
                    'notes'         => $request->notes,
                ]);

                foreach ($orderItemsData as $item) {
                    $sale->items()->create($item);
                }

                return $sale;
            });
        } catch (ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan Anda (' . $order->sale_number . ') berhasil dikirim! Mohon menunggu konfirmasi dan proses dari kasir.');
    }

    /**
     * Detail Pesanan Pelanggan
     */
    public function show(Sale $order)
    {
        // Proteksi: User hanya boleh melihat pesanannya sendiri!
        if (auth()->user()->isCustomer() && $order->user_id !== auth()->id()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data pesanan pengguna lain.');
        }

        $order->load(['user', 'items.product.category']);

        return view('orders.show', compact('order'));
    }
}
