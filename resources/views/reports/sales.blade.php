@extends('layouts.app')

@section('title', 'Laporan Penjualan & Profit')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Laporan Penjualan Kasir & Keuntungan</h4>
        <p class="text-muted small mb-0">Rekapitulasi penjualan kasir, total omset, potongan diskon, dan margin laba kotor.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.sales', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Export PDF
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('reports.sales') }}" method="GET" class="row g-2 align-items-end">
            @if(Auth::user()->isAdminGudang())
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Kasir</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $userId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Metode Bayar</label>
                <select name="metode_pembayaran" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    <option value="Tunai" {{ $paymentMethod === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ $paymentMethod === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Transfer" {{ $paymentMethod === 'Transfer' ? 'selected' : '' }}>Transfer</option>
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
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-primary border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Transaksi</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalSalesCount) }} Struk</h4>
            <small class="text-muted">{{ number_format($totalItemsSold) }} item terjual</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-success border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Omset Penjualan</span>
            <h4 class="fw-bold text-success mt-1 mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
            <small class="text-muted">Total penerimaan kasir</small>
        </div>
    </div>
    @if(Auth::user()->isAdminGudang())
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-info border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Beban Modal (HPP)</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h4>
            <small class="text-muted">Total modal barang terjual</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-warning border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Laba Kotor (Profit)</span>
            <h4 class="fw-bold text-primary mt-1 mb-0">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h4>
            <small class="text-muted">Omset dikurangi HPP</small>
        </div>
    </div>
    @endif
</div>

<!-- Table Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <span class="fw-bold"><i class="bi bi-receipt me-2 text-primary"></i> Data Rincian Penjualan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No Invoice</th>
                        <th>Tanggal & Jam</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Item</th>
                        <th>Metode</th>
                        <th>Subtotal</th>
                        <th>Diskon</th>
                        <th class="text-end">Total Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $index => $s)
                    <tr>
                        <td>{{ $sales->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('pos.receipt', $s->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                {{ $s->no_invoice }}
                            </a>
                        </td>
                        <td>{{ $s->tanggal->format('d/m/Y H:i') }}</td>
                        <td><small class="text-muted">{{ $s->user ? $s->user->name : '-' }}</small></td>
                        <td>{{ $s->nama_pelanggan }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $s->total_item }}</span></td>
                        <td><span class="badge bg-light text-secondary border">{{ $s->metode_pembayaran }}</span></td>
                        <td>Rp {{ number_format($s->subtotal, 0, ',', '.') }}</td>
                        <td>{{ $s->diskon > 0 ? 'Rp ' . number_format($s->diskon, 0, ',', '.') : '-' }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($s->total_akhir, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">Tidak ada data transaksi penjualan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sales->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $sales->links() }}
    </div>
    @endif
</div>
@endsection

