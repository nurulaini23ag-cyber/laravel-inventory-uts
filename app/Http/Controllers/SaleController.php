<?php




namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    /**
     *  Listing Penjualan dengan Search & Filter Tanggal
     */
    public function index(Request $request)
    {
        $sales = Sale::with(['user', 'items.product'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sale_number', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->start_date, function ($query, $startDate) {
                $query->whereDate('sold_at', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                $query->whereDate('sold_at', '<=', $endDate);
            })
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    /**
     *  Form Tambah Penjualan (Multi-item)
     */
    public function create()
    {
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

                $customers = \App\Models\User::whereIn('role', ['user', 'customer'])->orderBy('name')->get();

        return view('sales.create', compact('products', 'customers'));
    }

    /**
     *  Logika Transaksi Penjualan Lengkap
     */
    public function store(Request $request)
    {
        $request->validate([
            'sold_at'            => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $saleNumber = 'INV-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -5));

                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'sale_number' => $saleNumber,
                    'total'       => 0,
                    'status'      => 'completed',
                    'sold_at'     => $request->sold_at,
                    'notes'       => $request->notes,
                ]);

                $total = 0;

                foreach ($request->items as $itemData) {
                    $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                    $qty = (int) $itemData['quantity'];

                    //  Cek kuantitas dan kecukupan stok di server
                    if ($qty < 1 || $product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'items' => "Stok produk '{$product->name}' tidak mencukupi! Tersedia: {$product->stock}, diminta: {$qty}."
                        ]);
                    }

                    // Ambil harga resmi dari database server, bukan percaya input browser
                    $unitPrice = $product->selling_price;
                    $subtotal = $unitPrice * $qty;

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal'   => $subtotal,
                    ]);

                    // Kurangi stok produk
                    $product->decrement('stock', $qty);

                    // Catat audit trail pergerakan stok
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type'       => 'OUT',
                        'quantity'   => $qty,
                        'notes'      => 'Penjualan ' . $saleNumber,
                    ]);

                    $total += $subtotal;
                }

                // Update total transaksi dihitung oleh server
                $sale->update(['total' => $total]);

                return $sale;
            });
        } catch (ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Transaksi penjualan ' . $sale->sale_number . ' berhasil disimpan dan stok otomatis berkurang.');
    }

    /**
     *  Detail Penjualan
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Struk Penjualan Siap Cetak (Thermal / POS Receipt)
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);

        return view('sales.receipt', compact('sale'));
    }

    /**
     *  & 08: Pembatalan Penjualan oleh Admin
     * Membatalkan transaksi dan mengembalikan stok produk yang sebelumnya terjual
     */
    /**
     * Konfirmasi Pesanan Pelanggan oleh Kasir / Staff / Admin
     */
    public function confirm(Sale $sale)
    {
        if ($sale->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan berstatus Pending yang dapat dikonfirmasi.');
        }

        try {
            DB::transaction(function () use ($sale) {
                foreach ($sale->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'stock' => "Gagal konfirmasi: Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}, Dibutuhkan: {$item->quantity})."
                        ]);
                    }

                    $product->decrement('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type'       => 'OUT',
                        'quantity'   => $item->quantity,
                        'notes'      => 'Konfirmasi Pesanan Kasir ' . $sale->sale_number,
                    ]);
                }

                $sale->update(['status' => 'completed']);
            });
        } catch (ValidationException $ve) {
            return back()->withErrors($ve->errors());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', "Pesanan {$sale->sale_number} berhasil dikonfirmasi oleh kasir! Stok telah terpotong dan struk siap dicetak.");
    }

    /**
     * Tolak Pesanan Pelanggan oleh Kasir
     */
    public function reject(Sale $sale, Request $request)
    {
        if ($sale->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan berstatus Pending yang dapat ditolak.');
        }

        $reason = $request->input('reason', 'Ditolak oleh kasir.');

        $sale->update([
            'status' => 'cancelled',
            'notes'  => trim(($sale->notes ? $sale->notes . ' | ' : '') . 'Alasan Ditolak: ' . $reason),
        ]);

        return redirect()->route('sales.index')
            ->with('success', "Pesanan {$sale->sale_number} telah ditolak.");
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Transaksi penjualan ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                // Kembalikan stok
                $product->increment('stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type'       => 'IN',
                    'quantity'   => $item->quantity,
                    'notes'      => 'Pembatalan Penjualan ' . $sale->sale_number,
                ]);
            }

            $sale->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Transaksi penjualan berhasil dibatalkan dan stok produk telah dikembalikan.');
    }
}


