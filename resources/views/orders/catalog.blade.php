@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">🛍️ Katalog Produk</h1>
    <p style="color: #64748b; margin: 0.25rem 0 0; font-size: 0.875rem;">Pilih produk aksesoris laptop yang Anda butuhkan dan lakukan pemesanan secara mudah.</p>
  </div>
  <a href="{{ route('orders.create') }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
    ➕ Buat Pesanan Baru
  </a>
</div>

<!-- Filter & Search Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
  <form method="GET" action="{{ route('orders.catalog') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
    <div style="flex: 2; min-width: 220px;">
      <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode SKU..." value="{{ request('search') }}">
    </div>
    <div style="flex: 1; min-width: 180px;">
      <select name="category_id" class="form-select">
        <option value="">-- Semua Kategori --</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
            {{ $cat->name }}
          </option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    @if(request()->hasAny(['search', 'category_id']))
      <a href="{{ route('orders.catalog') }}" class="btn btn-secondary">Reset</a>
    @endif
  </form>
</div>

<!-- Product Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
  @forelse($products as $product)
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <span style="font-size: 0.75rem; font-family: monospace; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
            {{ $product->sku }}
          </span>
          <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
            {{ $product->category->name ?? 'Aksesoris' }}
          </span>
        </div>

        <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem; line-height: 1.35;">
          {{ $product->name }}
        </h3>

        <div style="margin-bottom: 1rem;">
          <div style="font-size: 1.35rem; font-weight: 700; color: #16a34a;">
            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
          </div>
          <div style="font-size: 0.8125rem; color: {{ $product->stock > 5 ? '#059669' : '#dc2626' }}; margin-top: 0.25rem; font-weight: 600;">
            @if($product->stock > 0)
              ● Tersedia: {{ $product->stock }} unit
            @else
              ❌ Stok Habis
            @endif
          </div>
        </div>
      </div>

      <div>
        @if($product->stock > 0)
          <a href="{{ route('orders.create', ['product_id' => $product->id]) }}" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.55rem; font-weight: 600;">
            🛒 Pesan Sekarang
          </a>
        @else
          <button disabled class="btn btn-secondary" style="width: 100%; justify-content: center; padding: 0.55rem; opacity: 0.6; cursor: not-allowed;">
            Stok Kosong
          </button>
        @endif
      </div>
    </div>
  @empty
    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem;">
      <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔍</div>
      <h3 style="color: #475569; margin: 0 0 0.5rem;">Produk Tidak Ditemukan</h3>
      <p style="color: #94a3b8; margin: 0;">Silakan coba kata kunci pencarian atau kategori lain.</p>
    </div>
  @endforelse
</div>

<div style="margin-top: 1.5rem;">
  {{ $products->links() }}
</div>
@endsection
