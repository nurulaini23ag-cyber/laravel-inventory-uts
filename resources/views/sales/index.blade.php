
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Transaksi Penjualan (Sales / Stock-Out)</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Mencatat transaksi penjualan ritel. Stok barang otomatis berkurang dan dilindungi dari overselling.</p>
  </div>
  <div>
    <a href="{{ route('sales.create') }}" class="btn btn-success">+ Buat Penjualan Baru</a>
  </div>
</div>


<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('sales.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 0.75rem; align-items: end;">
    <div>
      <label class="form-label">Cari No. Invoice / Kasir</label>
      <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="{{ request('search') }}">
    </div>
    <div>
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="">Semua Status</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
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
          <th style="width: 170px;">No. Invoice (Sales)</th>
          <th>Kasir / Petugas</th>
          <th style="width: 150px;">Tanggal Penjualan</th>
          <th style="text-align: right;">Total Nilai</th>
          <th style="text-align: center; width: 120px;">Status</th>
          <th style="text-align: right; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $s)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #2563eb;">
              <a href="{{ route('sales.show', $s) }}" style="color: #2563eb; text-decoration: none;">
                {{ $s->sale_number }}
              </a>
            </td>
            <td style="font-weight: 500; color: #1e293b;">
              {{ $s->user->name ?? 'System' }}
            </td>
            <td style="color: #475569; font-size: 0.8125rem;">
              {{ $s->sold_at ? $s->sold_at->format('d/m/Y H:i') : '-' }}
            </td>
            <td style="text-align: right; font-weight: 700; color: #16a34a; font-size: 0.95rem;">
              Rp {{ number_format($s->total, 0, ',', '.') }}
            </td>
            <td style="text-align: center;">
              @if($s->status === 'completed')
                <span class="badge badge-success">Selesai</span>
              @else
                <span class="badge badge-danger">Dibatalkan</span>
              @endif
            </td>
            <td style="text-align: right;">
              <a href="{{ route('sales.receipt', $s) }}" target="_blank" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #16a34a; border-color: #15803d; margin-right: 4px;" title="Cetak Struk">
                🖨️ Struk
              </a>
              <a href="{{ route('sales.show', $s) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Detail &rarr;
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🛍️</div>
              <p style="margin: 0; font-size: 1rem; font-weight: 500;">Belum ada riwayat transaksi penjualan.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top: 1rem;">
  {{ $sales->links() }}
</div>
@endsection
