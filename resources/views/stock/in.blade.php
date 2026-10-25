@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Catat Stok Masuk</h1>
      <p style="color: #64748b; margin: 0.25rem 0 0 0; font-size: 0.875rem;">Penambahan stok barang masuk ke dalam gudang inventaris.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('stock.in.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="product_id">Pilih Produk <span style="color: #dc2626;">*</span></label>
        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" {{ old('product_id', request('product_id')) == $p->id ? 'selected' : '' }}>
              {{ $p->sku }} — {{ $p->name }} (Stok saat ini: {{ $p->stock }})
            </option>
          @endforeach
        </select>
        @error('product_id')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="quantity">Jumlah Masuk (Unit) <span style="color: #dc2626;">*</span></label>
        <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
        @error('quantity')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="notes">Catatan / Keterangan</label>
        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Contoh: Penerimaan barang dari PO-102">{{ old('notes') }}</textarea>
        @error('notes')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-success">+ Simpan Stok Masuk</button>
      </div>
    </form>
  </div>
</div>
@endsection
