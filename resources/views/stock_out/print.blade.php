<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pengeluaran Barang - {{ $stockOut->no_transaksi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 13px; color: #111; background-color: #f8fafc; }
        .print-container { max-width: 800px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .table th, .table td { padding: 8px 10px; }
        @media print {
            body { background: #fff; margin: 0; }
            .print-container { max-width: 100%; margin: 0; padding: 0; box-shadow: none; border-radius: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="container my-3 no-print text-center">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2"><i class="bi bi-printer"></i> Cetak Dokumen</button>
        <button onclick="window.close()" class="btn btn-secondary px-4">Tutup</button>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h4 class="fw-bold mb-0">SMART INVENTORY</h4>
                <small class="text-muted">Sistem Manajemen Logistik & Gudang</small>
            </div>
            <div class="text-end">
                <h5 class="fw-bold text-danger mb-0">BERITA ACARA PENGELUARAN BARANG GUDANG</h5>
                <small class="fw-semibold">No: {{ $stockOut->no_transaksi }}</small>
            </div>
        </div>

        <!-- Meta -->
        <div class="row mb-3">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 130px;">Tanggal Keluar</td>
                        <td>: <strong>{{ \Carbon\Carbon::parse($stockOut->tanggal)->translatedFormat('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alasan / Keterangan</td>
                        <td>: <strong class="text-danger">{{ $stockOut->keterangan }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 130px;">Petugas Gudang</td>
                        <td>: {{ $stockOut->user ? $stockOut->user->name : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered table-striped align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>Kode SKU</th>
                    <th>Nama Produk</th>
                    <th class="text-center">Jumlah Dikeluarkan</th>
                    <th>Keterangan / Kondisi Fisik</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOut->details as $idx => $d)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><code>{{ $d->product ? $d->product->kode_produk : '-' }}</code></td>
                    <td>{{ $d->product ? $d->product->nama_produk : 'Produk' }}</td>
                    <td class="text-center fw-bold text-danger">-{{ $d->jumlah }} {{ $d->product ? $d->product->satuan : '' }}</td>
                    <td>{{ $d->catatan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <th colspan="3" class="text-end">Total Kuantitas Dikeluarkan:</th>
                    <th class="text-center text-danger fw-bold">-{{ number_format($stockOut->total_item) }} Unit</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-3">
            <div class="col-6">
                <p class="mb-5">Mengetahui / Supervisor Gudang,</p>
                <div class="border-top mx-auto" style="width: 180px;">( ..................................... )</div>
            </div>
            <div class="col-6">
                <p class="mb-5">Petugas Pelaksana,</p>
                <div class="border-top mx-auto fw-semibold" style="width: 180px;">( {{ $stockOut->user ? $stockOut->user->name : 'Petugas' }} )</div>
            </div>
        </div>
    </div>

</body>
</html>

