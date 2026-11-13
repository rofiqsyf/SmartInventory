<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Posisi Stok Barang - SMART INVENTORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; color: #111; background-color: #f8fafc; }
        .print-container { max-width: 1000px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
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
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h4 class="fw-bold mb-0">SMART INVENTORY STORE</h4>
                <small class="text-muted">Laporan Posisi Persediaan & Nilai Aset Stok Gudang</small>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</small>
                <small class="text-muted">Dicetak oleh: {{ Auth::user()->name }}</small>
            </div>
        </div>

        <!-- Summary -->
        <div class="row g-2 mb-3">
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Varian Produk</small>
                    <strong>{{ number_format($totalItems) }} Item</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Kuantitas Fisik</small>
                    <strong>{{ number_format($totalStock) }} Unit</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Total Nilai Modal (HPP)</small>
                    <strong>Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="border p-2 rounded">
                    <small class="text-muted d-block">Potensi Nilai Jual</small>
                    <strong>Rp {{ number_format($totalPotentialValue, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-bordered table-striped align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>Kode SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok Fisik</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Subtotal Modal</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Subtotal Jual</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><code>{{ $p->kode_produk }}</code></td>
                    <td>{{ $p->nama_produk }}</td>
                    <td>{{ $p->category ? $p->category->nama_kategori : '-' }}</td>
                    <td class="text-center fw-bold">{{ $p->stok }} {{ $p->satuan }}</td>
                    <td class="text-end">Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->stok * $p->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($p->stok * $p->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($p->stok <= 0)
                            <span class="text-danger fw-bold">Habis</span>
                        @elseif($p->stok <= $p->stok_minimum)
                            <span class="text-warning fw-bold">Menipis</span>
                        @else
                            <span class="text-success">Aman</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <th colspan="4" class="text-end">TOTAL KESELURUHAN:</th>
                    <th class="text-center">{{ number_format($totalStock) }} Unit</th>
                    <th></th>
                    <th class="text-end">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</th>
                    <th></th>
                    <th class="text-end">Rp {{ number_format($totalPotentialValue, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html>

