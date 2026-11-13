@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h4>
        <p class="text-muted small mb-0">Selamat bertugas di kasir toko. Layani pelanggan dengan ramah dan teliti.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('pos.index') }}" class="btn btn-success btn-lg shadow-sm px-4">
            <i class="bi bi-cart4 me-2"></i> Buka Kasir (POS)
        </a>
    </div>
</div>

<!-- Stat Cards Kasir -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Transaksi Hari Ini</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($todaySalesCount) }}</h3>
                    <small class="text-muted">Nota belanja kasir</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pendapatan Hari Ini</span>
                    <h4 class="fw-bold text-primary mt-1 mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h4>
                    <small class="text-muted">Total omset kasir Anda</small>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Item Terjual</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($todayItemsSold) }}</h3>
                    <small class="text-muted">Produk laku hari ini</small>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-bag-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 p-3 bg-white border-start border-secondary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Produk Siap Jual</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($availableProductsCount) }}</h3>
                    <small class="text-muted">Item dengan stok > 0</small>
                </div>
                <div class="stat-icon bg-light text-secondary">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Row: Quick POS Action Card & Recent Sales -->
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm h-100 text-white" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <span class="badge bg-white text-success mb-2 px-3 py-1 fw-bold">Point of Sale (POS)</span>
                    <h3 class="fw-bold mb-2">Kasir Siap Melayani Pembeli</h3>
                    <p class="text-white-50">Lakukan pemindaian barang, hitung diskon, pembayaran tunai/transfer/QRIS secara instan, dan cetak struk pembelian.</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pos.index') }}" class="btn btn-light btn-lg w-100 fw-bold text-success shadow-sm py-3">
                        <i class="bi bi-cart-plus-fill me-2 fs-5"></i> Masuk ke Kasir Penjualan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Penjualan Terakhir Anda</span>
                <a href="{{ route('pos.history') }}" class="btn btn-link btn-sm p-0 text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sale->no_invoice }}</div>
                                    <small class="text-muted">{{ $sale->tanggal->format('H:i, d/m/Y') }}</small>
                                </td>
                                <td>{{ $sale->nama_pelanggan }}</td>
                                <td><span class="fw-bold text-dark">Rp {{ number_format($sale->total_akhir, 0, ',', '.') }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $sale->metode_pembayaran }}</span></td>
                                <td>
                                    <a href="{{ route('pos.receipt', $sale->id) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Cetak Struk">
                                        <i class="bi bi-printer"></i> Struk
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi penjualan hari ini.</td>
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

