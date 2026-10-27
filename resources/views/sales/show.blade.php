@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Rincian Transaksi Penjualan</h1>
      <p style="color: #64748b; margin: 0.25rem 0 0; font-size: 0.875rem;">
        No. Invoice / Faktur: <strong style="font-family: monospace; color: #16a34a;">{{ $sale->sale_number }}</strong>
      </p>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      @if($sale->status === 'completed')
        <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="btn btn-primary" style="background: #16a34a; border-color: #15803d;">
          🖨️ Cetak Struk Penjualan
        </a>
      @endif

      @if($sale->status === 'pending')
        <form action="{{ route('sales.confirm', $sale) }}" method="POST" onsubmit="return confirm('Konfirmasi pesanan ini dan potong stok gudang sekarang?');" style="margin: 0;">
          @csrf
          <button type="submit" class="btn btn-primary" style="background: #2563eb; border-color: #1d4ed8;">
            ✅ Konfirmasi &amp; Proses Pesanan
          </button>
        </form>
        <form action="{{ route('sales.reject', $sale) }}" method="POST" onsubmit="return confirm('Tolak pesanan pelanggan ini?');" style="margin: 0;">
          @csrf
          <button type="submit" class="btn btn-danger">
            ❌ Tolak Pesanan
          </button>
        </form>
      @endif

      <a href="{{ route('sales.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar</a>

      {{-- Hak Akses Khusus Admin untuk Membatalkan Penjualan yang sudah Selesai --}}
      @if(auth()->user()->isAdmin() && $sale->status === 'completed')
        <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('PERINGATAN: Membatalkan penjualan ini akan mengembalikan stok produk ke gudang. Lanjutkan pembatalan?');" style="margin: 0;">
          @csrf
          <button type="submit" class="btn btn-danger">
            ✕ Batalkan Penjualan (Kembalikan Stok)
          </button>
        </form>
      @endif
    </div>
  </div>

  @if($sale->status === 'pending')
    <div class="card" style="background: #fffbeb; border-left: 4px solid #f59e0b; margin-bottom: 1.5rem;">
      <div style="display: flex; gap: 1rem; align-items: center;">
        <div style="font-size: 2rem;">⏳</div>
        <div>
          <h3 style="margin: 0 0 0.25rem; color: #b45309; font-size: 1.05rem;">Pesanan Baru Masuk dari Pelanggan</h3>
          <p style="margin: 0; color: #78350f; font-size: 0.875rem;">
            Pesanan ini sedang berstatus <strong>Pending</strong>. Stok gudang belum dipotong sampai kasir menekan tombol <strong>Konfirmasi &amp; Proses Pesanan</strong> di atas.
          </p>
        </div>
      </div>
    </div>
  @endif

  <!-- Header Details Card -->
  <div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Pelanggan / Pembeli</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">
          {{ $sale->customer_name ?: ($sale->user && $sale->user->isCustomer() ? $sale->user->name : 'Pelanggan Umum') }}
        </strong>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Petugas / Kasir</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">
          {{ $sale->user && !$sale->user->isCustomer() ? $sale->user->name : 'Pesanan Online (Menunggu Kasir)' }}
        </strong>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Waktu Transaksi</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">{{ $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : '-' }}</strong>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Status Transaksi</span>
        <div style="margin-top: 0.25rem;">
          @if($sale->status === 'pending')
            <span class="badge badge-warning" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.875rem;">
              ⏳ Menunggu Konfirmasi (Pending)
            </span>
          @elseif($sale->status === 'completed')
            <span class="badge badge-success" style="font-size: 0.875rem;">Selesai (Completed)</span>
          @else
            <span class="badge badge-danger" style="font-size: 0.875rem;">Dibatalkan (Cancelled)</span>
          @endif
        </div>
      </div>
    </div>

    @if($sale->notes)
      <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; font-size: 0.875rem; color: #475569;">
        <strong>Catatan Transaksi:</strong> {{ $sale->notes }}
      </div>
    @endif
  </div>

  <!-- Items Table Card -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
      <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Rincian Item Produk</h3>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Produk</th>
            <th>Kategori</th>
            <th style="text-align: center;">Jumlah</th>
            <th style="text-align: right;">Harga Satuan</th>
            <th style="text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sale->items as $item)
            <tr>
              <td>
                <strong style="color: #0f172a;">{{ $item->product->name ?? 'Produk Telah Dihapus' }}</strong>
                <div style="font-size: 0.75rem; color: #64748b; font-family: monospace;">SKU: {{ $item->product->sku ?? '-' }}</div>
              </td>
              <td>{{ $item->product->category->name ?? '-' }}</td>
              <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
              <td style="text-align: right; color: #475569;">
                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
              </td>
              <td style="text-align: right; font-weight: 700; color: #16a34a;">
                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background: #f8fafc; font-weight: 700;">
            <td colspan="4" style="text-align: right; padding: 1rem; font-size: 1rem;">Total Pembayaran:</td>
            <td style="text-align: right; font-size: 1.25rem; color: #16a34a; padding: 1rem;">
              Rp {{ number_format($sale->total, 0, ',', '.') }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
