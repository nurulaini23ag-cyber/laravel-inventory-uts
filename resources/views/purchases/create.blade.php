
@extends('layouts.app')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Catat Pembelian Stok Baru</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Mencatat barang masuk dari pemasok (Purchase Order). Stok produk akan bertambah otomatis.</p>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">&larr; Kembali</a>
  </div>

  <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
    @csrf

    <div class="card" style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; color: #0f172a;">Informasi Transaksi</h3>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="supplier_id">Pilih Supplier / Pemasok <span style="color: #dc2626;">*</span></label>
          <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">-- Pilih Supplier --</option>
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                {{ $supplier->name }} ({{ $supplier->phone ?? '-' }})
              </option>
            @endforeach
          </select>
          @error('supplier_id')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="purchased_at">Tanggal Pembelian <span style="color: #dc2626;">*</span></label>
          <input type="datetime-local" name="purchased_at" id="purchased_at" class="form-control @error('purchased_at') is-invalid @enderror" value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}" required>
          @error('purchased_at')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label" for="notes">Catatan / No. Referensi Pengadaan</label>
        <input type="text" name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Contoh: Pengadaan awal kuartal dari PO vendor">
        @error('notes')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <!-- Multi-Items Table -->
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
      <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Daftar Item Pembelian</h3>
          <small style="color: #64748b;">Tambahkan satu atau lebih produk dalam satu transaksi </small>
        </div>
        <button type="button" class="btn btn-primary" id="addRowBtn" style="padding: 0.35rem 0.75rem; font-size: 0.8125rem;">
          + Tambah Baris Item
        </button>
      </div>

      <div class="table-responsive">
        <table id="itemsTable">
          <thead>
            <tr>
              <th style="min-width: 250px;">Produk</th>
              <th style="width: 130px; text-align: center;">Jumlah (Qty)</th>
              <th style="width: 170px; text-align: right;">Harga Beli Satuan (Rp)</th>
              <th style="width: 170px; text-align: right;">Subtotal (Rp)</th>
              <th style="width: 60px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            {{-- Row template populated by JS or initial row --}}
          </tbody>
          <tfoot>
            <tr style="background: #f1f5f9; font-weight: 700;">
              <td colspan="3" style="text-align: right; padding: 1rem;">TOTAL KESELURUHAN:</td>
              <td style="text-align: right; font-size: 1.1rem; color: #2563eb; padding: 1rem;" id="grandTotalDisplay">Rp 0</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 3rem;">
      <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-success" style="padding: 0.65rem 1.5rem; font-weight: 600;">
        ✓ Simpan Transaksi Pembelian &amp; Tambah Stok
      </button>
    </div>
  </form>
</div>

<script>
  const productsData = @json($products);
  let rowIndex = 0;

  function createRow(selectedProductId = '', qty = 1, cost = 0) {
    const tr = document.createElement('tr');
    tr.id = 'row-' + rowIndex;

    let optionsHtml = '<option value="">-- Pilih Produk --</option>';
    productsData.forEach(p => {
      const isSel = (p.id == selectedProductId) ? 'selected' : '';
      optionsHtml += `<option value="${p.id}" data-cost="${p.purchase_price}" data-stock="${p.stock}" ${isSel}>${p.sku} - ${p.name} (Stok saat ini: ${p.stock})</option>`;
    });

    tr.innerHTML = `
      <td>
        <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
          ${optionsHtml}
        </select>
      </td>
      <td>
        <input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="${qty}" min="1" required style="text-align: center;">
      </td>
      <td>
        <input type="number" step="0.01" name="items[${rowIndex}][unit_cost]" class="form-control cost-input" value="${cost}" min="0" required style="text-align: right;">
      </td>
      <td style="text-align: right; font-weight: 600; vertical-align: middle;" class="subtotal-display">
        Rp 0
      </td>
      <td style="text-align: center; vertical-align: middle;">
        <button type="button" class="btn btn-danger remove-row-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">&times;</button>
      </td>
    `;

    document.getElementById('itemsBody').appendChild(tr);

    const select = tr.querySelector('.product-select');
    const qtyIn = tr.querySelector('.qty-input');
    const costIn = tr.querySelector('.cost-input');
    const removeBtn = tr.querySelector('.remove-row-btn');

    select.addEventListener('change', function() {
      const opt = select.options[select.selectedIndex];
      if (opt && opt.dataset.cost) {
        costIn.value = opt.dataset.cost;
      }
      updateCalculations();
    });

    qtyIn.addEventListener('input', updateCalculations);
    costIn.addEventListener('input', updateCalculations);

    removeBtn.addEventListener('click', function() {
      if (document.querySelectorAll('#itemsBody tr').length > 1) {
        tr.remove();
        updateCalculations();
      } else {
        alert('Transaksi minimal harus memiliki satu item produk.');
      }
    });

    rowIndex++;
    updateCalculations();
  }

  function updateCalculations() {
    let grandTotal = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
      const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
      const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
      const subtotal = qty * cost;
      tr.querySelector('.subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
      grandTotal += subtotal;
    });
    document.getElementById('grandTotalDisplay').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
  }

  document.getElementById('addRowBtn').addEventListener('click', () => createRow());

  // Init first row
  createRow();
</script>
@endsection
