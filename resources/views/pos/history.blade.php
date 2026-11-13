@extends('layouts.app')

@section('title', 'Riwayat Penjualan Kasir')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Riwayat Transaksi Penjualan</h4>
        <p class="text-muted small mb-0">Daftar transaksi struk penjualan yang telah selesai dilakukan oleh kasir.</p>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-success">
        <i class="bi bi-cart4 me-1"></i> Buka Kasir (POS)
    </a>
</div>

<!-- Filter Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('pos.history') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Cari Invoice / Pelanggan</label>
                <input type="text" class="form-control form-control-sm" name="search" value="{{ $search }}" placeholder="INV-... / Nama pembeli">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if($search || $startDate || $endDate)
                    <a href="{{ route('pos.history') }}" class="btn btn-sm btn-outline-danger" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-receipt me-2 text-success"></i> Transaksi Penjualan</span>
        <span class="badge bg-light text-dark">Total: {{ $sales->total() }} Struk</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No Invoice</th>
                        <th>Waktu Transaksi</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Item</th>
                        <th>Total Belanja</th>
                        <th>Metode</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $index => $s)
                    <tr>
                        <td>{{ $sales->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $s->no_invoice }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $s->tanggal->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $s->tanggal->format('H:i') }} WIB</small>
                        </td>
                        <td><small class="text-muted">{{ $s->user ? $s->user->name : '-' }}</small></td>
                        <td>{{ $s->nama_pelanggan }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $s->total_item }} Unit</span></td>
                        <td><span class="fw-bold text-success">Rp {{ number_format($s->total_akhir, 0, ',', '.') }}</span></td>
                        <td><span class="badge bg-light text-secondary border">{{ $s->metode_pembayaran }}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light border" onclick="showSaleDetail({{ $s->id }})" title="Rincian Produk">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="{{ route('pos.receipt', $s->id) }}" target="_blank" class="btn btn-light border text-primary" title="Cetak Struk">
                                    <i class="bi bi-printer"></i> Struk
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-cart-x fs-1 text-muted d-block mb-2"></i>
                            Tidak ada data transaksi penjualan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sales->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $sales->links() }}
    </div>
    @endif
</div>

<!-- Modal Detail Penjualan -->
<div class="modal fade" id="modalSaleDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="detailInvoiceTitle">Rincian Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Kasir & Pelanggan:</small>
                        <span class="fw-semibold" id="detailKasirPelanggan">-</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Waktu Transaksi:</small>
                        <span class="fw-semibold" id="detailWaktu">-</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Metode Pembayaran:</small>
                        <span class="badge bg-primary" id="detailMetode">-</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th>Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody">
                            <!-- Items -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th class="text-end" id="detailSubtotal">-</th>
                            </tr>
                            <tr id="detailRowDiskon">
                                <th colspan="3" class="text-end text-danger">Diskon:</th>
                                <th class="text-end text-danger" id="detailDiskon">-</th>
                            </tr>
                            <tr class="table-light">
                                <th colspan="3" class="text-end fs-6">Total Akhir:</th>
                                <th class="text-end text-primary fs-5 fw-bold" id="detailTotalAkhir">-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="detailPrintBtn" target="_blank" class="btn btn-primary"><i class="bi bi-printer me-1"></i> Cetak Struk</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const salesJson = @json($sales->items());
    const receiptBaseUrl = "{{ url('pos/receipt') }}";

    function showSaleDetail(saleId) {
        const sale = salesJson.find(s => s.id === saleId);
        if (!sale) return;

        document.getElementById('detailInvoiceTitle').innerText = `Rincian Invoice: ${sale.no_invoice}`;
        document.getElementById('detailKasirPelanggan').innerText = `${sale.user ? sale.user.name : '-'} | ${sale.nama_pelanggan}`;
        document.getElementById('detailWaktu').innerText = new Date(sale.tanggal).toLocaleString('id-ID');
        document.getElementById('detailMetode').innerText = sale.metode_pembayaran;
        document.getElementById('detailPrintBtn').href = `${receiptBaseUrl}/${sale.id}`;

        const tbody = document.getElementById('detailItemsBody');
        tbody.innerHTML = '';

        sale.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-semibold text-dark">${item.product ? item.product.nama_produk : 'Produk'}</div>
                    <small class="text-muted">${item.product ? item.product.kode_produk : '-'}</small>
                </td>
                <td class="text-center"><span class="badge bg-light text-dark border">${item.jumlah}</span></td>
                <td>${formatRupiah(item.harga_jual)}</td>
                <td class="text-end fw-semibold">${formatRupiah(item.subtotal)}</td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('detailSubtotal').innerText = formatRupiah(sale.subtotal);
        if (sale.diskon > 0) {
            document.getElementById('detailRowDiskon').style.display = 'table-row';
            document.getElementById('detailDiskon').innerText = '- ' + formatRupiah(sale.diskon);
        } else {
            document.getElementById('detailRowDiskon').style.display = 'none';
        }
        document.getElementById('detailTotalAkhir').innerText = formatRupiah(sale.total_akhir);

        new bootstrap.Modal(document.getElementById('modalSaleDetail')).show();
    }
</script>
@endpush
@endsection
