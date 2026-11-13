@extends('layouts.app')

@section('title', 'Laporan Posisi Stok Barang')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Laporan Posisi Stok Barang</h4>
        <p class="text-muted small mb-0">Laporan real-time kondisi persediaan, nilai aset modal, dan potensi nilai jual.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.stock', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Export PDF
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('reports.stock') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ $c->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Stok</option>
                    <option value="normal" {{ $status === 'normal' ? 'selected' : '' }}>✅ Stok Aman (> Minimum)</option>
                    <option value="low" {{ $status === 'low' ? 'selected' : '' }}>⚠️ Stok Menipis (<= Minimum)</option>
                    <option value="out" {{ $status === 'out' ? 'selected' : '' }}>❌ Stok Habis (0)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if($categoryId || $status)
                    <a href="{{ route('reports.stock') }}" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-primary border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Item Produk</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalItems) }} Produk</h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-success border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Kuantitas Fisik</span>
            <h4 class="fw-bold text-success mt-1 mb-0">{{ number_format($totalStock) }} Unit</h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-info border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Total Nilai Modal (HPP)</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 bg-white h-100 border-start border-warning border-4 shadow-sm">
            <span class="text-muted small fw-semibold text-uppercase">Potensi Nilai Jual</span>
            <h4 class="fw-bold text-primary mt-1 mb-0">Rp {{ number_format($totalPotentialValue, 0, ',', '.') }}</h4>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <span class="fw-bold"><i class="bi bi-table me-2 text-primary"></i> Tabel Posisi Persediaan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-center">Sisa Stok</th>
                        @if(Auth::user()->isAdminGudang())
                            <th>Harga Beli</th>
                            <th>Total Aset Modal</th>
                        @endif
                        <th>Harga Jual</th>
                        <th>Potensi Omset</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $p)
                    <tr>
                        <td>{{ $products->firstItem() + $index }}</td>
                        <td><code>{{ $p->kode_produk }}</code></td>
                        <td>
                            <a href="{{ route('products.show', $p->id) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $p->nama_produk }}
                            </a>
                        </td>
                        <td><span class="badge bg-light text-secondary border">{{ $p->category ? $p->category->nama_kategori : '-' }}</span></td>
                        <td class="text-center fw-bold">{{ $p->stok }} {{ $p->satuan }}</td>
                        @if(Auth::user()->isAdminGudang())
                            <td>Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                            <td class="fw-semibold text-dark">Rp {{ number_format($p->stok * $p->harga_beli, 0, ',', '.') }}</td>
                        @endif
                        <td>Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                        <td class="fw-semibold text-primary">Rp {{ number_format($p->stok * $p->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($p->stok <= 0)
                                <span class="badge bg-danger">Habis</span>
                            @elseif($p->stok <= $p->stok_minimum)
                                <span class="badge bg-warning text-dark">Menipis</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">Tidak ada produk yang sesuai dengan kriteria laporan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection

