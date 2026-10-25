
@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Manajemen Kategori Produk</h1>

    </div>
    <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Relasi One-to-Many dengan produk inventaris (Category hasMany Products).</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) 2fr; gap: 1.5rem; align-items: start;">
  <!-- Form Tambah Kategori -->
  @if(auth()->user()->isAdmin())
    <div class="card">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
        + Tambah Kategori
      </h3>
      <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="name">Nama Kategori <span style="color: #dc2626;">*</span></label>
          <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Perangkat Jaringan" required>
          @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="description">Deskripsi</label>
          <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Keterangan singkat kategori...">{{ old('description') }}</textarea>
          @error('description')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.65rem;">
          Simpan Kategori
        </button>
      </form>
    </div>
  @endif

  <!-- Tabel Daftar Kategori -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
      <h3 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">Daftar Kategori Produk</h3>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th style="text-align: center; width: 110px;">Jml Produk</th>
            @if(auth()->user()->isAdmin())
              <th style="text-align: right; width: 130px;">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td style="font-weight: 600; color: #0f172a;">{{ $category->name }}</td>
              <td style="color: #64748b; font-size: 0.8125rem;">{{ $category->description ?? '-' }}</td>
              <td style="text-align: center;">
                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="badge badge-info" style="text-decoration: none;">
                  {{ $category->products_count }} produk
                </a>
              </td>
              @if(auth()->user()->isAdmin())
                <td style="text-align: right;">
                  <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #2563eb;">Edit</a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
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
              <td colspan="{{ auth()->user()->isAdmin() ? 4 : 3 }}" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada data kategori.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="padding: 1rem;">
      {{ $categories->links() }}
    </div>
  </div>
</div>
@endsection
