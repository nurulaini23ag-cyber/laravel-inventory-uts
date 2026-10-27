@extends('layouts.app')

@section('content')
<div style="max-width: 480px; margin: 2rem auto;">
  <div style="text-align: center; margin-bottom: 1.5rem;">
    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔐</div>
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Masuk Sistem Inventaris &amp; Penjualan</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
      Silakan masuk sesuai peran Anda (Admin, Staff, atau Customer)
    </p>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="login">Username atau Email</label>
        <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror" value="{{ old('login', 'admin') }}" placeholder="admin / staff / customer" required autofocus>
        @error('login')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
        @error('password')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #475569; cursor: pointer;">
          <input type="checkbox" name="remember" value="1">
          <span>Ingat saya</span>
        </label>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.65rem 1rem; font-weight: 600;">
        Masuk ke Aplikasi
      </button>
    </form>

    <!-- Tombol Daftar Pelanggan Baru -->
    <div style="margin-top: 1.25rem; text-align: center; padding: 0.75rem; background: #f0fdf4; border-radius: 8px; border: 1px dashed #86efac; font-size: 0.875rem;">
      <span style="color: #166534;">Pelanggan baru?</span>
      <a href="{{ route('register') }}" style="color: #15803d; font-weight: 700; text-decoration: none; margin-left: 0.35rem;">
        🛍️ Daftar Akun Customer Baru &rarr;
      </a>
    </div>

    <!-- Informasi 3 Akun Demo Resmi: Admin, Staff, Customer -->
    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; font-size: 0.8125rem; color: #475569;">
      <div style="font-weight: 700; margin-bottom: 0.65rem; color: #0f172a; display: flex; align-items: center; gap: 0.35rem;">
        <span>🔑</span>
        <span>3 Akun Login Resmi (Siap Digunakan Langsung):</span>
      </div>
      <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.4rem 0;">
              <span class="badge badge-danger" style="font-size: 0.65rem;">ADMIN</span>
              <strong>Admin / Owner</strong>
            </td>
            <td style="padding: 0.4rem 0; font-family: monospace;"><code>admin</code> / <code>admin123</code></td>
          </tr>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.4rem 0;">
              <span class="badge badge-info" style="font-size: 0.65rem;">STAFF</span>
              <strong>Staff / Kasir</strong>
            </td>
            <td style="padding: 0.4rem 0; font-family: monospace;"><code>staff</code> / <code>staff123</code></td>
          </tr>
          <tr>
            <td style="padding: 0.4rem 0;">
              <span class="badge" style="background: #059669; color: #fff; font-size: 0.65rem;">CUSTOMER</span>
              <strong>Customer / Pelanggan</strong>
            </td>
            <td style="padding: 0.4rem 0; font-family: monospace;"><code>customer</code> / <code>customer123</code></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
