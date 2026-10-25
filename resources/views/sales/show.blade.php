
@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Rincian Transaksi Penjualan</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">
        No. Invoice: <strong style="font-family: monospace; color: #16a34a;">{{ $sale->sale_number }}</strong>
      </p>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center;">
      <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="btn btn-primary" style="background: #16a34a; border-color: #15803d;">
        🖨️ Cetak Struk Penjualan
      </a>
      <a href="{{ route('sales.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar</a>

      {{-- Hak Akses Khusus Admin untuk Membatalkan Penjualan --}}
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

  <!-- Header Details Card -->
  <div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Kasir / Petugas</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">{{ $sale->user->name ?? 'System' }}</strong>
        <span style="font-size: 0.75rem; color: #64748b; display: block;">{{ $sale->user->email ?? '' }}</span>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Waktu Penjualan</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">{{ $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : '-' }}</strong>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Status Penjualan</span>
        <div style="margin-top: 0.25rem;">
          @if($sale->status === 'completed')
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

  <!-- Items Table -->
  <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    <div style="padding: 0.85rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">Rincian Item Produk Terjual (Stock-Out)</h3>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th style="width: 140px;">SKU</th>
            <th>Nama Produk</th>
            <th style="width: 120px; text-align: center;">Jumlah Terjual</th>
            <th style="width: 180px; text-align: right;">Harga Satuan</th>
            <th style="width: 180px; text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sale->items as $item)
            <tr>
              <td style="font-family: monospace; font-weight: 600; color: #2563eb;">
                {{ $item->product->sku ?? '-' }}
              </td>
              <td>
                <strong>{{ $item->product->name ?? 'Produk Dihapus' }}</strong>
              </td>
              <td style="text-align: center; font-weight: 700; color: #dc2626; font-size: 1rem;">
                -{{ $item->quantity }}
              </td>
              <td style="text-align: right; color: #475569;">
                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
              </td>
              <td style="text-align: right; font-weight: 700; color: #0f172a;">
                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background: #f8fafc; font-weight: 700;">
            <td colspan="4" style="text-align: right; padding: 1rem; font-size: 1rem;">TOTAL PENJUALAN:</td>
            <td style="text-align: right; padding: 1rem; font-size: 1.2rem; color: #16a34a;">
              Rp {{ number_format($sale->total, 0, ',', '.') }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
