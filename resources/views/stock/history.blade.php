
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Audit Trail Riwayat Mutasi Stok</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Audit log transaksi stok masuk (Pembelian/Restock) dan keluar (Penjualan/Keluar).</p>
  </div>
  <div style="display: flex; gap: 0.5rem;">
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Pembelian (Stock-In)</a>
    <a href="{{ route('sales.create') }}" class="btn btn-success">+ Penjualan (Stock-Out)</a>
  </div>
</div>

<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('stock.history') }}" style="display: grid; grid-template-columns: 2fr 1.5fr 100px; gap: 0.75rem; align-items: end;">
    <div>
      <label class="form-label">Cari Nama / SKU Produk</label>
      <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
    </div>
    <div>
      <label class="form-label">Tipe Transaksi</label>
      <select name="type" class="form-select">
        <option value="">Semua Tipe (IN &amp; OUT)</option>
        <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>Stok Masuk (IN)</option>
        <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>Stok Keluar (OUT)</option>
      </select>
    </div>
    <div>
      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Filter</button>
    </div>
  </form>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th style="width: 170px;">Waktu Transaksi</th>
          <th>SKU &amp; Nama Produk</th>
          <th style="text-align: center; width: 120px;">Jenis Mutasi</th>
          <th style="text-align: center; width: 100px;">Jumlah</th>
          <th>Petugas / User</th>
          <th>Keterangan / Catatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($movements as $m)
          <tr>
            <td style="color: #64748b; font-size: 0.8125rem;">
              {{ $m->created_at->format('d/m/Y H:i:s') }}
            </td>
            <td>
              @if($m->product)
                <a href="{{ route('products.show', $m->product) }}" style="color: #0f172a; font-weight: 600; text-decoration: none;">
                  <span style="font-family: monospace; color: #3b82f6;">[{{ $m->product->sku }}]</span> {{ $m->product->name }}
                </a>
              @else
                <span style="color: #94a3b8;">Produk Dihapus</span>
              @endif
            </td>
            <td style="text-align: center;">
              @if($m->type === 'IN')
                <span class="badge badge-success">+ MASUK (IN)</span>
              @else
                <span class="badge badge-danger">- KELUAR (OUT)</span>
              @endif
            </td>
            <td style="text-align: center; font-weight: 700; font-size: 0.95rem; color: {{ $m->type === 'IN' ? '#16a34a' : '#dc2626' }};">
              {{ $m->type === 'IN' ? '+' : '-' }}{{ $m->quantity }}
            </td>
            <td>
              <span style="font-size: 0.8125rem; font-weight: 500; color: #475569;">
                {{ $m->user->name ?? 'System' }}
              </span>
            </td>
            <td style="color: #475569; font-size: 0.8125rem;">
              {{ $m->notes ?? '-' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 3rem 1rem;">
              Belum ada histori transaksi mutasi stok.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top: 1rem;">
  {{ $movements->links() }}
</div>
@endsection
