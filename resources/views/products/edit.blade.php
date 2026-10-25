
@extends('layouts.app')

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Edit Produk</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Perbarui rincian informasi produk {{ $product->name }}.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('products.update', $product) }}">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label class="form-label" for="category_id">Kategori Produk <span style="color: #dc2626;">*</span></label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
          <label class="form-label" for="sku">SKU / Kode Produk <span style="color: #dc2626;">*</span></label>
          <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
          @error('sku')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="name">Nama Produk <span style="color: #dc2626;">*</span></label>
          <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
          @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="purchase_price">Harga Beli / HPP (Rp) <span style="color: #dc2626;">*</span></label>
          <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" required>
          @error('purchase_price')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="selling_price">Harga Jual (Rp) <span style="color: #dc2626;">*</span></label>
          <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', $product->selling_price) }}" min="0" required>
          @error('selling_price')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="stock">Jumlah Stok <span style="color: #dc2626;">*</span></label>
          <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" min="0" required>
          @error('stock')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="minimum_stock">Batas Minimum Stok <span style="color: #dc2626;">*</span></label>
          <input type="number" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', $product->minimum_stock) }}" min="0" required>
          @error('minimum_stock')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      @php
        $attachedSupplier = $product->suppliers->first();
      @endphp
      <div class="form-group">
        <label class="form-label" for="supplier_id">Pemasok Utama</label>
        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
          <option value="">-- Tanpa Supplier --</option>
          @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ old('supplier_id', $attachedSupplier->id ?? null) == $supplier->id ? 'selected' : '' }}>
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
        <button type="submit" class="btn btn-primary">Perbarui Data Produk</button>
      </div>
    </form>
  </div>
</div>
@endsection
