@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Manajemen Pengguna &amp; Pelanggan (Customer)</h1>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Khusus Admin / Owner: Mengatur akun staf toko, wewenang hak akses, serta akun pelanggan terdaftar.</p>
  </div>
</div>

<!-- Statistik Ringkas Peran -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
  <a href="{{ route('users.index') }}" style="text-decoration: none;">
    <div class="card" style="padding: 1rem; border-left: 4px solid #64748b; {{ !request('role') ? 'background: #f1f5f9;' : '' }}">
      <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Semua Akun</div>
      <div style="font-size: 1.5rem; font-weight: 700; color: #0f172a;">{{ $totalAdmin + $totalStaff + $totalCustomers }}</div>
    </div>
  </a>
  <a href="{{ route('users.index', ['role' => 'user']) }}" style="text-decoration: none;">
    <div class="card" style="padding: 1rem; border-left: 4px solid #059669; {{ request('role') == 'user' ? 'background: #ecfdf5;' : '' }}">
      <div style="font-size: 0.75rem; font-weight: 600; color: #059669; text-transform: uppercase;">Pelanggan / Customer</div>
      <div style="font-size: 1.5rem; font-weight: 700; color: #065f46;">{{ $totalCustomers }}</div>
    </div>
  </a>
  <a href="{{ route('users.index', ['role' => 'staff']) }}" style="text-decoration: none;">
    <div class="card" style="padding: 1rem; border-left: 4px solid #2563eb; {{ request('role') == 'staff' ? 'background: #eff6ff;' : '' }}">
      <div style="font-size: 0.75rem; font-weight: 600; color: #2563eb; text-transform: uppercase;">Staff Kasir</div>
      <div style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">{{ $totalStaff }}</div>
    </div>
  </a>
  <a href="{{ route('users.index', ['role' => 'admin']) }}" style="text-decoration: none;">
    <div class="card" style="padding: 1rem; border-left: 4px solid #dc2626; {{ request('role') == 'admin' ? 'background: #fef2f2;' : '' }}">
      <div style="font-size: 0.75rem; font-weight: 600; color: #dc2626; text-transform: uppercase;">Admin / Owner</div>
      <div style="font-size: 1.5rem; font-weight: 700; color: #991b1b;">{{ $totalAdmin }}</div>
    </div>
  </a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) 2fr; gap: 1.5rem; align-items: start;">
  <!-- Form Tambah User Baru -->
  <div class="card">
    <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
      + Daftarkan Pengguna Baru
    </h3>
    <form method="POST" action="{{ route('users.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="name">Nama Lengkap <span style="color: #dc2626;">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Rian Pratama" required>
        @error('name')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="username">Username <span style="color: #dc2626;">*</span></label>
        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: rian23" required>
        @error('username')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Alamat Email <span style="color: #dc2626;">*</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="rian@example.com" required>
        @error('email')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password <span style="color: #dc2626;">*</span></label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
        @error('password')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="role">Hak Akses (Role) <span style="color: #dc2626;">*</span></label>
        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
          <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Pelanggan / Customer (Hanya Pemesanan)</option>
          <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff Kasir (Penjualan &amp; Konfirmasi)</option>
          <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin / Owner (Akses Penuh Seluruh Sistem)</option>
        </select>
        @error('role')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.65rem;">
        Simpan Pengguna
      </button>
    </form>
  </div>

  <!-- Tabel Daftar Pengguna & Pelanggan -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">
        Daftar Akun Pengguna &amp; Pelanggan Terdaftar
      </h3>
      @if(request('role'))
        <a href="{{ route('users.index') }}" style="font-size: 0.8125rem; color: #2563eb; text-decoration: underline;">Tampilkan Semua</a>
      @endif
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama Lengkap</th>
            <th>Username &amp; Email</th>
            <th style="text-align: center; width: 140px;">Role / Hak Akses</th>
            <th style="text-align: center; width: 130px;">Aktivitas</th>
            <th style="text-align: right; width: 90px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td>
                <strong>{{ $user->name }}</strong>
                @if(auth()->id() === $user->id)
                  <span style="font-size: 0.7rem; background: #e0f2fe; color: #0284c7; padding: 1px 6px; border-radius: 4px; font-weight: 600;">(Anda)</span>
                @endif
              </td>
              <td>
                <div style="font-family: monospace; color: #2563eb;">{{ $user->username ?? '-' }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">{{ $user->email }}</div>
              </td>
              <td style="text-align: center;">
                @if($user->isAdmin())
                  <span class="badge badge-danger">ADMIN</span>
                @elseif($user->isStaff())
                  <span class="badge badge-info">STAFF KASIR</span>
                @else
                  <span class="badge" style="background: #059669; color: #fff;">PELANGGAN</span>
                @endif
              </td>
              <td style="text-align: center; font-size: 0.8125rem;">
                @if($user->isCustomer())
                  <div>{{ $user->sales_count }} Pesanan</div>
                  <div style="color: #64748b; font-size: 0.75rem;">Pelanggan Toko</div>
                @else
                  <div>{{ $user->sales_count }} Penjualan</div>
                  <div style="color: #64748b; font-size: 0.75rem;">{{ $user->purchases_count }} Pembelian</div>
                @endif
              </td>
              <td style="text-align: right;">
                @if(auth()->id() !== $user->id)
                  <form action="{{ route('users.destroy', $user) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Hapus</button>
                  </form>
                @else
                  <span style="color: #94a3b8; font-size: 0.75rem;">Aktif</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada akun yang sesuai kriteria.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="padding: 1rem;">
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection
