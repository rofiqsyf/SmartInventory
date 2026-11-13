@extends('layouts.app')

@section('title', 'Kelola Pengguna Sistem')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Pengguna (User Akun)</h4>
        <p class="text-muted small mb-0">Kelola akun staf Admin Gudang dan Kasir Toko serta hak aksesnya.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
    </button>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau telepon...">
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Role / Peran</option>
                    <option value="admin_gudang" {{ $role === 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                    <option value="kasir" {{ $role === 'kasir' ? 'selected' : '' }}>Kasir Toko</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if($search || $role)
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <span class="fw-bold"><i class="bi bi-people me-2 text-primary"></i> Daftar Akun Pengguna</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Role / Peran</th>
                        <th>Kontak Telepon</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle {{ $u->isAdminGudang() ? 'bg-primary' : 'bg-success' }} text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $u->name }}</div>
                                    <small class="text-muted">{{ $u->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($u->isAdminGudang())
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-shield-check me-1"></i> Admin Gudang
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-person-badge me-1"></i> Kasir
                                </span>
                            @endif
                        </td>
                        <td>{{ $u->phone ?: '-' }}</td>
                        <td class="text-center">
                            @if($u->status)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light border text-warning" 
                                    onclick="editUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}', '{{ $u->phone }}', {{ $u->status ? 1 : 0 }})" 
                                    title="Edit Pengguna">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($u->id !== Auth::id())
                                <button type="button" class="btn btn-light border text-danger" 
                                    onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')" 
                                    title="Hapus Pengguna">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>

                            @if($u->id !== Auth::id())
                            <form id="delete-form-{{ $u->id }}" action="{{ route('users.destroy', $u->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Modal Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="Nama lengkap petugas">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required placeholder="email@perusahaan.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peran / Role Akses <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="kasir">Kasir (Akses Penjualan POS & Katalog)</option>
                            <option value="admin_gudang">Admin Gudang (Akses Penuh Inventori & Logistik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon</label>
                        <input type="text" class="form-control" name="phone" placeholder="08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kata Sandi Awal <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" minlength="6" required placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Buat Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditUser" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-1"></i> Edit Data Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peran / Role Akses <span class="text-danger">*</span></label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="kasir">Kasir (Akses Penjualan POS & Katalog)</option>
                            <option value="admin_gudang">Admin Gudang (Akses Penuh Inventori & Logistik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Akun <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="1">Aktif (Dapat Login)</option>
                            <option value="0">Nonaktif (Diblokir)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ganti Kata Sandi (Opsional)</label>
                        <input type="password" class="form-control" name="password" minlength="6" placeholder="Biarkan kosong jika tidak ingin mengganti">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Perbarui Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const userBaseUrl = "{{ url('users') }}";

    function editUser(id, name, email, role, phone, status) {
        document.getElementById('formEditUser').action = `${userBaseUrl}/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_phone').value = phone || '';
        document.getElementById('edit_status').value = status;
        new bootstrap.Modal(document.getElementById('modalEditUser')).show();
    }

    function deleteUser(id, name) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: `Apakah Anda yakin ingin menghapus akun "${name}"?`,
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
