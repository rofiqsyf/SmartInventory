@extends('layouts.app')

@section('title', 'Pusat Laporan Inventori & Penjualan')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Pusat Laporan Sistem</h4>
    <p class="text-muted small mb-0">Akses seluruh laporan stok inventori, pergerakan logistik gudang, dan pembukuan penjualan kasir.</p>
</div>

<!-- Overview Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-primary border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Produk Terdata</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalProducts) }}</h3>
            <small class="text-muted">Dengan {{ number_format($totalStock) }} unit fisik</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-info border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Nilai Aset Modal Fisik</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h4>
            <small class="text-muted">Total investasi stok di gudang</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-success border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Penjualan Hari Ini</span>
            <h4 class="fw-bold text-success mt-1 mb-0">Rp {{ number_format($todaySales, 0, ',', '.') }}</h4>
            <small class="text-muted">Omset kasir hari ini</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-warning border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Penjualan Bulan Ini</span>
            <h4 class="fw-bold text-primary mt-1 mb-0">Rp {{ number_format($monthlySales, 0, ',', '.') }}</h4>
            <small class="text-muted">Omset bulan berjalan</small>
        </div>
    </div>
</div>

<!-- Report Navigation Hub Cards -->
<div class="row g-4">
    <!-- Laporan Stok -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="stat-icon bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Laporan Posisi Stok</h5>
                    <p class="text-muted small">Ringkasan stok barang, nilai aset modal, potensi pendapatan, dan status produk menipis atau habis.</p>
                </div>
                <div class="mt-3">
                    <a href="{{ route('reports.stock') }}" class="btn btn-outline-primary w-100">
                        Buka Laporan Stok <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Stok Masuk -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="stat-icon bg-success-subtle text-success mb-3">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Laporan Stok Masuk</h5>
                    <p class="text-muted small">Catatan pasokan barang yang diterima dari supplier berdasarkan rentang periode tanggal.</p>
                </div>
                <div class="mt-3">
                    <a href="{{ route('reports.stock-in') }}" class="btn btn-outline-success w-100">
                        Buka Stok Masuk <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Stok Keluar -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="stat-icon bg-danger-subtle text-danger mb-3">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Laporan Stok Keluar</h5>
                    <p class="text-muted small">Rekap pengeluaran gudang internal non-penjualan seperti barang rusak, kadaluarsa, atau retur.</p>
                </div>
                <div class="mt-3">
                    <a href="{{ route('reports.stock-out') }}" class="btn btn-outline-danger w-100">
                        Buka Stok Keluar <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Penjualan -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="stat-icon bg-warning-subtle text-warning mb-3">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Laporan Penjualan</h5>
                    <p class="text-muted small">Rekapitulasi penjualan kasir, omset transaksi, beban modal pokok (HPP), dan laba kotor toko.</p>
                </div>
                <div class="mt-3">
                    <a href="{{ route('reports.sales') }}" class="btn btn-outline-warning w-100 text-dark">
                        Buka Penjualan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

