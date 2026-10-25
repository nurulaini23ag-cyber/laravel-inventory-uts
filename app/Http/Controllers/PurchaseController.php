<?php



namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     *  Purchase List with Search & Date Filter
     */
    public function index(Request $request)
    {
        $purchases = Purchase::with(['supplier', 'user', 'items.product'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('purchase_number', 'like', "%{$search}%")
                      ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->start_date, function ($query, $startDate) {
                $query->whereDate('purchased_at', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                $query->whereDate('purchased_at', '<=', $endDate);
            })
            ->latest('purchased_at')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    /**
     *  Form Tambah Pembelian (Multi-item)
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with('category')->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Simpan Header + Items secara atomik dalam DB::transaction dan tambah stok otomatis
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'         => ['required', 'exists:suppliers,id'],
            'purchased_at'        => ['required', 'date'],
            'notes'               => ['nullable', 'string', 'max:500'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'   => ['required', 'numeric', 'min:0'],
        ]);

        $purchase = DB::transaction(function () use ($request) {
            $purchaseNumber = 'PO-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -5));

            $purchase = Purchase::create([
                'supplier_id'     => $request->supplier_id,
                'user_id'         => auth()->id(),
                'purchase_number' => $purchaseNumber,
                'total'           => 0,
                'status'          => 'completed',
                'purchased_at'    => $request->purchased_at,
                'notes'           => $request->notes,
            ]);

            $total = 0;

            foreach ($request->items as $itemData) {
                $qty = (int) $itemData['quantity'];
                $cost = (float) $itemData['unit_cost'];

                if ($qty < 1 || $cost < 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Kuantitas atau harga beli per item tidak valid.',
                    ]);
                }

                $subtotal = $qty * $cost;

                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_cost'  => $cost,
                    'subtotal'   => $subtotal,
                ]);

                // Update stok produk otomatis
                $product->increment('stock', $qty);

                // Update harga beli produk terakhir
                $product->update(['purchase_price' => $cost]);

                // Catat audit stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'IN',
                    'quantity'   => $qty,
                    'notes'      => 'Pembelian ' . $purchaseNumber,
                ]);

                $total += $subtotal;
            }

            // Hitung total di server
            $purchase->update(['total' => $total]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Transaksi pembelian ' . $purchase->purchase_number . ' berhasil disimpan dan stok telah bertambah.');
    }

    /**
     *  Detail Pembelian
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     *  Pembatalan Transaksi oleh Admin
     * Membatalkan transaksi dan memulihkan stok secara konsisten (mengurangi stok yang sebelumnya dibeli)
     */
    public function cancel(Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'Transaksi pembelian ini sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                // Periksa apakah stok produk mencukupi untuk dikurangi kembali
                foreach ($purchase->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Gagal membatalkan pembelian: Stok produk {$product->name} tersisa {$product->stock}, tidak cukup untuk dikurangi {$item->quantity}.");
                    }
                    $product->decrement('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id'    => auth()->id(),
                        'type'       => 'OUT',
                        'quantity'   => $item->quantity,
                        'notes'      => 'Pembatalan Pembelian ' . $purchase->purchase_number,
                    ]);
                }

                $purchase->update(['status' => 'cancelled']);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Transaksi pembelian berhasil dibatalkan dan stok produk telah disesuaikan kembali.');
    }
}
