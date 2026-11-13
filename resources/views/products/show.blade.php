@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->nama_produk)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary">{{ $product->kode_produk }}</span>
            <h4 class="fw-bold mb-0">{{ $product->nama_produk }}</h4>
        </div>
        <p class="text-muted small mb-0">Informasi spesifikasi lengkap dan riwayat mutasi stok produk.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if(Auth::user()->isAdminGudang())
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Produk
        </a>
        @endif
    </div>
</div>

<!-- Product Info Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 bg-white h-100 border-start border-4 {{ $product->stok <= 0 ? 'border-danger' : ($product->stok <= $product->stok_minimum ? 'border-warning' : 'border-success') }}">
            <span class="text-muted small fw-semibold text-uppercase">Sisa Stok Fisik</span>
            <h3 class="fw-bold mt-1 mb-0 {{ $product->stok <= 0 ? 'text-danger' : ($product->stok <= $product->stok_minimum ? 'text-warning' : 'text-success') }}">
                {{ $product->stok }} <span class="fs-6 text-muted">{{ $product->satuan }}</span>
            </h3>
            <small class="text-muted">Batas Minimum: {{ $product->stok_minimum }} {{ $product->satuan }}</small>
        </div>
    </div>

    @if(Auth::user()->isAdminGudang())
    <div class="col-md-3">
        <div class="card p-3 bg-white h-100 border-start border-4 border-primary">
            <span class="text-muted small fw-semibold text-uppercase">Harga Beli (Modal)</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</h4>
            <small class="text-muted">Total Aset: Rp {{ number_format($product->stok * $product->harga_beli, 0, ',', '.') }}</small>
        </div>
    </div>
    @endif

    <div class="col-md-3">
        <div class="card p-3 bg-white h-100 border-start border-4 border-info">
            <span class="text-muted small fw-semibold text-uppercase">Harga Jual Kasir</span>
            <h4 class="fw-bold text-primary mt-1 mb-0">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</h4>
            <small class="text-muted">Per {{ $product->satuan }}</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-white h-100 border-start border-4 border-secondary">
            <span class="text-muted small fw-semibold text-uppercase">Kategori Produk</span>
            <h5 class="fw-bold text-dark mt-1 mb-0">{{ $product->category ? $product->category->nama_kategori : 'Umum' }}</h5>
            <small class="text-muted">Status: {{ $product->status ? 'Aktif' : 'Non-Aktif' }}</small>
        </div>
    </div>
</div>

<!-- Main Details & Tabs -->
<div class="row g-4">
    <!-- Left Column: Image and Description -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center p-4">
                @if($product->gambar)
                    <img src="{{ asset($product->gambar) }}" alt="{{ $product->nama_produk }}" class="img-fluid rounded border shadow-sm mb-3" style="max-height: 240px; width: 100%; object-fit: contain;">
                @else
                    <div class="rounded bg-light text-muted d-flex flex-column align-items-center justify-content-center p-5 mb-3 border">
                        <i class="bi bi-image fs-1 mb-2"></i>
                        <span class="small">Tidak ada foto produk</span>
                    </div>
                @endif

                <div class="text-start mt-3">
                    <h6 class="fw-bold text-dark mb-1">Deskripsi Produk:</h6>
                    <p class="text-muted small mb-0">{{ $product->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Mutation History Tabs -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white p-2">
                <ul class="nav nav-tabs card-header-tabs" id="mutationTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" id="stockin-tab" data-bs-toggle="tab" data-bs-target="#stockin-pane" type="button">
                            <i class="bi bi-arrow-down-left text-primary me-1"></i> Stok Masuk ({{ $stockIns->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="stockout-tab" data-bs-toggle="tab" data-bs-target="#stockout-pane" type="button">
                            <i class="bi bi-arrow-up-right text-danger me-1"></i> Stok Keluar Gudang ({{ $stockOutsGudang->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-pane" type="button">
                            <i class="bi bi-cart4 text-success me-1"></i> Penjualan Kasir ({{ $salesHistory->count() }})
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="mutationTabContent">
                    <!-- Tab Stok Masuk -->
                    <div class="tab-pane fade show active" id="stockin-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th class="text-center">Jumlah</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockIns as $in)
                                    <tr>
                                        <td class="fw-semibold">{{ $in->transaction->no_transaksi }}</td>
                                        <td>{{ \Carbon\Carbon::parse($in->transaction->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $in->transaction->supplier ? $in->transaction->supplier->nama_supplier : '-' }}</td>
                                        <td class="text-center"><span class="badge bg-primary-subtle text-primary">+{{ $in->jumlah }}</span></td>
                                        <td><small class="text-muted">{{ $in->catatan ?: '-' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat stok masuk untuk produk ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Stok Keluar Gudang -->
                    <div class="tab-pane fade" id="stockout-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Alasan / Keterangan</th>
                                        <th class="text-center">Jumlah</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockOutsGudang as $out)
                                    <tr>
                                        <td class="fw-semibold">{{ $out->transaction->no_transaksi }}</td>
                                        <td>{{ \Carbon\Carbon::parse($out->transaction->tanggal)->format('d/m/Y') }}</td>
                                        <td><small class="text-muted">{{ $out->transaction->keterangan }}</small></td>
                                        <td class="text-center"><span class="badge bg-danger-subtle text-danger">-{{ $out->jumlah }}</span></td>
                                        <td><small class="text-muted">{{ $out->catatan ?: '-' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat stok keluar gudang untuk produk ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Penjualan Kasir -->
                    <div class="tab-pane fade" id="sales-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th>Waktu</th>
                                        <th>Kasir</th>
                                        <th class="text-center">Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesHistory as $sh)
                                    <tr>
                                        <td class="fw-semibold">{{ $sh->sale->no_invoice }}</td>
                                        <td>{{ $sh->sale->tanggal->format('d/m/Y H:i') }}</td>
                                        <td>{{ $sh->sale->user ? $sh->sale->user->name : '-' }}</td>
                                        <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $sh->jumlah }}</span></td>
                                        <td>Rp {{ number_format($sh->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat penjualan kasir untuk produk ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

