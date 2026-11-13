@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Kategori Produk</h4>
        <p class="text-muted small mb-0">Kelola kelompok dan klasifikasi produk dalam inventori.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-4">
                <form action="{{ route('categories.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Cari kategori...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        @if($search)
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="col-md-auto text-muted small">
                Total: <strong>{{ $categories->total() }}</strong> kategori
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 140px;">Jumlah Produk</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $cat)
                    <tr>
                        <td>{{ $categories->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $cat->nama_kategori }}</span>
                        </td>
                        <td class="text-muted small">{{ $cat->deskripsi ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border">{{ $cat->products_count }} Produk</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light border" 
                                    onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->nama_kategori) }}', '{{ addslashes($cat->deskripsi ?? '') }}')" 
                                    title="Edit Kategori">
                                    <i class="bi bi-pencil text-warning"></i>
                                </button>
                                <button type="button" class="btn btn-light border text-danger" 
                                    onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->nama_kategori) }}')" 
                                    title="Hapus Kategori">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="delete-form-{{ $cat->id }}" action="{{ route('categories.destroy', $cat->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada kategori yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($categories->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<!-- Modal Add Category -->
<div class="modal fade" id="modalAddCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-tag me-1"></i> Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori" required placeholder="Contoh: Makanan & Minuman">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Keterangan singkat tentang kategori ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Category -->
<div class="modal fade" id="modalEditCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditCategory" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-1"></i> Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const categoryBaseUrl = "{{ url('categories') }}";

    function editCategory(id, name, description) {
        document.getElementById('formEditCategory').action = `${categoryBaseUrl}/${id}`;
        document.getElementById('edit_nama_kategori').value = name;
        document.getElementById('edit_deskripsi').value = description;
        new bootstrap.Modal(document.getElementById('modalEditCategory')).show();
    }

    function deleteCategory(id, name) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: `Apakah Anda yakin ingin menghapus kategori "${name}"?`,
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
