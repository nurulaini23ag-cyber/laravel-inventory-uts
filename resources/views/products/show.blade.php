
@extends('layouts.app')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">{{ $product->name }}</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">
        SKU: <span style="font-family: monospace; font-weight: 700; color: #2563eb;">{{ $product->sku }}</span> &bull;
        Kategori: <strong>{{ $product->category->name ?? 'Uncategorized' }}</strong>
      </p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
      <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali</a>
      @if(auth()->user()->isAdmin())
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Produk</a>
      @endif
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <div class="card" style="margin-bottom: 0;">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; color: #0f172a;">Informasi Harga &amp; Stok</h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div>
          <span style="font-size: 0.8125rem; color: #64748b; display: block;">Harga Beli (HPP)</span>
          <strong style="font-size: 1.1rem; color: #475569;">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</strong>
        </div>
        <div>
          <span style="font-size: 0.8125rem; color: #64748b; display: block;">Harga Jual Resmi</span>
          <strong style="font-size: 1.2rem; color: #16a34a;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</strong>
        </div>
        <div>
          <span style="font-size: 0.8125rem; color: #64748b; display: block;">Stok Saat Ini</span>
          <strong style="font-size: 1.5rem; color: {{ $product->stock <= $product->minimum_stock ? '#dc2626' : '#2563eb' }};">{{ $product->stock }} Unit</strong>
        </div>
        <div>
          <span style="font-size: 0.8125rem; color: #64748b; display: block;">Batas Minimum Stok</span>
          <strong style="font-size: 1.1rem; color: #1e293b;">{{ $product->minimum_stock }} Unit</strong>
        </div>
      </div>

      <div style="margin-top: 1.5rem;">
        <h4 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: #475569;">Pemasok Terkait:</h4>
        @if($product->suppliers->count() > 0)
          <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach($product->suppliers as $sup)
              <div style="padding: 0.5rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <strong>{{ $sup->name }}</strong>
                  <span style="color: #64748b; font-size: 0.8125rem; display: block;">{{ $sup->phone }} | {{ $sup->email }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p style="color: #94a3b8; font-size: 0.875rem; margin: 0;">Tidak ada supplier yang terhubung.</p>
        @endif
      </div>
    </div>

    <div class="card" style="margin-bottom: 0;">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; color: #0f172a;">Aksi Transaksi Cepat</h3>
      <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary" style="justify-content: center;">+ Buat Transaksi Pembelian</a>
        <a href="{{ route('sales.create') }}" class="btn btn-success" style="justify-content: center;">+ Buat Transaksi Penjualan</a>
      </div>
      <div style="margin-top: 1.25rem; padding: 0.75rem; background: #f8fafc; border-radius: 6px; font-size: 0.75rem; color: #64748b; border: 1px dashed #cbd5e1;">
        <strong>Catatan Bisnis :</strong><br>
        Penambahan stok dilakukan via menu Pembelian, dan pengurangan stok via menu Penjualan dengan pengecekan stok otomatis.
      </div>
    </div>
  </div>

  <!-- Riwayat Transaksi Item Penjualan -->
  <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">Riwayat Penjualan Produk Ini</h3>

    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>No. Invoice</th>
            <th>Waktu</th>
            <th style="text-align: center;">Jumlah Terjual</th>
            <th style="text-align: right;">Harga Satuan</th>
            <th style="text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @forelse($product->saleItems as $sItem)
            <tr>
              <td>
                <a href="{{ route('sales.show', $sItem->sale) }}" style="font-family: monospace; font-weight: 600; color: #2563eb; text-decoration: none;">
                  {{ $sItem->sale->sale_number ?? '-' }}
                </a>
              </td>
              <td style="color: #64748b; font-size: 0.8125rem;">
                {{ $sItem->sale->sold_at ? $sItem->sale->sold_at->format('d/m/Y H:i') : '-' }}
              </td>
              <td style="text-align: center; font-weight: 700; color: #dc2626;">{{ $sItem->quantity }}</td>
              <td style="text-align: right;">Rp {{ number_format($sItem->unit_price, 0, ',', '.') }}</td>
              <td style="text-align: right; font-weight: 600;">Rp {{ number_format($sItem->subtotal, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Belum ada riwayat penjualan produk ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
