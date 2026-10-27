@extends('layouts.app')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">➕ Formulir Pemesanan Barang</h1>
      <p style="color: #64748b; margin: 0.25rem 0 0; font-size: 0.875rem;">
        Pilih produk dan jumlah pesanan Anda. Pesanan akan dikirimkan ke kasir toko untuk diverifikasi &amp; diproses.
      </p>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">&larr; Riwayat Pesanan</a>
  </div>

  <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
    @csrf

    <div class="card" style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1rem; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
        Informasi Pemesan
      </h3>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <div>
          <label class="form-label">Nama Pemesan</label>
          <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly style="background: #f8fafc; font-weight: 600;">
        </div>
        <div>
          <label class="form-label" for="notes">Catatan Tambahan untuk Kasir *(Opsional)*</label>
          <input type="text" name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Contoh: Tolong siapkan untuk diambil sore ini">
          @error('notes')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>
    </div>

    <!-- Multi-Item Order Table -->
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
      <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Daftar Barang yang Dipesan</h3>
          <small style="color: #64748b;">Pilih produk dan tentukan jumlah yang ingin Anda beli</small>
        </div>
        <button type="button" class="btn btn-primary" id="addRowBtn" style="padding: 0.35rem 0.75rem; font-size: 0.8125rem;">
          + Tambah Produk Lain
        </button>
      </div>

      <div class="table-responsive">
        <table id="orderTable">
          <thead>
            <tr>
              <th style="min-width: 280px;">Produk (Stok Tersedia)</th>
              <th style="width: 140px; text-align: center;">Jumlah Pesan</th>
              <th style="width: 180px; text-align: right;">Harga Satuan</th>
              <th style="width: 180px; text-align: right;">Subtotal</th>
              <th style="width: 60px; text-align: center;">Hapus</th>
            </tr>
          </thead>
          <tbody id="orderTableBody">
            <tr class="item-row">
              <td>
                <select name="items[0][product_id]" class="form-select product-select" required>
                  <option value="">-- Pilih Produk --</option>
                  @foreach($products as $prod)
                    <option value="{{ $prod->id }}" 
                            data-price="{{ $prod->selling_price }}" 
                            data-stock="{{ $prod->stock }}"
                            {{ ($selectedProductId == $prod->id || old('items.0.product_id') == $prod->id) ? 'selected' : '' }}>
                      {{ $prod->name }} (Sisa: {{ $prod->stock }})
                    </option>
                  @endforeach
                </select>
              </td>
              <td>
                <input type="number" name="items[0][quantity]" class="form-control qty-input text-center" min="1" max="999" value="{{ old('items.0.quantity', 1) }}" required>
              </td>
              <td style="text-align: right;">
                <span class="unit-price-display font-bold">Rp 0</span>
              </td>
              <td style="text-align: right;">
                <span class="subtotal-display font-bold" style="color: #16a34a;">Rp 0</span>
              </td>
              <td style="text-align: center;">
                <button type="button" class="btn btn-danger remove-row-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" disabled>&times;</button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr style="background: #f8fafc; font-weight: 700;">
              <td colspan="3" style="text-align: right; padding: 1rem;">Total Estimasi Pembayaran:</td>
              <td style="text-align: right; font-size: 1.2rem; color: #16a34a; padding: 1rem;">
                <span id="grandTotalDisplay">Rp 0</span>
              </td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    @if($errors->has('items'))
      <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        {{ $errors->first('items') }}
      </div>
    @endif

    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
      <a href="{{ route('orders.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem; font-size: 1rem; font-weight: 600;">
        🚀 Kirim Pemesanan ke Kasir
      </button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  let rowIndex = 1;
  const tbody = document.getElementById('orderTableBody');
  const addBtn = document.getElementById('addRowBtn');
  const grandTotalDisplay = document.getElementById('grandTotalDisplay');

  function formatRp(val) {
    return 'Rp ' + Number(val).toLocaleString('id-ID');
  }

  function calculateRow(row) {
    const select = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.qty-input');
    const unitPriceDisplay = row.querySelector('.unit-price-display');
    const subtotalDisplay = row.querySelector('.subtotal-display');

    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption && selectedOption.dataset.price ? parseFloat(selectedOption.dataset.price) : 0;
    const stock = selectedOption && selectedOption.dataset.stock ? parseInt(selectedOption.dataset.stock) : 0;
    
    let qty = parseInt(qtyInput.value) || 0;
    if (stock > 0 && qty > stock) {
      qty = stock;
      qtyInput.value = stock;
      alert('Kuantitas melebihi stok yang tersedia (' + stock + ' unit).');
    }

    const subtotal = price * qty;
    unitPriceDisplay.textContent = formatRp(price);
    subtotalDisplay.textContent = formatRp(subtotal);

    calculateGrandTotal();
  }

  function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
      const select = row.querySelector('.product-select');
      const qtyInput = row.querySelector('.qty-input');
      const selectedOption = select.options[select.selectedIndex];
      const price = selectedOption && selectedOption.dataset.price ? parseFloat(selectedOption.dataset.price) : 0;
      const qty = parseInt(qtyInput.value) || 0;
      grandTotal += (price * qty);
    });
    grandTotalDisplay.textContent = formatRp(grandTotal);
  }

  function bindRowEvents(row) {
    const select = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.qty-input');
    const removeBtn = row.querySelector('.remove-row-btn');

    select.addEventListener('change', () => calculateRow(row));
    qtyInput.addEventListener('input', () => calculateRow(row));

    if (removeBtn) {
      removeBtn.addEventListener('click', () => {
        if (document.querySelectorAll('.item-row').length > 1) {
          row.remove();
          calculateGrandTotal();
          updateRemoveButtons();
        }
      });
    }
  }

  function updateRemoveButtons() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach(r => {
      const btn = r.querySelector('.remove-row-btn');
      if (btn) btn.disabled = (rows.length === 1);
    });
  }

  addBtn.addEventListener('click', function () {
    const firstRow = document.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelectorAll('select, input').forEach(input => {
      const name = input.getAttribute('name');
      if (name) {
        input.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
      }
      if (input.tagName === 'SELECT') {
        input.selectedIndex = 0;
      } else {
        input.value = 1;
      }
    });

    newRow.querySelector('.unit-price-display').textContent = 'Rp 0';
    newRow.querySelector('.subtotal-display').textContent = 'Rp 0';

    tbody.appendChild(newRow);
    bindRowEvents(newRow);
    rowIndex++;
    updateRemoveButtons();
  });

  document.querySelectorAll('.item-row').forEach(row => {
    bindRowEvents(row);
    calculateRow(row);
  });
  updateRemoveButtons();
});
</script>
@endsection
