@extends('layouts.app')

@section('title', 'Kasir (Point of Sale)')

@push('styles')
<style>
    .pos-product-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .pos-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        border-color: #4361ee;
    }
    .pos-product-card.out-of-stock {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .pos-cart-panel {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        position: sticky;
        top: 85px;
    }
    .pos-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    .pos-cart-summary {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 1.25rem;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }
    .quick-cash-btn {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
</style>
@endpush

@section('content')
<div class="row g-3">
    <!-- Left Column: Product Catalog & Search -->
    <div class="col-lg-7 col-xl-8">
        <!-- Top Search and Category Bar -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control" id="productSearch" placeholder="Cari nama barang atau scan kode SKU..." autofocus>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-1 overflow-auto pb-1" id="categoryTabs">
                            <button type="button" class="btn btn-sm btn-primary active text-nowrap cat-filter-btn" data-category="all">
                                Semua ({{ $products->count() }})
                            </button>
                            @foreach($categories as $c)
                                <button type="button" class="btn btn-sm btn-light text-nowrap cat-filter-btn" data-category="{{ $c->id }}">
                                    {{ $c->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-3" id="productsGrid" style="max-height: calc(100vh - 210px); overflow-y: auto; padding-right: 4px;">
            @foreach($products as $prod)
            <div class="col-6 col-md-4 col-xl-3 product-item" 
                 data-id="{{ $prod->id }}" 
                 data-name="{{ strtolower($prod->nama_produk) }}" 
                 data-code="{{ strtolower($prod->kode_produk) }}" 
                 data-category="{{ $prod->category_id }}"
                 data-price="{{ $prod->harga_jual }}"
                 data-stock="{{ $prod->stok }}"
                 data-unit="{{ $prod->satuan }}">
                <div class="pos-product-card h-100 p-2 d-flex flex-column justify-content-between" onclick="addToCart({{ $prod->id }})">
                    <div>
                        <div class="position-relative mb-2">
                            @if($prod->gambar)
                                <img src="{{ asset($prod->gambar) }}" alt="{{ $prod->nama_produk }}" class="w-100 rounded object-fit-cover" style="height: 100px;">
                            @else
                                <div class="w-100 rounded bg-light text-muted d-flex align-items-center justify-content-center" style="height: 100px;">
                                    <i class="bi bi-box fs-2"></i>
                                </div>
                            @endif
                            <span class="position-absolute top-0 end-0 m-1 badge bg-dark opacity-75 small">
                                Stok: {{ $prod->stok }} {{ $prod->satuan }}
                            </span>
                        </div>
                        <span class="badge bg-light text-secondary border small mb-1" style="font-size: 0.7rem;">{{ $prod->kode_produk }}</span>
                        <div class="fw-semibold text-dark small text-truncate" title="{{ $prod->nama_produk }}">{{ $prod->nama_produk }}</div>
                    </div>
                    <div class="mt-2 pt-1 border-top d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary small">Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</span>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 rounded-circle" style="width: 24px; height: 24px; line-height: 1;">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right Column: Cart & Checkout Panel -->
    <div class="col-lg-5 col-xl-4">
        <div class="pos-cart-panel">
            <!-- Cart Header -->
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-cart4 text-success fs-5"></i>
                    <h6 class="fw-bold mb-0">Keranjang Kasir</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="clearCart()" title="Kosongkan Keranjang">
                    <i class="bi bi-trash me-1"></i> Reset
                </button>
            </div>

            <!-- Customer & Invoice Info -->
            <div class="px-3 py-2 bg-light border-bottom">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label text-muted small fw-semibold mb-0" style="font-size: 0.725rem;">No Invoice</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="noInvoice" value="{{ $suggestedInvoice }}" readonly>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small fw-semibold mb-0" style="font-size: 0.725rem;">Nama Pelanggan</label>
                        <input type="text" class="form-control form-control-sm" id="namaPelanggan" value="Pelanggan Umum" placeholder="Nama Pelanggan">
                    </div>
                </div>
            </div>

            <!-- Cart Items List -->
            <div class="pos-cart-items" id="cartContainer">
                <div class="text-center text-muted py-5" id="emptyCartNotice">
                    <i class="bi bi-cart-x fs-1 text-muted d-block mb-2"></i>
                    <p class="small mb-0">Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambah.</p>
                </div>
                <div id="cartItemsList"></div>
            </div>

            <!-- Cart Summary & Payment -->
            <div class="pos-cart-summary">
                <div class="d-flex justify-content-between text-muted small mb-1">
                    <span>Subtotal:</span>
                    <span id="summarySubtotal" class="fw-semibold">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                    <span>Diskon (Rp):</span>
                    <input type="number" id="inputDiskon" class="form-control form-control-sm text-end" style="width: 110px;" value="0" min="0" oninput="updateCalculations()">
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-2">
                    <span class="fw-bold fs-6 text-dark">Total Tagihan:</span>
                    <span id="summaryGrandTotal" class="fw-bold fs-5 text-primary">Rp 0</span>
                </div>

                <!-- Payment Method -->
                <div class="mb-2">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.75rem;">Metode Pembayaran</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="payment_method" id="payTunai" value="Tunai" checked>
                        <label class="btn btn-outline-secondary btn-sm" for="payTunai">Tunai</label>

                        <input type="radio" class="btn-check" name="payment_method" id="payQRIS" value="QRIS">
                        <label class="btn btn-outline-secondary btn-sm" for="payQRIS">QRIS</label>

                        <input type="radio" class="btn-check" name="payment_method" id="payTransfer" value="Transfer">
                        <label class="btn btn-outline-secondary btn-sm" for="payTransfer">Transfer</label>
                    </div>
                </div>

                <!-- Cash Payment & Quick Denominations -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-muted small fw-semibold mb-0" style="font-size: 0.75rem;">Uang Bayar (Rp)</label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary quick-cash-btn" onclick="setExactCash()">Pas</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn" onclick="addCash(50000)">50rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn" onclick="addCash(100000)">100rb</button>
                        </div>
                    </div>
                    <input type="number" id="inputBayar" class="form-control form-control-lg fw-bold text-end" value="0" min="0" oninput="updateCalculations()">
                </div>

                <!-- Change Calculation -->
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-white border">
                    <span class="small fw-semibold text-secondary">Kembalian:</span>
                    <span id="summaryKembalian" class="fw-bold fs-6 text-success">Rp 0</span>
                </div>

                <!-- Checkout Button -->
                <button type="button" class="btn btn-success btn-lg w-100 fw-bold shadow-sm" id="btnCheckout" onclick="processCheckout()">
                    <i class="bi bi-check2-circle me-1"></i> Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Success Checkout -->
<div class="modal fade" id="modalSuccess" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="my-3">
                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="bi bi-check-lg fs-1"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">Transaksi Berhasil!</h4>
            <p class="text-muted small mb-3" id="modalInvoiceText">Invoice: INV-...</p>

            <div class="p-3 bg-light rounded-3 mb-4 text-start small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Total Belanja:</span>
                    <strong id="modalTotalText">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Uang Diterima:</span>
                    <strong id="modalBayarText">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between border-top pt-1">
                    <span class="text-muted">Kembalian:</span>
                    <strong class="text-success" id="modalKembalianText">Rp 0</strong>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="#" id="modalPrintBtn" target="_blank" class="btn btn-primary py-2 fw-semibold">
                    <i class="bi bi-printer me-1"></i> Cetak Struk Belanja
                </a>
                <button type="button" class="btn btn-outline-secondary py-2" onclick="newTransaction()">
                    <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const products = @json($products);
    let cart = []; // Array of { id, name, code, price, stock, unit, qty }

    // Search and Category Filter
    document.getElementById('productSearch').addEventListener('input', filterProducts);

    document.querySelectorAll('.cat-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.cat-filter-btn').forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light');
            });
            this.classList.remove('btn-light');
            this.classList.add('btn-primary', 'active');
            filterProducts();
        });
    });

    function filterProducts() {
        const query = document.getElementById('productSearch').value.toLowerCase().trim();
        const activeCat = document.querySelector('.cat-filter-btn.active')?.getAttribute('data-category') || 'all';

        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name');
            const code = item.getAttribute('data-code');
            const category = item.getAttribute('data-category');

            const matchQuery = name.includes(query) || code.includes(query);
            const matchCat = (activeCat === 'all') || (category === activeCat);

            if (matchQuery && matchCat) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Add to Cart
    function addToCart(productId) {
        const prod = products.find(p => p.id == productId);
        if (!prod) return;

        if (prod.stok <= 0) {
            Swal.fire('Stok Habis', `Produk ${prod.nama_produk} sedang kosong!`, 'warning');
            return;
        }

        const existingItem = cart.find(item => item.id == productId);
        if (existingItem) {
            if (existingItem.qty >= prod.stok) {
                Swal.fire('Stok Maksimal', `Jumlah di keranjang sudah mencapai batas stok (${prod.stok} ${prod.satuan}).`, 'info');
                return;
            }
            existingItem.qty++;
        } else {
            cart.push({
                id: prod.id,
                name: prod.nama_produk,
                code: prod.kode_produk,
                price: parseFloat(prod.harga_jual),
                stock: prod.stok,
                unit: prod.satuan,
                qty: 1
            });
        }

        renderCart();
    }

    // Update Quantity
    function updateQty(productId, delta) {
        const item = cart.find(i => i.id == productId);
        if (!item) return;

        const newQty = item.qty + delta;
        if (newQty <= 0) {
            removeFromCart(productId);
            return;
        }

        if (newQty > item.stock) {
            Swal.fire('Stok Terbatas', `Maksimal stok tersedia adalah ${item.stock} ${item.unit}.`, 'info');
            return;
        }

        item.qty = newQty;
        renderCart();
    }

    function setQty(productId, value) {
        const item = cart.find(i => i.id == productId);
        if (!item) return;

        let qty = parseInt(value) || 1;
        if (qty <= 0) qty = 1;
        if (qty > item.stock) {
            qty = item.stock;
            Swal.fire('Stok Terbatas', `Maksimal stok tersedia adalah ${item.stock} ${item.unit}.`, 'info');
        }

        item.qty = qty;
        renderCart();
    }

    function removeFromCart(productId) {
        cart = cart.filter(i => i.id != productId);
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        cart = [];
        renderCart();
    }

    // Render Cart HTML
    function renderCart() {
        const emptyNotice = document.getElementById('emptyCartNotice');
        const cartList = document.getElementById('cartItemsList');

        if (cart.length === 0) {
            emptyNotice.style.display = 'block';
            cartList.innerHTML = '';
        } else {
            emptyNotice.style.display = 'none';
            let html = '';
            cart.forEach(item => {
                const subtotal = item.qty * item.price;
                html += `
                    <div class="p-2 mb-2 bg-light rounded border d-flex align-items-center justify-content-between">
                        <div style="max-width: 48%;">
                            <div class="fw-semibold text-dark small text-truncate" title="${item.name}">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${formatRupiah(item.price)} / ${item.unit}</div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="updateQty(${item.id}, -1)">-</button>
                            <input type="number" class="form-control form-control-sm text-center p-0" style="width: 42px; height: 28px;" value="${item.qty}" min="1" max="${item.stock}" onchange="setQty(${item.id}, this.value)">
                            <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                        <div class="text-end" style="width: 80px;">
                            <div class="fw-bold text-dark small">${formatRupiah(subtotal)}</div>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none" onclick="removeFromCart(${item.id})" style="font-size: 0.7rem;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            cartList.innerHTML = html;
        }

        updateCalculations();
    }

    // Calculations
    function updateCalculations() {
        let subtotal = 0;
        cart.forEach(i => subtotal += (i.qty * i.price));

        const diskon = parseFloat(document.getElementById('inputDiskon').value) || 0;
        const grandTotal = Math.max(0, subtotal - diskon);
        const bayar = parseFloat(document.getElementById('inputBayar').value) || 0;
        const kembalian = Math.max(0, bayar - grandTotal);

        document.getElementById('summarySubtotal').innerText = formatRupiah(subtotal);
        document.getElementById('summaryGrandTotal').innerText = formatRupiah(grandTotal);

        const kembalianEl = document.getElementById('summaryKembalian');
        kembalianEl.innerText = formatRupiah(kembalian);

        if (bayar < grandTotal && cart.length > 0) {
            kembalianEl.classList.remove('text-success');
            kembalianEl.classList.add('text-danger');
            kembalianEl.innerText = 'Kurang: ' + formatRupiah(grandTotal - bayar);
        } else {
            kembalianEl.classList.remove('text-danger');
            kembalianEl.classList.add('text-success');
        }
    }

    function setExactCash() {
        let subtotal = 0;
        cart.forEach(i => subtotal += (i.qty * i.price));
        const diskon = parseFloat(document.getElementById('inputDiskon').value) || 0;
        const grandTotal = Math.max(0, subtotal - diskon);

        document.getElementById('inputBayar').value = grandTotal;
        updateCalculations();
    }

    function addCash(amount) {
        let current = parseFloat(document.getElementById('inputBayar').value) || 0;
        document.getElementById('inputBayar').value = current + amount;
        updateCalculations();
    }

    // Checkout via AJAX
    function processCheckout() {
        if (cart.length === 0) {
            Swal.fire('Keranjang Kosong', 'Silakan pilih produk yang akan dibeli.', 'warning');
            return;
        }

        let subtotal = 0;
        cart.forEach(i => subtotal += (i.qty * i.price));
        const diskon = parseFloat(document.getElementById('inputDiskon').value) || 0;
        const grandTotal = Math.max(0, subtotal - diskon);
        const bayar = parseFloat(document.getElementById('inputBayar').value) || 0;

        if (bayar < grandTotal) {
            Swal.fire('Uang Kurang', `Total belanja ${formatRupiah(grandTotal)}, sedangkan uang bayar baru ${formatRupiah(bayar)}.`, 'warning');
            return;
        }

        const invoice = document.getElementById('noInvoice').value;
        const namaPelanggan = document.getElementById('namaPelanggan').value;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'Tunai';

        const payload = {
            no_invoice: invoice,
            nama_pelanggan: namaPelanggan,
            metode_pembayaran: paymentMethod,
            diskon: diskon,
            bayar: bayar,
            items: cart.map(i => ({ product_id: i.id, jumlah: i.qty }))
        };

        const btn = document.getElementById('btnCheckout');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

        fetch("{{ route('pos.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Bayar Sekarang';

            if (data.success) {
                // Tampilkan Modal Sukses
                document.getElementById('modalInvoiceText').innerText = `No Invoice: ${data.no_invoice}`;
                document.getElementById('modalTotalText').innerText = formatRupiah(grandTotal);
                document.getElementById('modalBayarText').innerText = formatRupiah(bayar);
                document.getElementById('modalKembalianText').innerText = formatRupiah(bayar - grandTotal);
                document.getElementById('modalPrintBtn').href = data.receipt_url;

                const modal = new bootstrap.Modal(document.getElementById('modalSuccess'));
                modal.show();

                // Buka struk di tab baru secara otomatis
                window.open(data.receipt_url, '_blank');
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat memproses transaksi.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Bayar Sekarang';
            Swal.fire('Kesalahan Sistem', 'Tidak dapat terhubung ke server.', 'error');
        });
    }

    function newTransaction() {
        location.reload();
    }
</script>
@endpush
@endsection

