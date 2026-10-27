<nav style="background: #0f172a; color: #fff; padding: 0.75rem 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
  <div style="max-width: 1250px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
      <a href="{{ auth()->check() && auth()->user()->isCustomer() ? route('orders.catalog') : route('dashboard') }}" style="color: #fff; text-decoration: none; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
        <span style="background: #2563eb; padding: 4px 8px; border-radius: 6px;">📦</span>
        <span>Inventory &amp; Sales</span>
      </a>

      <div style="display: flex; gap: 0.5rem; font-size: 0.875rem; flex-wrap: wrap;">
        @auth
          @if(auth()->user()->isCustomer())
            {{-- Menu Khusus Pelanggan / User --}}
            <a href="{{ route('orders.catalog') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('orders.catalog') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">🛍️ Katalog Produk</a>
            <a href="{{ route('orders.index') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('orders.index', 'orders.show') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">📋 Pesanan Saya</a>
            <a href="{{ route('orders.create') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('orders.create') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">➕ Pesan Barang</a>
          @else
            {{-- Menu Internal Toko (Admin & Staff) --}}
            <a href="{{ route('dashboard') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('dashboard') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">Dashboard</a>
            <a href="{{ route('products.index') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('products.*') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">Produk</a>
            <a href="{{ route('categories.index') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('categories.*') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">Kategori</a>
            <a href="{{ route('suppliers.index') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('suppliers.*') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">Supplier</a>
            <a href="{{ route('purchases.index') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('purchases.*') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">Pembelian (PO)</a>
            <a href="{{ route('sales.index') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('sales.*') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">
              Penjualan (Sales)
              @php
                $pendingOrdersCount = \App\Models\Sale::where('status', 'pending')->count();
              @endphp
              @if($pendingOrdersCount > 0)
                <span style="background: #f59e0b; color: #000; font-size: 0.7rem; font-weight: 700; padding: 1px 6px; border-radius: 9999px; margin-left: 4px;">{{ $pendingOrdersCount }}</span>
              @endif
            </a>
            <a href="{{ route('stock.history') }}" style="color: #cbd5e1; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('stock.*') ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : '' }}">Audit Stok</a>

            @if(auth()->user()->isAdmin())
              <a href="{{ route('users.index') }}" style="color: #fcd34d; text-decoration: none; padding: 0.4rem 0.65rem; border-radius: 6px; {{ request()->routeIs('users.*') ? 'background: #1e293b; color: #f59e0b; font-weight: 600;' : '' }}">Pengguna (User)</a>
            @endif
          @endif
        @endauth
      </div>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      @auth
        <span style="font-size: 0.8125rem; background: #1e293b; border: 1px solid #334155; padding: 0.35rem 0.75rem; border-radius: 9999px; color: #e2e8f0; display: inline-flex; align-items: center; gap: 0.35rem;">
          <span>👤</span>
          <strong>{{ auth()->user()->name }}</strong>
          <span style="font-size: 0.7rem; background: {{ auth()->user()->isAdmin() ? '#dc2626' : (auth()->user()->isStaff() ? '#2563eb' : '#059669') }}; color: #fff; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
            {{ strtoupper(auth()->user()->role) }}
          </span>
        </span>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
          @csrf
          <button type="submit" style="background: #334155; color: #f1f5f9; border: 1px solid #475569; padding: 0.35rem 0.75rem; border-radius: 6px; cursor: pointer; font-size: 0.8125rem; font-weight: 500;">
            Logout
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" style="background: #2563eb; color: #fff; text-decoration: none; padding: 0.4rem 0.85rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">Login</a>
      @endauth
    </div>
  </div>
</nav>
