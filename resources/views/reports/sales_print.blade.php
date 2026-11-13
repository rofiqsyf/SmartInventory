<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Kasir - SMART INVENTORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; color: #111; background-color: #f8fafc; }
        .print-container { max-width: 950px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .table th, .table td { padding: 6px 8px; }
        @media print {
            body { background: #fff; margin: 0; }
            .print-container { max-width: 100%; margin: 0; padding: 0; box-shadow: none; border-radius: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="container my-3 no-print text-center">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2"><i class="bi bi-printer"></i> Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-secondary px-4">Tutup</button>
    </div>

    <div class="print-container">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h4 class="fw-bold mb-0">SMART INVENTORY STORE</h4>
                <small class="text-muted">Laporan Rekapitulasi Penjualan Kasir & Omset Toko</small>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
                <small class="text-muted">Dicetak oleh: {{ Auth::user()->name }}</small>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Transaksi</small>
                    <strong>{{ number_format($totalSalesCount) }} Struk</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Item Terjual</small>
                    <strong>{{ number_format($totalItemsSold) }} Unit</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Omset Pendapatan</small>
                    <strong class="text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                </div>
            </div>
            @if(Auth::user()->isAdminGudang())
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Laba Kotor (Gross Profit)</small>
                    <strong class="text-primary">Rp {{ number_format($totalProfit, 0, ',', '.') }}</strong>
                </div>
            </div>
            @endif
        </div>

        <table class="table table-bordered table-striped align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>No Invoice</th>
                    <th>Tanggal & Waktu</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th class="text-center">Item</th>
                    <th>Metode</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Total Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><code>{{ $s->no_invoice }}</code></td>
                    <td>{{ $s->tanggal->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user ? $s->user->name : '-' }}</td>
                    <td>{{ $s->nama_pelanggan }}</td>
                    <td class="text-center">{{ $s->total_item }}</td>
                    <td>{{ $s->metode_pembayaran }}</td>
                    <td class="text-end">Rp {{ number_format($s->subtotal, 0, ',', '.') }}</td>
                    <td class="text-end">{{ $s->diskon > 0 ? 'Rp ' . number_format($s->diskon, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($s->total_akhir, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <th colspan="5" class="text-end">TOTAL KESELURUHAN:</th>
                    <th class="text-center">{{ number_format($totalItemsSold) }}</th>
                    <th></th>
                    <th class="text-end">Rp {{ number_format($totalRevenue + $totalDiscounts, 0, ',', '.') }}</th>
                    <th class="text-end">{{ $totalDiscounts > 0 ? 'Rp ' . number_format($totalDiscounts, 0, ',', '.') : '-' }}</th>
                    <th class="text-end text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html>

