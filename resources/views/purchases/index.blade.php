
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Transaksi Pembelian Stok (Purchases / Stock-In)</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Mencatat pengadaan barang dari supplier dan penambahan stok secara otomatis.</p>
  </div>
  <div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Buat Pembelian Baru</a>
  </div>
</div>


<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('purchases.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 0.75rem; align-items: end;">
    <div>
      <label class="form-label">Cari No. PO / Supplier</label>
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
      <a href="{{ route('purchases.index') }}" style="color: #64748b; text-decoration: underline;">Reset Filter</a>
    </div>
  @endif
</div>

<!-- Table Data -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th style="width: 170px;">No. Transaksi (PO)</th>
          <th>Supplier</th>
          <th style="width: 150px;">Tanggal Pembelian</th>
          <th style="text-align: right;">Total Nilai</th>
          <th>Petugas / User</th>
          <th style="text-align: center; width: 120px;">Status</th>
          <th style="text-align: right; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($purchases as $p)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #2563eb;">
              <a href="{{ route('purchases.show', $p) }}" style="color: #2563eb; text-decoration: none;">
                {{ $p->purchase_number }}
              </a>
            </td>
            <td style="font-weight: 600; color: #1e293b;">
              {{ $p->supplier->name ?? 'Pemasok Umum' }}
            </td>
            <td style="color: #475569; font-size: 0.8125rem;">
              {{ $p->purchased_at ? $p->purchased_at->format('d/m/Y H:i') : '-' }}
            </td>
            <td style="text-align: right; font-weight: 700; color: #0f172a;">
              Rp {{ number_format($p->total, 0, ',', '.') }}
            </td>
            <td style="color: #64748b; font-size: 0.8125rem;">
              {{ $p->user->name ?? 'System' }}
            </td>
            <td style="text-align: center;">
              @if($p->status === 'completed')
                <span class="badge badge-success">Selesai</span>
              @else
                <span class="badge badge-danger">Dibatalkan</span>
              @endif
            </td>
            <td style="text-align: right;">
              <a href="{{ route('purchases.show', $p) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Detail &rarr;
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📥</div>
              <p style="margin: 0; font-size: 1rem; font-weight: 500;">Belum ada riwayat transaksi pembelian stok.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top: 1rem;">
  {{ $purchases->links() }}
</div>
@endsection
