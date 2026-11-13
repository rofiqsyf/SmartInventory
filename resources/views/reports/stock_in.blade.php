@extends('layouts.app')

@section('title', 'Laporan Stok Masuk')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Laporan Transaksi Stok Masuk</h4>
        <p class="text-muted small mb-0">Rekapitulasi penerimaan barang dan pembelanjaan dari supplier pemasok.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.stock-in', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Export PDF
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('reports.stock-in') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter Periode</button>
                <a href="{{ route('reports.stock-in') }}" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 bg-white h-100 border-start border-primary border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Transaksi Masuk</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalTransactions) }} Transaksi</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-white h-100 border-start border-success border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Item Diterima</span>
            <h4 class="fw-bold text-success mt-1 mb-0">+{{ number_format($totalItemIn) }} Unit</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-white h-100 border-start border-info border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Nilai Pembelian</span>
            <h4 class="fw-bold text-primary mt-1 mb-0">Rp {{ number_format($totalNominalIn, 0, ',', '.') }}</h4>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <span class="fw-bold"><i class="bi bi-file-earmark-arrow-down me-2 text-success"></i> Riwayat Penerimaan Barang</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Petugas Gudang</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-end">Total Nominal</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $t)
                    <tr>
                        <td>{{ $transactions->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $t->no_transaksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $t->supplier ? $t->supplier->nama_supplier : '-' }}</td>
                        <td><small class="text-muted">{{ $t->user ? $t->user->name : '-' }}</small></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary">+{{ $t->total_item }}</span></td>
                        <td class="text-end fw-bold">Rp {{ number_format($t->total_nominal, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('stock-in.show', $t->id) }}" class="btn btn-sm btn-light border" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">Tidak ada transaksi stok masuk pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection

