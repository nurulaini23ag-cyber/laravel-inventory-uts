@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Transaksi Penjualan &amp; Pesanan Masuk</h1>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Kelola penjualan kasir langsung (POS) dan konfirmasi pesanan masuk dari pelanggan.</p>
  </div>
  <div style="display: flex; gap: 0.5rem;">
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
      + Kasir / Penjualan Baru
    </a>
  </div>
</div>

<!-- Filter Box -->
<div class="card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('sales.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 0.75rem; align-items: end;">
    <div>
      <label class="form-label">Cari No. Faktur / Pelanggan</label>
      <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="{{ request('search') }}">
    </div>
    <div>
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi (Pending)</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Selesai (Completed)</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan (Cancelled)</option>
      </select>
    </div>
    <div>
      <label class="form-label">Dari Tanggal</label>
      <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div>
      <label class="form-label">Sampai Tanggal</label>
      <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div>
      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Filter</button>
    </div>
  </form>
  @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
    <div style="margin-top: 0.75rem; font-size: 0.8125rem;">
      <a href="{{ route('sales.index') }}" style="color: #64748b; text-decoration: underline;">Reset Filter</a>
    </div>
  @endif
</div>

<!-- Table Data -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th style="width: 170px;">No. Invoice / Pesanan</th>
          <th>Pelanggan / Pembeli</th>
          <th>Kasir / Petugas</th>
          <th style="width: 150px;">Tanggal</th>
          <th style="text-align: right;">Total Nilai</th>
          <th style="text-align: center; width: 140px;">Status</th>
          <th style="text-align: right; width: 170px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $s)
          <tr style="{{ $s->status === 'pending' ? 'background: #fffbeb;' : '' }}">
            <td style="font-family: monospace; font-weight: 700; color: #2563eb;">
              <a href="{{ route('sales.show', $s) }}" style="color: #2563eb; text-decoration: none;">
                {{ $s->sale_number }}
              </a>
            </td>
            <td style="font-weight: 600; color: #0f172a;">
              {{ $s->customer_name ?: ($s->user && $s->user->isCustomer() ? $s->user->name : 'Pelanggan Umum') }}
            </td>
            <td style="color: #64748b; font-size: 0.8125rem;">
              {{ $s->user && !$s->user->isCustomer() ? $s->user->name : 'Pesanan Online' }}
            </td>
            <td style="color: #475569; font-size: 0.8125rem;">
              {{ $s->sold_at ? $s->sold_at->format('d/m/Y H:i') : '-' }}
            </td>
            <td style="text-align: right; font-weight: 700; color: #16a34a; font-size: 0.95rem;">
              Rp {{ number_format($s->total, 0, ',', '.') }}
            </td>
            <td style="text-align: center;">
              @if($s->status === 'pending')
                <span class="badge badge-warning" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                  ⏳ Menunggu Konfirmasi
                </span>
              @elseif($s->status === 'completed')
                <span class="badge badge-success">Selesai</span>
              @else
                <span class="badge badge-danger">Dibatalkan</span>
              @endif
            </td>
            <td style="text-align: right;">
              @if($s->status === 'pending')
                <form action="{{ route('sales.confirm', $s) }}" method="POST" style="display: inline;" onsubmit="return confirm('Konfirmasi pesanan ini dan potong stok fisik di gudang?');">
                  @csrf
                  <button type="submit" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #2563eb; border-color: #1d4ed8;" title="Konfirmasi & Proses">
                    ✅ Konfirmasi
                  </button>
                </form>
                <form action="{{ route('sales.reject', $s) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tolak pesanan pelanggan ini?');">
                  @csrf
                  <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Tolak Pesanan">
                    ❌ Tolak
                  </button>
                </form>
              @else
                <a href="{{ route('sales.receipt', $s) }}" target="_blank" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #16a34a; border-color: #15803d; margin-right: 4px;" title="Cetak Struk">
                  🖨️ Struk
                </a>
              @endif
              <a href="{{ route('sales.show', $s) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Detail &rarr;
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🛍️</div>
              <p style="margin: 0; font-size: 1rem; font-weight: 500;">Belum ada riwayat transaksi penjualan.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top: 1.5rem;">
  {{ $sales->links() }}
</div>
@endsection
