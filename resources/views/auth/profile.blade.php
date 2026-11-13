@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i> Pengaturan Profil Akun</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Akun</label>
                        <div>
                            @if(Auth::user()->isAdminGudang())
                                <span class="badge bg-primary px-3 py-2 fs-6"><i class="bi bi-shield-check me-1"></i> Admin Gudang</span>
                            @else
                                <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-person-badge me-1"></i> Kasir</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email (Akun Login)</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted">Alamat email tidak dapat diubah sendiri.</small>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i> Ganti Kata Sandi (Opsional)</h6>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Masukkan password saat ini jika ingin mengganti">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label fw-semibold">Kata Sandi Baru</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Minimal 6 karakter">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_password_confirmation" class="form-label fw-semibold">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

