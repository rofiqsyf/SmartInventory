<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Belanja - {{ $sale->no_invoice }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
        }

        body {
            background-color: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 10px;
        }

        .receipt-card {
            background: #fff;
            width: 300px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 12px;
            color: #111;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .dashed-line {
            border-bottom: 1px dashed #444;
            margin: 8px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .btn-actions {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 4px;
            border: 1px solid #ccc;
            cursor: pointer;
            background: #fff;
        }

        .btn-primary {
            background: #4361ee;
            color: #fff;
            border-color: #4361ee;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .btn-actions {
                display: none !important;
            }
            .receipt-card {
                width: 100%;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="btn-actions">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Struk</button>
        <button onclick="window.close()" class="btn">Tutup</button>
    </div>

    <div class="receipt-card">
        <!-- Header -->
        <div class="text-center">
            <h3 class="fw-bold" style="font-size: 15px; margin-bottom: 2px;">SMART INVENTORY</h3>
            <p style="font-size: 11px;">Jl. Raya Bisnis No. 88, Indonesia</p>
            <p style="font-size: 11px;">Telp: (021) 8876-5432</p>
        </div>

        <div class="dashed-line"></div>

        <!-- Meta -->
        <div>
            <div class="item-row">
                <span>No Inv :</span>
                <span class="fw-bold">{{ $sale->no_invoice }}</span>
            </div>
            <div class="item-row">
                <span>Waktu  :</span>
                <span>{{ $sale->tanggal->format('d/m/Y H:i') }}</span>
            </div>
            <div class="item-row">
                <span>Kasir  :</span>
                <span>{{ $sale->user ? $sale->user->name : 'Kasir' }}</span>
            </div>
            <div class="item-row">
                <span>Plgn   :</span>
                <span>{{ $sale->nama_pelanggan }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Items -->
        <div>
            @foreach($sale->items as $item)
            <div style="margin-bottom: 6px;">
                <div class="fw-bold">{{ $item->product ? $item->product->nama_produk : 'Produk' }}</div>
                <div class="item-row" style="padding-left: 5px;">
                    <span>{{ $item->jumlah }} x Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                    <span class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="dashed-line"></div>

        <!-- Totals -->
        <div>
            <div class="item-row">
                <span>Subtotal ({{ $sale->total_item }} item):</span>
                <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($sale->diskon > 0)
            <div class="item-row">
                <span>Diskon:</span>
                <span>-Rp {{ number_format($sale->diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="item-row fw-bold" style="font-size: 13px; margin: 4px 0;">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->total_akhir, 0, ',', '.') }}</span>
            </div>
            <div class="item-row">
                <span>Bayar ({{ $sale->metode_pembayaran }}):</span>
                <span>Rp {{ number_format($sale->bayar, 0, ',', '.') }}</span>
            </div>
            <div class="item-row">
                <span>Kembalian:</span>
                <span class="fw-bold">Rp {{ number_format($sale->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Footer -->
        <div class="text-center" style="margin-top: 10px;">
            <p class="fw-bold">Terima Kasih Atas Kunjungan Anda</p>
            <p style="font-size: 10px; margin-top: 3px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa struk ini.</p>
        </div>
    </div>

</body>
</html>

