@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Daftar Supplier / Pemasok</h4>
        <p class="text-muted small mb-0">Kelola informasi mitra pemasok barang inventori.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSupplier">
        <i class="bi bi-plus-lg me-1"></i> Tambah Supplier
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-4">
                <form action="{{ route('suppliers.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Cari nama, telepon, alamat...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        @if($search)
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="col-md-auto text-muted small">
                Total: <strong>{{ $suppliers->total() }}</strong> supplier
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Supplier</th>
                        <th>Kontak Telepon</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th class="text-center">Riwayat Pasokan</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $sup)
                    <tr>
                        <td>{{ $suppliers->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $sup->nama_supplier }}</div>
                        </td>
                        <td>{{ $sup->telepon ?: '-' }}</td>
                        <td>{{ $sup->email ?: '-' }}</td>
                        <td><span class="text-muted small">{{ $sup->alamat ?: '-' }}</span></td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border">{{ $sup->stock_transactions_count }} Transaksi</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light border" 
                                    onclick="editSupplier({{ $sup->id }}, '{{ addslashes($sup->nama_supplier) }}', '{{ addslashes($sup->telepon ?? '') }}', '{{ addslashes($sup->email ?? '') }}', '{{ addslashes($sup->alamat ?? '') }}')" 
                                    title="Edit Supplier">
                                    <i class="bi bi-pencil text-warning"></i>
                                </button>
                                <button type="button" class="btn btn-light border text-danger" 
                                    onclick="deleteSupplier({{ $sup->id }}, '{{ addslashes($sup->nama_supplier) }}')" 
                                    title="Hapus Supplier">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="delete-form-{{ $sup->id }}" action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada supplier yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($suppliers->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>

<!-- Modal Add Supplier -->
<div class="modal fade" id="modalAddSupplier" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-truck me-1"></i> Tambah Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Supplier / Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_supplier" required placeholder="Contoh: PT Sumber Berkah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="telepon" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Contoh: supplier@domain.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Kantor / Gudang</label>
                        <textarea class="form-control" name="alamat" rows="3" placeholder="Alamat lengkap supplier..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Supplier -->
<div class="modal fade" id="modalEditSupplier" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditSupplier" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-1"></i> Edit Data Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Supplier / Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_supplier" name="nama_supplier" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control" id="edit_telepon" name="telepon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Kantor / Gudang</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const supplierBaseUrl = "{{ url('suppliers') }}";

    function editSupplier(id, name, phone, email, address) {
        document.getElementById('formEditSupplier').action = `${supplierBaseUrl}/${id}`;
        document.getElementById('edit_nama_supplier').value = name;
        document.getElementById('edit_telepon').value = phone;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_alamat').value = address;
        new bootstrap.Modal(document.getElementById('modalEditSupplier')).show();
    }

    function deleteSupplier(id, name) {
        Swal.fire({
            title: 'Hapus Supplier?',
            text: `Apakah Anda yakin ingin menghapus supplier "${name}"?`,
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
