@extends('layouts.app')

@section('title', 'Laporan Stok Keluar Gudang')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Laporan Transaksi Stok Keluar Gudang</h4>
        <p class="text-muted small mb-0">Rekapitulasi pengeluaran barang non-penjualan (rusak, kadaluarsa, retur, dll).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.stock-out', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Export PDF
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('reports.stock-out') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter Periode</button>
                <a href="{{ route('reports.stock-out') }}" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3 bg-white h-100 border-start border-danger border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Transaksi Keluar</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalTransactions) }} Transaksi</h4>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 bg-white h-100 border-start border-warning border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Item Dikeluarkan</span>
            <h4 class="fw-bold text-danger mt-1 mb-0">-{{ number_format($totalItemOut) }} Unit</h4>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <span class="fw-bold"><i class="bi bi-file-earmark-arrow-up me-2 text-danger"></i> Riwayat Pengeluaran Gudang</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Alasan & Catatan</th>
                        <th>Petugas Gudang</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $t)
                    <tr>
                        <td>{{ $transactions->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $t->no_transaksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                        <td><span class="text-danger fw-semibold">{{ $t->keterangan }}</span></td>
                        <td><small class="text-muted">{{ $t->user ? $t->user->name : '-' }}</small></td>
                        <td class="text-center"><span class="badge bg-danger-subtle text-danger">-{{ $t->total_item }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('stock-out.show', $t->id) }}" class="btn btn-sm btn-light border" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">Tidak ada data stok keluar gudang pada periode ini.</td>
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

