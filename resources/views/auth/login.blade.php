
@extends('layouts.app')

@section('content')
<div style="max-width: 440px; margin: 3rem auto;">
  <div style="text-align: center; margin-bottom: 2rem;">
    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔐</div>
    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Masuk Sistem Inventaris</h1>

    </div>
    <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">Autentikasi akun pengguna sistem (Admin / Staff)</p>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="login">Username atau Email</label>
        <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror" value="{{ old('login', 'admin') }}" placeholder="admin / staff" required autofocus>
        @error('login')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="admin123" placeholder="••••••••" required>
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

    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; font-size: 0.8125rem; color: #64748b;">
      <strong>Akun Pengguna Default:</strong>
      <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
        <li>Admin/Owner: <code>admin</code> / <code>admin123</code> (Hak Akses Penuh)</li>
        <li>Staff: <code>staff</code> / <code>staff123</code> (Transaksi Pembelian &amp; Penjualan)</li>
      </ul>
    </div>
  </div>
</div>
@endsection
