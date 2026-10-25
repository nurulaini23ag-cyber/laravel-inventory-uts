
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Katalog Master Produk</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Kelola seluruh inventaris, harga beli (HPP), harga jual, dan stok barang.</p>
  </div>
  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Pembelian (Stock-In)</a>
    <a href="{{ route('sales.create') }}" class="btn btn-success">+ Penjualan (Stock-Out)</a>
    @if(auth()->user()->isAdmin())
      <a href="{{ route('products.create') }}" class="btn btn-warning">+ Tambah Master Produk</a>
    @endif
  </div>
</div>


<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('products.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 0.75rem; align-items: end;">
    <div>
      <label class="form-label">Cari Nama / SKU</label>
      <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="{{ request('search') }}">
    </div>
    <div>
      <label class="form-label">Filter Kategori</label>
      <select name="category_id" class="form-select">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
            {{ $cat->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="form-label">Status Stok</label>
      <select name="stock_status" class="form-select">
        <option value="">Semua Status</option>
        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
      </select>
    </div>
    <div>
      <label class="form-label">Urutkan</label>
      <select name="sort" class="form-select">
        <option value="name" {{ ($sortBy ?? '') == 'name' ? 'selected' : '' }}>Nama Produk</option>
        <option value="sku" {{ ($sortBy ?? '') == 'sku' ? 'selected' : '' }}>SKU</option>
        <option value="selling_price" {{ ($sortBy ?? '') == 'selling_price' ? 'selected' : '' }}>Harga Jual</option>
        <option value="stock" {{ ($sortBy ?? '') == 'stock' ? 'selected' : '' }}>Stok</option>
        <option value="created_at" {{ ($sortBy ?? '') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
      </select>
    </div>
    <div>
      <label class="form-label">Arah</label>
      <select name="order" class="form-select">
        <option value="asc" {{ ($sortOrder ?? '') == 'asc' ? 'selected' : '' }}>A-Z / Rendah</option>
        <option value="desc" {{ ($sortOrder ?? '') == 'desc' ? 'selected' : '' }}>Z-A / Tinggi</option>
      </select>
    </div>
    <div>
      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Filter</button>
    </div>
  </form>
  @if(request()->hasAny(['search', 'category_id', 'stock_status', 'sort', 'order']))
    <div style="margin-top: 0.75rem; font-size: 0.8125rem;">
      <a href="{{ route('products.index') }}" style="color: #64748b; text-decoration: underline;">Reset Filter</a>
    </div>
  @endif
</div>

<!-- Table Data -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th style="width: 120px;">SKU</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th style="text-align: right;">Harga Beli</th>
          <th style="text-align: right;">Harga Jual</th>
          <th style="text-align: center;">Stok</th>
          <th style="text-align: center;">Status</th>
          <th style="text-align: right; width: 180px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td style="font-family: monospace; font-weight: 600; color: #2563eb;">
              {{ $product->sku }}
            </td>
            <td>
              <a href="{{ route('products.show', $product) }}" style="color: #0f172a; font-weight: 600; text-decoration: none;">
                {{ $product->name }}
              </a>
            </td>
            <td>
              <span class="badge badge-info">{{ $product->category->name ?? 'Uncategorized' }}</span>
            </td>
            <td style="text-align: right; color: #64748b; font-size: 0.8125rem;">
              Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
            </td>
            <td style="text-align: right; font-weight: 600; color: #16a34a;">
              Rp {{ number_format($product->selling_price, 0, ',', '.') }}
            </td>
            <td style="text-align: center; font-weight: 700; font-size: 0.95rem;">
              {{ $product->stock }}
            </td>
            <td style="text-align: center;">
              @if($product->stock <= $product->minimum_stock)
                <span class="badge badge-danger">Menipis (&le; {{ $product->minimum_stock }})</span>
              @else
                <span class="badge badge-success">Aman</span>
              @endif
            </td>
            <td style="text-align: right;">
              <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                <a href="{{ route('products.show', $product) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Detail</a>
                @if(auth()->user()->isAdmin())
                  <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #2563eb;">Edit</a>
                  <form action="{{ route('products.destroy', $product) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Hapus</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📦</div>
              <p style="margin: 0; font-size: 1rem; font-weight: 500;">Belum ada produk yang ditemukan.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination Links -->
<div style="margin-top: 1rem;">
  {{ $products->links() }}
</div>
@endsection
