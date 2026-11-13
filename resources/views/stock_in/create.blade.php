@extends('layouts.app')

@section('title', 'Catat Stok Masuk')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Catat Stok Masuk Gudang</h4>
        <p class="text-muted small mb-0">Input penerimaan pasokan barang baru untuk menambah kuantiti stok produk.</p>
    </div>
    <a href="{{ route('stock-in.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
    </a>
</div>

<form action="{{ route('stock-in.store') }}" method="POST" id="formStockIn">
    @csrf

    <div class="row g-4">
        <!-- Top Info Card -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">No Transaksi / Penerimaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_transaksi" value="{{ old('no_transaksi', $suggestedCode) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier Pemasok</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">-- Tanpa Supplier / Pembelian Umum --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Keterangan / Catatan</label>
                            <input type="text" class="form-control" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Pengiriman DO #10923">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Items Table -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-box-arrow-in-down me-2 text-primary"></i> Daftar Produk yang Diterima</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddRow">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Baris Produk
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="itemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 35%;">Produk <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Jumlah Masuk <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">Harga Beli Satuan <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Subtotal</th>
                                    <th style="width: 10%;">Catatan</th>
                                    <th style="width: 5%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic rows will appear here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white p-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="alert alert-info py-2 px-3 small mb-0 d-inline-block">
                                <i class="bi bi-info-circle me-1"></i> Stok produk otomatis bertambah sesuai jumlah yang Anda masukkan.
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-semibold">Total Item:</span>
                                <span class="fw-bold fs-6" id="summaryTotalQty">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-top pt-2">
                                <span class="text-dark fw-bold fs-5">Total Nominal:</span>
                                <span class="fw-bold fs-4 text-primary" id="summaryTotalNominal">Rp 0</span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm w-100">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Transaksi Stok Masuk
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const productsData = @json($products);
    let rowIndex = 0;

    function createRow(initialProductId = '', initialQty = 1, initialPrice = 0) {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let optionsHtml = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const selected = p.id == initialProductId ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-price="${p.harga_beli}" data-unit="${p.satuan}" data-stock="${p.stok}" ${selected}>${p.kode_produk} - ${p.nama_produk} (Sisa: ${p.stok} ${p.satuan})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select select-product" required onchange="handleProductChange(this, ${rowIndex})">
                    ${optionsHtml}
                </select>
                <div class="small text-muted mt-1 product-meta" id="meta-${rowIndex}"></div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="items[${rowIndex}][jumlah]" class="form-control input-qty text-center" value="${initialQty}" min="1" required oninput="calculateRow(${rowIndex})">
                    <span class="input-group-text small unit-label" id="unit-${rowIndex}">Pcs</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="items[${rowIndex}][harga_satuan]" class="form-control input-price" value="${initialPrice}" min="0" step="100" required oninput="calculateRow(${rowIndex})">
                </div>
            </td>
            <td>
                <span class="fw-bold text-dark row-subtotal" id="subtotal-${rowIndex}">Rp 0</span>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][catatan]" class="form-control form-control-sm" placeholder="Catatan item...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(${rowIndex})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Jika initial product dipilih, update field
        const select = tr.querySelector('.select-product');
        if (initialProductId) {
            handleProductChange(select, rowIndex);
        }

        calculateRow(rowIndex);
        rowIndex++;
    }

    function handleProductChange(select, index) {
        const option = select.options[select.selectedIndex];
        const row = document.getElementById(`row-${index}`);
        if (!option || !option.value) {
            document.getElementById(`meta-${index}`).innerText = '';
            return;
        }

        const price = option.getAttribute('data-price') || 0;
        const unit = option.getAttribute('data-unit') || 'Pcs';
        const stock = option.getAttribute('data-stock') || 0;

        document.getElementById(`unit-${index}`).innerText = unit;
        document.getElementById(`meta-${index}`).innerText = `Stok saat ini di gudang: ${stock} ${unit}`;
        
        const priceInput = row.querySelector('.input-price');
        priceInput.value = price;

        calculateRow(index);
    }

    function calculateRow(index) {
        const row = document.getElementById(`row-${index}`);
        if (!row) return;

        const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
        const price = parseFloat(row.querySelector('.input-price').value) || 0;
        const subtotal = qty * price;

        document.getElementById(`subtotal-${index}`).innerText = formatRupiah(subtotal);
        calculateTotal();
    }

    function removeRow(index) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.children.length <= 1) {
            Swal.fire('Info', 'Minimal harus ada 1 produk pada transaksi!', 'warning');
            return;
        }
        const row = document.getElementById(`row-${index}`);
        if (row) row.remove();
        calculateTotal();
    }

    function calculateTotal() {
        let totalQty = 0;
        let totalNominal = 0;

        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.input-qty')?.value) || 0;
            const price = parseFloat(tr.querySelector('.input-price')?.value) || 0;
            totalQty += qty;
            totalNominal += (qty * price);
        });

        document.getElementById('summaryTotalQty').innerText = totalQty;
        document.getElementById('summaryTotalNominal').innerText = formatRupiah(totalNominal);
    }

    document.getElementById('btnAddRow').addEventListener('click', () => {
        createRow();
    });

    // Inisialisasi baris pertama saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', () => {
        createRow();
    });
</script>
@endpush
@endsection

