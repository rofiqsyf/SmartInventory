@extends('layouts.app')

@section('title', 'Detail Stok Masuk - ' . $stockIn->no_transaksi)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary fs-6">{{ $stockIn->no_transaksi }}</span>
            <h4 class="fw-bold mb-0">Rincian Transaksi Stok Masuk</h4>
        </div>
        <p class="text-muted small mb-0">Penerimaan pasokan barang gudang tanggal {{ \Carbon\Carbon::parse($stockIn->tanggal)->format('d F Y') }}.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock-in.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('stock-in.print', $stockIn->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Cetak Bukti Penerimaan
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Meta Info Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-info-circle me-1"></i> Data Transaksi</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">No Transaksi</td>
                        <td class="fw-semibold text-dark">{{ $stockIn->no_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>{{ \Carbon\Carbon::parse($stockIn->tanggal)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Supplier</td>
                        <td class="fw-semibold text-primary">{{ $stockIn->supplier ? $stockIn->supplier->nama_supplier : '<Tanpa Supplier>' }}</td>
                    </tr>
                    @if($stockIn->supplier)
                    <tr>
                        <td class="text-muted">Kontak</td>
                        <td>{{ $stockIn->supplier->telepon ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td class="small">{{ $stockIn->supplier->alamat ?: '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Penerima</td>
                        <td>{{ $stockIn->user ? $stockIn->user->name : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td>{{ $stockIn->keterangan ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-boxes me-1"></i> Rincian Barang Diterima</span>
                <span class="badge bg-primary-subtle text-primary">{{ $stockIn->total_item }} Total Unit</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th>Harga Beli</th>
                                <th>Subtotal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockIn->details as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->product ? $item->product->nama_produk : 'Produk Dihapus' }}</div>
                                    <small class="text-muted">{{ $item->product ? $item->product->kode_produk : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fs-6">+{{ $item->jumlah }} {{ $item->product ? $item->product->satuan : '' }}</span>
                                </td>
                                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td><small class="text-muted">{{ $item->catatan ?: '-' }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Pembelian:</th>
                                <th colspan="2" class="text-primary fs-5">Rp {{ number_format($stockIn->total_nominal, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

