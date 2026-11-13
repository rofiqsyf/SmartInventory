<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Stok Keluar - SMART INVENTORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; color: #111; background-color: #f8fafc; }
        .print-container { max-width: 900px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
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
                <small class="text-muted">Laporan Pengeluaran Barang Internal Gudang (Stok Keluar)</small>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
                <small class="text-muted">Dicetak oleh: {{ Auth::user()->name }}</small>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Transaksi Keluar</small>
                    <strong>{{ number_format($totalTransactions) }} Transaksi</strong>
                </div>
            </div>
            <div class="col-6">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Kuantitas Dikeluarkan</small>
                    <strong class="text-danger">-{{ number_format($totalItemOut) }} Unit</strong>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>No Transaksi</th>
                    <th>Tanggal</th>
                    <th>Alasan & Catatan</th>
                    <th>Petugas Pelaksana</th>
                    <th class="text-center">Total Item Keluar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $idx => $t)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><code>{{ $t->no_transaksi }}</code></td>
                    <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $t->keterangan }}</td>
                    <td>{{ $t->user ? $t->user->name : '-' }}</td>
                    <td class="text-center fw-bold text-danger">-{{ number_format($t->total_item) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <th colspan="5" class="text-end">TOTAL KESELURUHAN:</th>
                    <th class="text-center text-danger">-{{ number_format($totalItemOut) }} Unit</th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html>

