<?php


namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;

class DashboardController extends Controller
{
    /**
     * Hitung metrik operasional secara real-time dari database menggunakan fungsi agregasi
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->isCustomer()) {
            return redirect()->route('orders.index');
        }
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalStock = Product::sum('stock');
        $totalValuation = Product::selectRaw('SUM(stock * selling_price) as total_val')->value('total_val') ?? 0;
        $lowStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')->count();

        //  Penjualan Hari Ini & Bulan Ini
        $salesToday = Sale::where('status', 'completed')
            ->whereDate('sold_at', today())
            ->sum('total');

        $salesThisMonth = Sale::where('status', 'completed')
            ->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year)
            ->sum('total');

        $totalCompletedSales = Sale::where('status', 'completed')->count();
        $totalCompletedPurchases = Purchase::where('status', 'completed')->count();

        // Produk Stok Rendah
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        // 5 Transaksi Penjualan Terakhir
        $recentSales = Sale::with(['user', 'items.product'])
            ->latest('sold_at')
            ->take(5)
            ->get();

        // 5 Transaksi Pembelian Terakhir
        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->latest('purchased_at')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalStock',
            'totalValuation',
            'lowStockCount',
            'salesToday',
            'salesThisMonth',
            'totalCompletedSales',
            'totalCompletedPurchases',
            'lowStockProducts',
            'recentSales',
            'recentPurchases'
        ));
    }
}

