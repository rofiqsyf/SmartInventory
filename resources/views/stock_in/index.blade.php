@extends('layouts.app')

@section('title', 'Transaksi Stok Masuk')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Transaksi Stok Masuk</h4>
        <p class="text-muted small mb-0">Catatan penerimaan pasokan barang dari supplier ke gudang.</p>
    </div>
    <a href="{{ route('stock-in.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Catat Stok Masuk
    </a>
</div>

<!-- Filter Box -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('stock-in.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Cari No Transaksi</label>
                <input type="text" class="form-control form-control-sm" name="search" value="{{ $search }}" placeholder="IN-2026...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if($search || $supplierId || $startDate || $endDate)
                    <a href="{{ route('stock-in.index') }}" class="btn btn-sm btn-outline-danger" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Stock In Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-arrow-down-left-square text-primary me-2"></i> Riwayat Stok Masuk</span>
        <span class="badge bg-light text-dark">Total: {{ $stockIns->total() }} Transaksi</span>
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
                        <th>Dicatat Oleh</th>
                        <th class="text-center">Total Item</th>
                        <th>Total Nominal</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $index => $item)
                    <tr>
                        <td>{{ $stockIns->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $item->no_transaksi }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->supplier ? $item->supplier->nama_supplier : '<Tanpa Supplier>' }}</td>
                        <td><small class="text-muted">{{ $item->user ? $item->user->name : '-' }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary">+{{ number_format($item->total_item) }}</span>
                        </td>
                        <td><span class="fw-semibold">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('stock-in.show', $item->id) }}" class="btn btn-light border" title="Lihat Rincian">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('stock-in.print', $item->id) }}" class="btn btn-light border" target="_blank" title="Cetak Bukti Penerimaan">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                            Belum ada riwayat transaksi stok masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($stockIns->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $stockIns->links() }}
    </div>
    @endif
</div>
@endsection

