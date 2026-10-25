<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function history(Request $request)
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', strtoupper($type));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock.history', compact('movements'));
    }

    public function createIn()
    {
        $products = Product::orderBy('name')->get();
        return view('stock.in', compact('products'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $product->increment('stock', $validated['quantity']);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id'    => auth()->id(),
                'type'       => 'IN',
                'quantity'   => $validated['quantity'],
                'notes'      => $validated['notes'] ?? 'Transaksi Stok Masuk',
            ]);
        });

        return redirect()->route('products.index')
            ->with('success', 'Stok masuk berhasil dicatat!');
    }

    public function createOut()
    {
        $products = Product::orderBy('name')->get();
        return view('stock.out', compact('products'));
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

                if ($product->stock < $validated['quantity']) {
                    throw new \Exception("Stok tidak mencukupi! Stok saat ini: {$product->stock}, jumlah keluar: {$validated['quantity']}");
                }

                $product->decrement('stock', $validated['quantity']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'OUT',
                    'quantity'   => $validated['quantity'],
                    'notes'      => $validated['notes'] ?? 'Transaksi Stok Keluar',
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('products.index')
            ->with('success', 'Stok keluar berhasil dicatat!');
    }
}
