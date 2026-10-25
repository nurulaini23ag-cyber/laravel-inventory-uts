
@extends('layouts.app')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.875rem; font-weight: 700; color: #0f172a; margin: 0;">Dashboard Inventaris &amp; Penjualan</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Ringkasan metrik statistik operasional, pergerakan stok, dan transaksi penjualan secara real-time.</p>
  </div>
  <div style="display: flex; gap: 0.5rem;">
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Pembelian Baru (PO)</a>
    <a href="{{ route('sales.create') }}" class="btn btn-success">+ Penjualan Baru</a>
  </div>
</div>

<!-- Stat Cards Widgets -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
  <div class="card" style="margin-bottom: 0; border-left: 4px solid #2563eb;">
    <span style="font-size: 0.8125rem; color: #64748b; font-weight: 600;">TOTAL PRODUK</span>
    <div style="font-size: 1.875rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $totalProducts }}</div>
    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">{{ $totalCategories }} kategori &bull; {{ $totalSuppliers }} supplier</div>
  </div>

  <div class="card" style="margin-bottom: 0; border-left: 4px solid #16a34a;">
    <span style="font-size: 0.8125rem; color: #64748b; font-weight: 600;">PENJUALAN BULAN INI</span>
    <div style="font-size: 1.7rem; font-weight: 700; color: #16a34a; margin-top: 0.25rem;">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</div>
    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Hari ini: <strong>Rp {{ number_format($salesToday, 0, ',', '.') }}</strong></div>
  </div>

  <div class="card" style="margin-bottom: 0; border-left: 4px solid #0891b2;">
    <span style="font-size: 0.8125rem; color: #64748b; font-weight: 600;">TOTAL TRANSAKSI</span>
    <div style="font-size: 1.875rem; font-weight: 700; color: #0891b2; margin-top: 0.25rem;">{{ $totalCompletedSales + $totalCompletedPurchases }}</div>
    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">{{ $totalCompletedSales }} Penjualan &bull; {{ $totalCompletedPurchases }} Pembelian</div>
  </div>

  <div class="card" style="margin-bottom: 0; border-left: 4px solid #dc2626;">
    <span style="font-size: 0.8125rem; color: #64748b; font-weight: 600;">PRODUK STOK RENDAH</span>
    <div style="font-size: 1.875rem; font-weight: 700; color: #dc2626; margin-top: 0.25rem;">{{ $lowStockCount }}</div>
    <div style="font-size: 0.75rem; color: #dc2626; margin-top: 0.25rem;">Stok &le; batas minimum stok</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
  <!-- Produk Stok Rendah Alert -->
  <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #fff1f2;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #991b1b;">⚠️ Peringatan: Stok Rendah</h3>

      </div>
      <a href="{{ route('products.index', ['stock_status' => 'low']) }}" style="font-size: 0.8125rem; color: #2563eb; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th style="text-align: center;">Sisa</th>
            <th style="text-align: center;">Min</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lowStockProducts as $lp)
            <tr>
              <td style="font-family: monospace; font-weight: 600; color: #2563eb;">{{ $lp->sku }}</td>
              <td>{{ $lp->name }}</td>
              <td style="text-align: center; font-weight: 700; color: #dc2626;">{{ $lp->stock }}</td>
              <td style="text-align: center;">{{ $lp->minimum_stock }}</td>
              <td style="text-align: right;">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">+ Restock</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align: center; color: #16a34a; padding: 2rem;">Semua stok produk dalam kondisi aman!</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Transaksi Penjualan Terbaru -->
  <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f0fdf4;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #166534;">🛒 Transaksi Penjualan Terakhir</h3>

      </div>
      <a href="{{ route('sales.index') }}" style="font-size: 0.8125rem; color: #166534; text-decoration: none; font-weight: 600;">Semua Penjualan &rarr;</a>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>No. Invoice</th>
            <th>Kasir/User</th>
            <th style="text-align: right;">Total</th>
            <th style="text-align: center;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentSales as $rs)
            <tr>
              <td>
                <a href="{{ route('sales.show', $rs) }}" style="font-family: monospace; font-weight: 600; color: #2563eb; text-decoration: none;">
                  {{ $rs->sale_number }}
                </a>
              </td>
              <td>{{ $rs->user->name ?? '-' }}</td>
              <td style="text-align: right; font-weight: 600; color: #16a34a;">
                Rp {{ number_format($rs->total, 0, ',', '.') }}
              </td>
              <td style="text-align: center;">
                @if($rs->status === 'completed')
                  <span class="badge badge-success">Sukses</span>
                @else
                  <span class="badge badge-danger">Batal</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada transaksi penjualan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
