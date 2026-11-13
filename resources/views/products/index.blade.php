@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Daftar Produk & Stok</h4>
        <p class="text-muted small mb-0">Kelola katalog barang, informasi harga beli & jual, serta batas stok minimum.</p>
    </div>
    @if(Auth::user()->isAdminGudang())
    <div class="d-flex gap-2">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
        </a>
    </div>
    @endif
</div>

<!-- Filters Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Cari nama produk / kode SKU...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ $c->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="stock_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Stok</option>
                    <option value="low" {{ $filterStock === 'low' ? 'selected' : '' }}>⚠️ Stok Menipis (<= Min)</option>
                    <option value="out" {{ $filterStock === 'out' ? 'selected' : '' }}>❌ Stok Habis (0)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if($search || $categoryId || $filterStock)
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-danger" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Products Table Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i> Data Katalog Produk</span>
        <span class="badge bg-light text-dark">Total: {{ $products->total() }} Produk</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        @if(Auth::user()->isAdminGudang())
                            <th>Harga Beli (Modal)</th>
                        @endif
                        <th>Harga Jual</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $prod)
                    <tr>
                        <td>{{ $products->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($prod->gambar)
                                    <img src="{{ asset($prod->gambar) }}" alt="{{ $prod->nama_produk }}" class="rounded object-fit-cover" style="width: 44px; height: 44px;">
                                @else
                                    <div class="rounded bg-light text-secondary d-flex align-items-center justify-content-center border" style="width: 44px; height: 44px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('products.show', $prod->id) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                        {{ $prod->nama_produk }}
                                    </a>
                                    <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 0.75rem;">
                                        {{ $prod->kode_produk }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border">
                                {{ $prod->category ? $prod->category->nama_kategori : 'Tanpa Kategori' }}
                            </span>
                        </td>
                        @if(Auth::user()->isAdminGudang())
                            <td>
                                <span class="text-muted small">Rp {{ number_format($prod->harga_beli, 0, ',', '.') }}</span>
                            </td>
                        @endif
                        <td>
                            <span class="fw-bold text-dark">Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</span>
                            <small class="text-muted">/ {{ $prod->satuan }}</small>
                        </td>
                        <td class="text-center">
                            @if($prod->stok <= 0)
                                <span class="badge bg-danger">Habis (0 {{ $prod->satuan }})</span>
                            @elseif($prod->stok <= $prod->stok_minimum)
                                <span class="badge bg-warning text-dark" title="Batas Min: {{ $prod->stok_minimum }}">
                                    Menipis ({{ $prod->stok }} {{ $prod->satuan }})
                                </span>
                            @else
                                <span class="badge bg-success">
                                    {{ $prod->stok }} {{ $prod->satuan }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('products.show', $prod->id) }}" class="btn btn-light border" title="Detail & Riwayat Mutasi">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->isAdminGudang())
                                <a href="{{ route('products.edit', $prod->id) }}" class="btn btn-light border text-warning" title="Edit Produk">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-light border text-danger" 
                                    onclick="deleteProduct({{ $prod->id }}, '{{ addslashes($prod->nama_produk) }}')" 
                                    title="Hapus Produk">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>

                            @if(Auth::user()->isAdminGudang())
                            <form id="delete-form-{{ $prod->id }}" action="{{ route('products.destroy', $prod->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-box-seam fs-1 text-muted d-block mb-2"></i>
                            Tidak ada data produk yang sesuai dengan filter.
                        </td>
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

@push('scripts')
<script>
    function deleteProduct(id, name) {
        Swal.fire({
            title: 'Hapus Produk?',
            text: `Yakin ingin menghapus produk "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endpush
@endsection

