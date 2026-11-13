@extends('layouts.app')

@section('title', 'Transaksi Stok Keluar Gudang')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Transaksi Stok Keluar Gudang</h4>
        <p class="text-muted small mb-0">Catatan pengeluaran stok internal seperti barang rusak, kadaluarsa, retur, atau sampel.</p>
    </div>
    <a href="{{ route('stock-out.create') }}" class="btn btn-danger">
        <i class="bi bi-dash-lg me-1"></i> Catat Stok Keluar
    </a>
</div>

<!-- Filter Box -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('stock-out.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Cari No Transaksi / Alasan</label>
                <input type="text" class="form-control form-control-sm" name="search" value="{{ $search }}" placeholder="OUT-2026 / Rusak...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if($search || $startDate || $endDate)
                    <a href="{{ route('stock-out.index') }}" class="btn btn-sm btn-outline-danger" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Stock Out Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-arrow-up-right-square text-danger me-2"></i> Riwayat Pengeluaran Gudang</span>
        <span class="badge bg-light text-dark">Total: {{ $stockOuts->total() }} Transaksi</span>
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
                        <th>Petugas</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $index => $item)
                    <tr>
                        <td>{{ $stockOuts->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $item->no_transaksi }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 320px;">{{ $item->keterangan }}</span>
                        </td>
                        <td><small class="text-muted">{{ $item->user ? $item->user->name : '-' }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-danger-subtle text-danger">-{{ number_format($item->total_item) }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('stock-out.show', $item->id) }}" class="btn btn-light border" title="Lihat Rincian">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('stock-out.print', $item->id) }}" class="btn btn-light border" target="_blank" title="Cetak Berita Acara">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-check2-circle fs-1 text-muted d-block mb-2"></i>
                            Belum ada riwayat stok keluar non-penjualan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($stockOuts->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $stockOuts->links() }}
    </div>
    @endif
</div>
@endsection

