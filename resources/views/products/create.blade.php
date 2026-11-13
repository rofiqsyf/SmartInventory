@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">Tambah Produk Baru</h4>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Left Column: Identitas Produk -->
                        <div class="col-md-6 mb-3">
                            <label for="kode_produk" class="form-label fw-semibold">Kode SKU / Barcode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_produk') is-invalid @enderror" id="kode_produk" name="kode_produk" value="{{ old('kode_produk', $suggestedCode) }}" required>
                            <small class="text-muted">Kode unik identifikasi barang.</small>
                            @error('kode_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold">Kategori Produk</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="nama_produk" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" placeholder="Contoh: Kopi Toraja 250gr" required>
                            @error('nama_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="satuan" class="form-label fw-semibold">Satuan Kemasan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', 'Pcs') }}" placeholder="Contoh: Pcs, Box, Kg, Liter" required>
                            @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="stok" class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', 0) }}" min="0" required>
                            @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="stok_minimum" class="form-label fw-semibold">Batas Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" min="0" required>
                            <small class="text-muted">Untuk peringatan low stock.</small>
                            @error('stok_minimum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga_beli" class="form-label fw-semibold">Harga Beli Modal (HPP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli" name="harga_beli" value="{{ old('harga_beli', 0) }}" min="0" step="100" required>
                            </div>
                            @error('harga_beli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga_jual" class="form-label fw-semibold">Harga Jual Kasir <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', 0) }}" min="0" step="100" required>
                            </div>
                            @error('harga_jual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="gambar" class="form-label fw-semibold">Foto / Gambar Produk</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
                            <small class="text-muted">Format: JPG, PNG, WEBP (Maksimal 2MB).</small>
                            @error('gambar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="mt-2" id="imagePreviewContainer" style="display: none;">
                                <img id="imagePreview" src="#" alt="Preview Gambar" class="rounded border p-1" style="max-height: 120px;">
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi / Catatan Produk</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Spesifikasi, varian warna, atau catatan lain...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('imagePreviewContainer');
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            container.style.display = 'none';
        }
    }
</script>
@endpush
@endsection

