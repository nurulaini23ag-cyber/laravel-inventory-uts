
@extends('layouts.app')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: #0f172a;">Kasir / Catat Penjualan Baru</h1>

      </div>
      <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Mencatat transaksi penjualan. Stok produk akan berkurang secara atomik (Anti-Overselling Protection).</p>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">&larr; Kembali</a>
  </div>

  <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
    @csrf

    <div class="card" style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1.05rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; color: #0f172a;">Informasi Transaksi</h3>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="sold_at">Tanggal &amp; Waktu Penjualan <span style="color: #dc2626;">*</span></label>
          <input type="datetime-local" name="sold_at" id="sold_at" class="form-control @error('sold_at') is-invalid @enderror" value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}" required>
          @error('sold_at')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="notes">Catatan Pelanggan / Invoice</label>
          <input type="text" name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Contoh: Pesanan Pelanggan Toko Fisik">
          @error('notes')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>
    </div>

    <!-- Multi-Item Table -->
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
      <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Daftar Item Penjualan</h3>
          <small style="color: #64748b;">Harga resmi dan kuantitas divalidasi oleh database server </small>
        </div>
        <button type="button" class="btn btn-primary" id="addRowBtn" style="padding: 0.35rem 0.75rem; font-size: 0.8125rem;">
          + Tambah Baris Produk
        </button>
      </div>

      <div class="table-responsive">
        <table id="salesTable">
          <thead>
            <tr>
              <th style="min-width: 280px;">Produk (Stok Tersedia)</th>
              <th style="width: 130px; text-align: center;">Jumlah Beli</th>
              <th style="width: 170px; text-align: right;">Harga Jual Satuan (Rp)</th>
              <th style="width: 170px; text-align: right;">Subtotal (Rp)</th>
              <th style="width: 60px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody id="salesBody">
            {{-- Populated dynamically --}}
          </tbody>
          <tfoot>
            <tr style="background: #f1f5f9; font-weight: 700;">
              <td colspan="3" style="text-align: right; padding: 1rem;">TOTAL PEMBAYARAN:</td>
              <td style="text-align: right; font-size: 1.2rem; color: #16a34a; padding: 1rem;" id="grandTotalDisplay">Rp 0</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 3rem;">
      <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-success" style="padding: 0.65rem 1.5rem; font-weight: 600;">
        ✓ Proses Transaksi Penjualan &amp; Potong Stok
      </button>
    </div>
  </form>
</div>

<script>
  const productsData = @json($products);
  let rowIndex = 0;

  function createRow(selectedProductId = '', qty = 1) {
    const tr = document.createElement('tr');
    tr.id = 'sale-row-' + rowIndex;

    let optionsHtml = '<option value="">-- Pilih Produk --</option>';
    productsData.forEach(p => {
      const isSel = (p.id == selectedProductId) ? 'selected' : '';
      optionsHtml += `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock}" ${isSel}>${p.sku} - ${p.name} (Tersedia: ${p.stock} unit)</option>`;
    });

    tr.innerHTML = `
      <td>
        <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
          ${optionsHtml}
        </select>
        <small class="stock-warning" style="display: none; color: #dc2626; font-size: 0.75rem; margin-top: 0.2rem;"></small>
      </td>
      <td>
        <input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="${qty}" min="1" required style="text-align: center;">
      </td>
      <td style="text-align: right; vertical-align: middle; font-weight: 500;" class="price-display">
        Rp 0
      </td>
      <td style="text-align: right; font-weight: 700; vertical-align: middle; color: #16a34a;" class="subtotal-display">
        Rp 0
      </td>
      <td style="text-align: center; vertical-align: middle;">
        <button type="button" class="btn btn-danger remove-row-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">&times;</button>
      </td>
    `;

    document.getElementById('salesBody').appendChild(tr);

    const select = tr.querySelector('.product-select');
    const qtyIn = tr.querySelector('.qty-input');
    const priceDisp = tr.querySelector('.price-display');
    const subtotalDisp = tr.querySelector('.subtotal-display');
    const warn = tr.querySelector('.stock-warning');
    const removeBtn = tr.querySelector('.remove-row-btn');

    function updateRow() {
      const opt = select.options[select.selectedIndex];
      if (opt && opt.dataset.price) {
        const price = parseFloat(opt.dataset.price);
        const stock = parseInt(opt.dataset.stock);
        const quantity = parseInt(qtyIn.value) || 0;

        priceDisp.textContent = 'Rp ' + price.toLocaleString('id-ID');
        const subtotal = price * quantity;
        subtotalDisp.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');

        if (quantity > stock) {
          warn.style.display = 'block';
          warn.textContent = `Peringatan: Jumlah (${quantity}) melebihi stok yang tersedia (${stock})!`;
          qtyIn.classList.add('is-invalid');
        } else {
          warn.style.display = 'none';
          qtyIn.classList.remove('is-invalid');
        }
      } else {
        priceDisp.textContent = 'Rp 0';
        subtotalDisp.textContent = 'Rp 0';
        warn.style.display = 'none';
      }
      updateGrandTotal();
    }

    select.addEventListener('change', updateRow);
    qtyIn.addEventListener('input', updateRow);

    removeBtn.addEventListener('click', function() {
      if (document.querySelectorAll('#salesBody tr').length > 1) {
        tr.remove();
        updateGrandTotal();
      } else {
        alert('Transaksi penjualan minimal harus memiliki satu produk.');
      }
    });

    rowIndex++;
    updateRow();
  }

  function updateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('#salesBody tr').forEach(tr => {
      const select = tr.querySelector('.product-select');
      const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
      const opt = select.options[select.selectedIndex];
      if (opt && opt.dataset.price) {
        grandTotal += (parseFloat(opt.dataset.price) * qty);
      }
    });
    document.getElementById('grandTotalDisplay').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
  }

  document.getElementById('addRowBtn').addEventListener('click', () => createRow());

  // Init first row
  createRow();
</script>
@endsection
