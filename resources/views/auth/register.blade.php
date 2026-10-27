@extends('layouts.app')

@section('content')
<div style="max-width: 480px; margin: 2rem auto;">
  <div style="text-align: center; margin-bottom: 2rem;">
    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛍️</div>
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Daftar Akun Pelanggan Baru</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
      Buat akun pelanggan untuk melihat katalog dan melakukan pemesanan aksesoris &amp; periferal laptop
    </p>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('register.post') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="name">Nama Lengkap <span style="color: #dc2626;">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
        @error('name')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="username">Username <span style="color: #dc2626;">*</span></label>
        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: budi23" required>
        @error('username')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Alamat Email <span style="color: #dc2626;">*</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="budi@example.com" required>
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
        <label class="form-label" for="password_confirmation">Konfirmasi Password <span style="color: #dc2626;">*</span></label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang password" required>
      </div>

      <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 0.75rem; margin-bottom: 1.25rem; font-size: 0.8125rem; color: #166534;">
        ℹ️ Akun Anda akan otomatis terdaftar sebagai <strong>Pelanggan (Customer)</strong> untuk melakukan pemesanan barang.
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.65rem 1rem; font-weight: 600;">
        Daftar Sekarang
      </button>
    </form>

    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; text-align: center; font-size: 0.875rem;">
      <span style="color: #64748b;">Sudah memiliki akun?</span>
      <a href="{{ route('login') }}" style="color: #2563eb; font-weight: 600; text-decoration: none; margin-left: 0.25rem;">
        Masuk di sini &rarr;
      </a>
    </div>
  </div>
</div>
@endsection
