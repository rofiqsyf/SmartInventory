@extends('layouts.app')

@section('title', 'Catat Stok Keluar Gudang')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Catat Stok Keluar Gudang</h4>
        <p class="text-muted small mb-0">Input pengurangan stok gudang (bukan penjualan kasir), seperti barang rusak, kadaluarsa, atau retur.</p>
    </div>
    <a href="{{ route('stock-out.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
    </a>
</div>

<form action="{{ route('stock-out.store') }}" method="POST" id="formStockOut">
    @csrf

    <div class="row g-4">
        <!-- Top Info Card -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">No Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_transaksi" value="{{ old('no_transaksi', $suggestedCode) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Alasan Pengeluaran <span class="text-danger">*</span></label>
                            <select name="alasan" class="form-select" required>
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Barang Rusak">Barang Rusak / Cacat</option>
                                <option value="Kadaluarsa">Kadaluarsa / Expired</option>
                                <option value="Retur ke Supplier">Retur ke Supplier</option>
                                <option value="Pemakaian Internal">Pemakaian Sendiri / Internal</option>
                                <option value="Sampel & Promosi">Sampel & Promosi</option>
                                <option value="Selisih Opname">Koreksi Selisih Opname</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <input type="text" class="form-control" name="keterangan" value="{{ old('keterangan') }}" placeholder="Kronologi / nomor BA...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Items Table -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-danger"><i class="bi bi-box-arrow-up-right me-2"></i> Daftar Produk yang Dikeluarkan</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnAddRow">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Baris Produk
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="itemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 45%;">Produk <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Jumlah Keluar <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Catatan Fisik Item</th>
                                    <th style="width: 5%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white p-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="alert alert-warning py-2 px-3 small mb-0 d-inline-block">
                                <i class="bi bi-exclamation-triangle me-1"></i> Stok produk otomatis berkurang. Pastikan jumlah tidak melebihi sisa stok di gudang.
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted fw-semibold">Total Unit Dikeluarkan:</span>
                                <span class="fw-bold fs-5 text-danger" id="summaryTotalQty">0 Item</span>
                            </div>
                            <button type="submit" class="btn btn-danger btn-lg px-4 shadow-sm w-100">
                                <i class="bi bi-dash-circle me-1"></i> Simpan Transaksi Stok Keluar
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

    function createRow(initialProductId = '', initialQty = 1) {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let optionsHtml = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const selected = p.id == initialProductId ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-stock="${p.stok}" data-unit="${p.satuan}" ${selected}>${p.kode_produk} - ${p.nama_produk} (Stok: ${p.stok} ${p.satuan})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select select-product" required onchange="handleProductChange(this, ${rowIndex})">
                    ${optionsHtml}
                </select>
                <div class="small text-danger mt-1 product-warning" id="warning-${rowIndex}"></div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="items[${rowIndex}][jumlah]" class="form-control input-qty text-center" value="${initialQty}" min="1" required oninput="validateQty(${rowIndex})">
                    <span class="input-group-text small unit-label" id="unit-${rowIndex}">Unit</span>
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][catatan]" class="form-control" placeholder="Contoh: Tutup pecah, kadaluarsa 10 Nov">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(${rowIndex})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        const select = tr.querySelector('.select-product');
        if (initialProductId) {
            handleProductChange(select, rowIndex);
        }

        validateQty(rowIndex);
        rowIndex++;
    }

    function handleProductChange(select, index) {
        const option = select.options[select.selectedIndex];
        const row = document.getElementById(`row-${index}`);
        if (!option || !option.value) {
            document.getElementById(`warning-${index}`).innerText = '';
            return;
        }

        const unit = option.getAttribute('data-unit') || 'Unit';
        const stock = parseInt(option.getAttribute('data-stock')) || 0;

        document.getElementById(`unit-${index}`).innerText = unit;
        
        const qtyInput = row.querySelector('.input-qty');
        qtyInput.max = stock;

        validateQty(index);
    }

    function validateQty(index) {
        const row = document.getElementById(`row-${index}`);
        if (!row) return;

        const select = row.querySelector('.select-product');
        const option = select.options[select.selectedIndex];
        const warningDiv = document.getElementById(`warning-${index}`);

        if (option && option.value) {
            const stock = parseInt(option.getAttribute('data-stock')) || 0;
            const unit = option.getAttribute('data-unit') || '';
            const qty = parseInt(row.querySelector('.input-qty').value) || 0;

            if (qty > stock) {
                warningDiv.innerText = `⚠️ Kuantiti (${qty}) melebihi stok yang ada (${stock} ${unit})!`;
                row.querySelector('.input-qty').classList.add('is-invalid');
            } else {
                warningDiv.innerText = `Tersedia: ${stock} ${unit}`;
                warningDiv.className = 'small text-muted mt-1 product-warning';
                row.querySelector('.input-qty').classList.remove('is-invalid');
            }
        }

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
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.input-qty')?.value) || 0;
            totalQty += qty;
        });

        document.getElementById('summaryTotalQty').innerText = `${totalQty} Item`;
    }

    document.getElementById('btnAddRow').addEventListener('click', () => {
        createRow();
    });

    document.addEventListener('DOMContentLoaded', () => {
        createRow();
    });
</script>
@endpush
@endsection

