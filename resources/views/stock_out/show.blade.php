@extends('layouts.app')

@section('title', 'Detail Stok Keluar - ' . $stockOut->no_transaksi)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger fs-6">{{ $stockOut->no_transaksi }}</span>
            <h4 class="fw-bold mb-0">Rincian Transaksi Stok Keluar Gudang</h4>
        </div>
        <p class="text-muted small mb-0">Pengeluaran barang internal tanggal {{ \Carbon\Carbon::parse($stockOut->tanggal)->format('d F Y') }}.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock-out.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('stock-out.print', $stockOut->id) }}" class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Cetak Berita Acara
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Meta Info Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-info-circle me-1"></i> Data Pengeluaran</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">No Transaksi</td>
                        <td class="fw-semibold text-dark">{{ $stockOut->no_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>{{ \Carbon\Carbon::parse($stockOut->tanggal)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Petugas</td>
                        <td>{{ $stockOut->user ? $stockOut->user->name : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keterangan / Alasan</td>
                        <td class="text-danger fw-semibold">{{ $stockOut->keterangan ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-boxes me-1"></i> Rincian Barang Dikeluarkan</span>
                <span class="badge bg-danger-subtle text-danger">{{ $stockOut->total_item }} Total Unit</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah Keluar</th>
                                <th>Catatan Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockOut->details as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->product ? $item->product->nama_produk : 'Produk Dihapus' }}</div>
                                    <small class="text-muted">{{ $item->product ? $item->product->kode_produk : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger fs-6">-{{ $item->jumlah }} {{ $item->product ? $item->product->satuan : '' }}</span>
                                </td>
                                <td><small class="text-muted">{{ $item->catatan ?: '-' }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th class="text-end">Total Barang Keluar:</th>
                                <th class="text-center text-danger fs-5">-{{ number_format($stockOut->total_item) }} Unit</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

