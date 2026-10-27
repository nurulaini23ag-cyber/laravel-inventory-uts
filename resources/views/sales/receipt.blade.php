<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan - {{ $sale->sale_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier Prime', Courier, monospace, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Action bar for screen view */
        .action-bar {
            width: 100%;
            max-width: 360px;
            margin-bottom: 16px;
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }

        .btn {
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background-color: #2563eb;
            color: #ffffff;
            flex: 1;
        }
        .btn-print:hover { background-color: #1d4ed8; }

        .btn-back {
            background-color: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-back:hover { background-color: #f8fafc; }

        /* Thermal receipt container */
        .receipt-card {
            width: 100%;
            max-width: 360px;
            background: #ffffff;
            padding: 24px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            font-size: 13px;
            line-height: 1.4;
            color: #111827;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }

        .store-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .store-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .store-subtitle {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #9ca3af;
            margin: 10px 0;
        }

        .double-divider {
            border-top: 2px dashed #4b5563;
            margin: 12px 0;
        }

        .meta-table {
            width: 100%;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* Items list */
        .items-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        .items-table th {
            padding: 4px 0;
            border-bottom: 1px dashed #9ca3af;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .item-name {
            font-weight: 700;
            word-break: break-word;
        }

        .item-meta {
            font-size: 11px;
            color: #4b5563;
        }

        .total-section {
            width: 100%;
            font-size: 13px;
        }

        .total-section td {
            padding: 3px 0;
        }

        .grand-total {
            font-size: 15px;
            font-weight: 700;
        }

        .footer-note {
            text-align: center;
            margin-top: 14px;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.4;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Inter', sans-serif;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }

        /* Print Optimization */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .action-bar {
                display: none !important;
            }

            .receipt-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            @page {
                size: 80mm auto;
                margin: 4mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden when printing) -->
    <div class="action-bar">
        <a href="{{ route('sales.show', $sale) }}" class="btn btn-back">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ Cetak Struk
        </button>
    </div>

    <!-- Printable Receipt Content -->
    <div class="receipt-card">
        <!-- Store Header -->
        <div class="store-header">
            <div style="font-size: 24px; margin-bottom: 4px;">📦</div>
            <div class="store-title">INVENTORY &amp; SALES SYSTEM</div>
            <div class="store-subtitle">
                Point of Sale &amp; Enterprise Stock Management<br>
                Jl. Pemrograman Web No. 2, Jakarta<br>
                Telp: (021) 8899-7766
            </div>
        </div>

        <div class="divider"></div>

        <!-- Transaction Metadata -->
        <table class="meta-table">
            <tr>
                <td style="width: 80px; color: #6b7280;">No. Invoice</td>
                <td style="width: 8px;">:</td>
                <td class="font-bold" style="font-family: monospace;">{{ $sale->sale_number }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tanggal</td>
                <td>:</td>
                <td>{{ $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Kasir</td>
                <td>:</td>
                <td>{{ $sale->user->name ?? 'Kasir Default' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Pelanggan</td>
                <td>:</td>
                <td>{{ $sale->customer_name ?: ($sale->user && $sale->user->isCustomer() ? $sale->user->name : 'Pelanggan Umum') }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Status</td>
                <td>:</td>
                <td>
                    @if($sale->status === 'completed')
                        <span class="badge-status badge-success">LUNAS / SUKSES</span>
                    @else
                        <span class="badge-status badge-danger">DIBATALKAN</span>
                    @endif
                </td>
            </tr>
            @if($sale->notes)
            <tr>
                <td style="color: #6b7280;">Catatan</td>
                <td>:</td>
                <td>{{ $sale->notes }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left">Item Produk</th>
                    <th class="text-right" style="width: 80px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td class="text-left">
                            <div class="item-name">{{ $item->product->name ?? 'Produk' }}</div>
                            <div class="item-meta">
                                {{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="text-right font-bold" style="vertical-align: middle;">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Total Calculation -->
        <table class="total-section">
            <tr>
                <td class="text-left" style="color: #4b5563;">Total Item</td>
                <td class="text-right font-bold">{{ $sale->items->sum('quantity') }} pcs</td>
            </tr>
            <tr>
                <td class="text-left" style="color: #4b5563;">Total Transaksi</td>
                <td class="text-right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
            <tr class="grand-total">
                <td class="text-left" style="padding-top: 6px;">TOTAL BAYAR</td>
                <td class="text-right" style="padding-top: 6px;">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="double-divider"></div>

        <!-- Footer / Thank you note -->
        <div class="footer-note">
            <p class="font-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
            <p style="margin-top: 4px; font-size: 10px;">
                Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan kecuali ada perjanjian sebelumnya.
            </p>
            <p style="margin-top: 6px; font-size: 10px; color: #9ca3af;">
                *** BUKTI PEMBAYARAN SAH ***
            </p>
        </div>
    </div>

</body>
</html>

