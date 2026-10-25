<?php




namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     *  Listing produk dengan Search, Filter Kategori, Sorting, dan Pagination
     */
    public function index(Request $request)
    {
        $allowedSorts = ['name', 'sku', 'selling_price', 'purchase_price', 'stock', 'created_at'];
        $sortBy = in_array($request->sort, $allowedSorts) ? $request->sort : 'name';
        $sortOrder = in_array(strtolower($request->order), ['asc', 'desc']) ? strtolower($request->order) : 'asc';

        $products = Product::with(['category', 'suppliers'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->stock_status === 'low', function ($query) {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'sortBy', 'sortOrder'));
    }

    /**
     *  Form create produk
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     *  Validasi Server-side dan Penyimpanan Produk
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'            => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name'           => ['required', 'string', 'max:150'],
            'category_id'    => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'minimum_stock'  => ['required', 'integer', 'min:0'],
            'supplier_id'    => ['nullable', 'exists:suppliers,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'category_id'    => $validated['category_id'],
                'sku'            => strtoupper(trim($validated['sku'])),
                'name'           => trim($validated['name']),
                'purchase_price' => $validated['purchase_price'],
                'selling_price'  => $validated['selling_price'],
                'stock'          => $validated['stock'],
                'minimum_stock'  => $validated['minimum_stock'],
            ]);

            if (!empty($validated['supplier_id'])) {
                $product->suppliers()->attach($validated['supplier_id'], [
                    'supplier_price' => $validated['purchase_price'],
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     *  Detail produk
     */
    public function show(Product $product)
    {
        $product->load(['category', 'suppliers', 'purchaseItems.purchase.supplier', 'saleItems.sale.user']);

        return view('products.show', compact('product'));
    }

    /**
     *  Form edit produk
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $product->load('suppliers');

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     *  Update data produk
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku'            => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name'           => ['required', 'string', 'max:150'],
            'category_id'    => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'minimum_stock'  => ['required', 'integer', 'min:0'],
            'supplier_id'    => ['nullable', 'exists:suppliers,id'],
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->update([
                'category_id'    => $validated['category_id'],
                'sku'            => strtoupper(trim($validated['sku'])),
                'name'           => trim($validated['name']),
                'purchase_price' => $validated['purchase_price'],
                'selling_price'  => $validated['selling_price'],
                'stock'          => $validated['stock'],
                'minimum_stock'  => $validated['minimum_stock'],
            ]);

            if (isset($validated['supplier_id'])) {
                if ($validated['supplier_id']) {
                    $product->suppliers()->sync([
                        $validated['supplier_id'] => ['supplier_price' => $validated['purchase_price']]
                    ]);
                } else {
                    $product->suppliers()->detach();
                }
            }
        });

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     *  Aturan Hapus Produk (RESTRICT jika sudah ada transaksi)
     */
    public function destroy(Product $product)
    {
        if ($product->purchaseItems()->count() > 0 || $product->saleItems()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah tercatat dalam transaksi pembelian atau penjualan (Data Integrity Protection).');
        }

        $product->suppliers()->detach();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
