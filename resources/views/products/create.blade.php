
@extends('layouts.app')

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Tambah Produk Baru</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Mendaftarkan master data produk baru ke dalam sistem inventaris.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('products.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="category_id">Kategori Produk <span style="color: #dc2626;">*</span></label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
        @error('category_id')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="sku">SKU / Kode Unik <span style="color: #dc2626;">*</span></label>
          <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Contoh: ELEC-002" required>
          @error('sku')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="name">Nama Produk <span style="color: #dc2626;">*</span></label>
          <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Wireless Mechanical Keyboard" required>
          @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="purchase_price">Harga Beli / HPP (Rp) <span style="color: #dc2626;">*</span></label>
          <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price') }}" placeholder="0" min="0" required>
          @error('purchase_price')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="selling_price">Harga Jual (Rp) <span style="color: #dc2626;">*</span></label>
          <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price') }}" placeholder="0" min="0" required>
          @error('selling_price')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="stock">Stok Awal <span style="color: #dc2626;">*</span></label>
          <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" required>
          @error('stock')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="minimum_stock">Batas Minimum Stok <span style="color: #dc2626;">*</span></label>
          <input type="number" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', 5) }}" min="0" required>
          @error('minimum_stock')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="supplier_id">Pemasok Utama (Opsional)</label>
        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
          <option value="">-- Tanpa Supplier --</option>
          @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
              {{ $supplier->name }}
            </option>
          @endforeach
        </select>
        @error('supplier_id')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Master Produk</button>
      </div>
    </form>
  </div>
</div>
@endsection
