
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Manajemen Pengguna &amp; Hak Akses (Role)</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Khusus Admin / Owner: Mengatur akun petugas, wewenang staf, dan integritas data.</p>
  </div>
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
        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: rian" required>
        @error('username')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Alamat Email <span style="color: #dc2626;">*</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="rian@inventory.com" required>
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
          <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff (Hanya Pembelian &amp; Penjualan)</option>
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

  <!-- Tabel Daftar Pengguna -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">Daftar Akun Pengguna Terdaftar</h3>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama Pengguna</th>
            <th>Username &amp; Email</th>
            <th style="text-align: center; width: 100px;">Role</th>
            <th style="text-align: center; width: 130px;">Aktivitas Transaksi</th>
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
                @else
                  <span class="badge badge-info">STAFF</span>
                @endif
              </td>
              <td style="text-align: center; font-size: 0.8125rem;">
                <div>{{ $user->sales_count }} Penjualan</div>
                <div style="color: #64748b;">{{ $user->purchases_count }} Pembelian</div>
              </td>
              <td style="text-align: right;">
                @if(auth()->id() !== $user->id)
                  <form action="{{ route('users.destroy', $user) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
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
              <td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada pengguna.</td>
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
