@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">📋 Pesanan Saya</h1>
    <p style="color: #64748b; margin: 0.25rem 0 0; font-size: 0.875rem;">Pantau status pemesanan Anda dari toko secara langsung.</p>
  </div>
  <div style="display: flex; gap: 0.5rem;">
    <a href="{{ route('orders.catalog') }}" class="btn btn-secondary">
      🛍️ Lihat Katalog
    </a>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
      ➕ Pesan Barang Baru
    </a>
  </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>No. Pesanan</th>
          <th>Waktu Pemesanan</th>
          <th>Rincian Produk</th>
          <th style="text-align: right;">Total Estimasi</th>
          <th style="text-align: center;">Status Konfirmasi</th>
          <th style="text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>
              <div style="font-weight: 700; font-family: monospace; color: #2563eb;">
                {{ $order->sale_number }}
              </div>
              @if($order->notes)
                <small style="color: #64748b; font-style: italic;">Catatan: {{ Str::limit($order->notes, 30) }}</small>
              @endif
            </td>
            <td>
              <div>{{ $order->sold_at ? $order->sold_at->format('d/m/Y') : '-' }}</div>
              <small style="color: #64748b;">{{ $order->sold_at ? $order->sold_at->format('H:i') : '' }} WIB</small>
            </td>
            <td>
              <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                @foreach($order->items as $item)
                  <li>
                    {{ $item->product->name ?? 'Produk' }} 
                    <strong>&times; {{ $item->quantity }}</strong>
                  </li>
                @endforeach
              </ul>
            </td>
            <td style="text-align: right; font-weight: 700; font-size: 1rem; color: #0f172a;">
              Rp {{ number_format($order->total, 0, ',', '.') }}
            </td>
            <td style="text-align: center;">
              @if($order->status === 'pending')
                <span class="badge badge-warning" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 4px 8px;">
                  ⏳ Menunggu Konfirmasi Kasir
                </span>
              @elseif($order->status === 'completed')
                <span class="badge badge-success" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 4px 8px;">
                  ✅ Dikonfirmasi / Selesai
                </span>
              @elseif($order->status === 'cancelled')
                <span class="badge badge-danger" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 8px;">
                  ❌ Ditolak / Dibatalkan
                </span>
              @else
                <span class="badge badge-secondary">{{ strtoupper($order->status) }}</span>
              @endif
            </td>
            <td style="text-align: center;">
              <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8125rem;">
                Lihat Detail &rarr;
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🛒</div>
              <p style="margin: 0 0 1rem; font-size: 1rem; color: #64748b;">Anda belum pernah melakukan pemesanan.</p>
              <a href="{{ route('orders.create') }}" class="btn btn-primary">Mulai Buat Pesanan Pertama</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top: 1rem;">
  {{ $orders->links() }}
</div>
@endsection
