
@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Edit Supplier</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Perbarui kontak dan data supplier {{ $supplier->name }}.</p>
    </div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">&larr; Kembali</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label class="form-label" for="name">Nama Supplier <span style="color: #dc2626;">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $supplier->name) }}" required>
        @error('name')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="contact_info">Kontak Person (PIC)</label>
        <input type="text" name="contact_info" id="contact_info" class="form-control @error('contact_info') is-invalid @enderror" value="{{ old('contact_info', $supplier->contact_info) }}">
        @error('contact_info')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="phone">Nomor Telepon</label>
        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $supplier->phone) }}">
        @error('phone')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Alamat Email</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email) }}">
        @error('email')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection
