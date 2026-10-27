@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Rincian Pesanan</h1>
      <p style="color: #64748b; margin: 0.25rem 0 0; font-size: 0.875rem;">Nomor Faktur Pesanan: <strong style="font-family: monospace; color: #2563eb;">{{ $order->sale_number }}</strong></p>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">&larr; Kembali ke Pesanan Saya</a>
  </div>

  <!-- Status Notification Card -->
  @if($order->status === 'pending')
    <div class="card" style="background: #fffbeb; border-left: 4px solid #f59e0b; margin-bottom: 1.5rem;">
      <div style="display: flex; gap: 1rem; align-items: center;">
        <div style="font-size: 2rem;">⏳</div>
        <div>
          <h3 style="margin: 0 0 0.25rem; color: #b45309; font-size: 1.1rem;">Menunggu Konfirmasi dari Kasir</h3>
          <p style="margin: 0; color: #78350f; font-size: 0.875rem;">
            Pesanan Anda telah diterima oleh sistem dan sedang menunggu kasir toko untuk memeriksa stok fisik serta mengonfirmasi pesanan.
          </p>
        </div>
      </div>
    </div>
  @elseif($order->status === 'completed')
    <div class="card" style="background: #f0fdf4; border-left: 4px solid #22c55e; margin-bottom: 1.5rem;">
      <div style="display: flex; gap: 1rem; align-items: center;">
        <div style="font-size: 2rem;">✅</div>
        <div>
          <h3 style="margin: 0 0 0.25rem; color: #15803d; font-size: 1.1rem;">Pesanan Dikonfirmasi &amp; Siap Diambil / Dikirim</h3>
          <p style="margin: 0; color: #166534; font-size: 0.875rem;">
            Kasir telah menyetujui pesanan Anda. Barang telah dipotong dari stok dan disiapkan untuk Anda.
          </p>
        </div>
      </div>
    </div>
  @elseif($order->status === 'cancelled')
    <div class="card" style="background: #fef2f2; border-left: 4px solid #ef4444; margin-bottom: 1.5rem;">
      <div style="display: flex; gap: 1rem; align-items: center;">
        <div style="font-size: 2rem;">❌</div>
        <div>
          <h3 style="margin: 0 0 0.25rem; color: #b91c1c; font-size: 1.1rem;">Pesanan Ditolak / Dibatalkan</h3>
          <p style="margin: 0; color: #991b1b; font-size: 0.875rem;">
            {{ $order->notes ?? 'Mohon maaf, pesanan ini tidak dapat diproses oleh toko.' }}
          </p>
        </div>
      </div>
    </div>
  @endif

  <!-- Order Items Card -->
  <div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="margin: 0 0 1rem; font-size: 1.05rem; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
      Item yang Dipesan
    </h3>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Produk</th>
            <th style="text-align: center;">Kuantitas</th>
            <th style="text-align: right;">Harga Satuan</th>
            <th style="text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
            <tr>
              <td>
                <strong>{{ $item->product->name ?? 'Produk Dihapus' }}</strong>
                <div style="font-size: 0.75rem; color: #64748b; font-family: monospace;">SKU: {{ $item->product->sku ?? '-' }}</div>
              </td>
              <td style="text-align: center; font-weight: 600;">
                {{ $item->quantity }}
              </td>
              <td style="text-align: right;">
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
            <td colspan="3" style="text-align: right; padding: 1rem;">Total Biaya:</td>
            <td style="text-align: right; font-size: 1.25rem; color: #16a34a; padding: 1rem;">
              Rp {{ number_format($order->total, 0, ',', '.') }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
