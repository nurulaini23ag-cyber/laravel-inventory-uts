
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Manajemen Supplier / Pemasok</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Relasi One-to-Many ke Pembelian (Supplier hasMany Purchases).</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) 2fr; gap: 1.5rem; align-items: start;">
  <!-- Form Tambah Supplier -->
  @if(auth()->user()->isAdmin())
    <div class="card">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
        + Tambah Supplier
      </h3>
      <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="name">Nama Supplier / PT <span style="color: #dc2626;">*</span></label>
          <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: PT Sumber Rejeki" required>
          @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="contact_info">Kontak Person (PIC)</label>
          <input type="text" name="contact_info" id="contact_info" class="form-control @error('contact_info') is-invalid @enderror" value="{{ old('contact_info') }}" placeholder="Nama PIC Vendor">
          @error('contact_info')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">Nomor Telepon</label>
          <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="0812xxxx">
          @error('phone')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Alamat Email</label>
          <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="vendor@example.com">
          @error('email')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.65rem;">
          Simpan Supplier
        </button>
      </form>
    </div>
  @endif

  <!-- Tabel Daftar Supplier -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">Daftar Supplier Pemasok</h3>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama Supplier</th>
            <th>Kontak PIC</th>
            <th>Telepon &amp; Email</th>
            <th style="text-align: center; width: 100px;">Jml PO</th>
            @if(auth()->user()->isAdmin())
              <th style="text-align: right; width: 130px;">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse($suppliers as $supplier)
            <tr>
              <td style="font-weight: 600; color: #0f172a;">{{ $supplier->name }}</td>
              <td>{{ $supplier->contact_info ?? '-' }}</td>
              <td>
                <div>{{ $supplier->phone ?? '-' }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">{{ $supplier->email ?? '-' }}</div>
              </td>
              <td style="text-align: center;">
                <span class="badge badge-info">{{ $supplier->purchases_count }} Pembelian</span>
              </td>
              @if(auth()->user()->isAdmin())
                <td style="text-align: right;">
                  <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #2563eb;">Edit</a>
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Hapus</button>
                    </form>
                  </div>
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada data supplier.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="padding: 1rem;">
      {{ $suppliers->links() }}
    </div>
  </div>
</div>
@endsection
