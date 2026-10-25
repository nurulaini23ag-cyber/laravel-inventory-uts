
@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Rincian Transaksi Pembelian</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">
        No. PO: <strong style="font-family: monospace; color: #2563eb;">{{ $purchase->purchase_number }}</strong>
      </p>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center;">
      <a href="{{ route('purchases.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar</a>

      {{-- Hak Akses Khusus Admin untuk Membatalkan Transaksi --}}
      @if(auth()->user()->isAdmin() && $purchase->status === 'completed')
        <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('PERINGATAN: Membatalkan pembelian ini akan mengurangi kembali stok produk yang sebelumnya bertambah. Lanjutkan pembatalan?');" style="margin: 0;">
          @csrf
          <button type="submit" class="btn btn-danger">
            ✕ Batalkan Pembelian (Kurangi Stok)
          </button>
        </form>
      @endif
    </div>
  </div>

  <!-- Header Details Card -->
  <div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Supplier / Pemasok</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">{{ $purchase->supplier->name ?? '-' }}</strong>
        <span style="font-size: 0.75rem; color: #64748b; display: block;">{{ $purchase->supplier->phone ?? '' }} | {{ $purchase->supplier->email ?? '' }}</span>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Waktu Transaksi</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">{{ $purchase->purchased_at ? $purchase->purchased_at->format('d/m/Y H:i') : '-' }}</strong>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Petugas Penerima</span>
        <strong style="font-size: 1.05rem; color: #0f172a;">{{ $purchase->user->name ?? 'System' }}</strong>
      </div>
      <div>
        <span style="font-size: 0.8125rem; color: #64748b; display: block;">Status Pembelian</span>
        <div style="margin-top: 0.25rem;">
          @if($purchase->status === 'completed')
            <span class="badge badge-success" style="font-size: 0.875rem;">Selesai (Completed)</span>
          @else
            <span class="badge badge-danger" style="font-size: 0.875rem;">Dibatalkan (Cancelled)</span>
          @endif
        </div>
      </div>
    </div>

    @if($purchase->notes)
      <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; font-size: 0.875rem; color: #475569;">
        <strong>Catatan / Keterangan:</strong> {{ $purchase->notes }}
      </div>
    @endif
  </div>

  <!-- Items Table -->
  <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    <div style="padding: 0.85rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">Rincian Item Barang Pembelian (Stock-In)</h3>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th style="width: 140px;">SKU</th>
            <th>Nama Produk</th>
            <th style="width: 120px; text-align: center;">Jumlah Masuk</th>
            <th style="width: 180px; text-align: right;">Harga Beli Satuan</th>
            <th style="width: 180px; text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($purchase->items as $item)
            <tr>
              <td style="font-family: monospace; font-weight: 600; color: #2563eb;">
                {{ $item->product->sku ?? '-' }}
              </td>
              <td>
                <strong>{{ $item->product->name ?? 'Produk Dihapus' }}</strong>
              </td>
              <td style="text-align: center; font-weight: 700; color: #16a34a; font-size: 1rem;">
                +{{ $item->quantity }}
              </td>
              <td style="text-align: right; color: #475569;">
                Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
              </td>
              <td style="text-align: right; font-weight: 700; color: #0f172a;">
                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background: #f8fafc; font-weight: 700;">
            <td colspan="4" style="text-align: right; padding: 1rem; font-size: 1rem;">TOTAL PEMBELIAN:</td>
            <td style="text-align: right; padding: 1rem; font-size: 1.2rem; color: #2563eb;">
              Rp {{ number_format($purchase->total, 0, ',', '.') }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
