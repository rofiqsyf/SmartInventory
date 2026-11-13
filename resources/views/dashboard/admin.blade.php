@extends('layouts.app')

@section('title', 'Dashboard Admin Gudang')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard Admin Gudang</h4>
        <p class="text-muted small mb-0">Ringkasan kondisi inventori, stok gudang, dan pergerakan logistik barang.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock-in.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Stok Masuk Baru
        </a>
        <a href="{{ route('stock-out.create') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-dash-circle me-1"></i> Stok Keluar Gudang
        </a>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Produk</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalProducts) }}</h3>
                    <small class="text-muted">Item terdaftar</small>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Stok Fisik</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalStock) }}</h3>
                    <small class="text-muted">Unit di gudang</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Nilai Aset Stok (Modal)</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h4>
                    <small class="text-muted">Berdasarkan harga beli</small>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Stok Menipis</span>
                    <h3 class="fw-bold {{ $lowStockCount > 0 ? 'text-danger' : 'text-dark' }} mt-1 mb-0">{{ $lowStockCount }}</h3>
                    <small class="text-muted">Perlu restock segera</small>
                </div>
                <div class="stat-icon {{ $lowStockCount > 0 ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }}">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Low Stock If Exists -->
@if($lowStockCount > 0)
<div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div class="d-flex align-items-center">
        <div class="me-3 fs-3 text-warning">
            <i class="bi bi-bell-fill"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">Peringatan: Ada {{ $lowStockCount }} produk dengan sisa stok di bawah batas minimum!</div>
            <div class="small text-secondary">
                @foreach($lowStockProducts as $lp)
                    <span class="badge bg-warning text-dark me-1 mb-1">{{ $lp->nama_produk }} (Sisa: {{ $lp->stok }} {{ $lp->satuan }})</span>
                @endforeach
            </div>
        </div>
    </div>
    <div>
        <a href="{{ route('stock-in.create') }}" class="btn btn-warning btn-sm text-dark fw-bold">
            <i class="bi bi-truck me-1"></i> Order / Catat Stok Masuk
        </a>
    </div>
</div>
@endif

<!-- Charts and Monthly Summary Row -->
<div class="row g-4 mb-4">
    <!-- Chart Column -->
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i> Tren Pergerakan Stok (7 Hari Terakhir)</span>
                <span class="badge bg-light text-dark">Barang Masuk vs Barang Keluar</span>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="stockTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary Column -->
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="bi bi-pie-chart me-2 text-primary"></i> Rekap Mutasi Bulan Ini</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-around">
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Total Stok Masuk</span>
                        <span class="badge bg-primary">+{{ number_format($stockInThisMonth) }} item</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Stok Keluar Gudang</span>
                        <span class="badge bg-danger">-{{ number_format($stockOutThisMonth) }} item</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Total Penjualan Kasir</span>
                        <span class="fw-bold text-success">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</span>
                    </div>
                    <small class="text-muted">Omset penjualan bulan berjalan</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Tables Row -->
<div class="row g-4">
    <!-- Recent Stock In -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-arrow-down-left-square text-primary me-2"></i> Stok Masuk Terbaru</span>
                <a href="{{ route('stock-in.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No Transaksi</th>
                                <th>Supplier</th>
                                <th>Total Item</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStockIns as $in)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $in->no_transaksi }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($in->tanggal)->format('d/m/Y') }}</small>
                                </td>
                                <td>{{ $in->supplier ? $in->supplier->nama_supplier : '-' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">+{{ $in->total_item }}</span></td>
                                <td>
                                    <a href="{{ route('stock-in.show', $in->id) }}" class="btn btn-sm btn-light border py-0 px-2" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada transaksi stok masuk</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Stock Out -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-arrow-up-right-square text-danger me-2"></i> Stok Keluar Gudang Terbaru</span>
                <a href="{{ route('stock-out.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No Transaksi</th>
                                <th>Keterangan</th>
                                <th>Total Item</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStockOuts as $out)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $out->no_transaksi }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($out->tanggal)->format('d/m/Y') }}</small>
                                </td>
                                <td><span class="text-truncate d-inline-block" style="max-width: 180px;">{{ $out->keterangan }}</span></td>
                                <td><span class="badge bg-danger-subtle text-danger">-{{ $out->total_item }}</span></td>
                                <td>
                                    <a href="{{ route('stock-out.show', $out->id) }}" class="btn btn-sm btn-light border py-0 px-2" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada transaksi stok keluar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('stockTrendChart').getContext('2d');
    const stockTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDates) !!},
            datasets: [
                {
                    label: 'Stok Masuk',
                    data: {!! json_encode($chartStockIn) !!},
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointBackgroundColor: '#4361ee'
                },
                {
                    label: 'Stok Keluar (Gudang & Kasir)',
                    data: {!! json_encode($chartStockOut) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointBackgroundColor: '#ef4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        usePointStyle: true,
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush

