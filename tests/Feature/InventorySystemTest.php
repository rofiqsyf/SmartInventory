<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockTransaction;
use App\Models\Sale;

class InventorySystemTest extends TestCase
{
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SmartInventory');
    }

    public function test_admin_gudang_can_login_and_access_dashboard()
    {
        $admin = User::where('role', 'admin_gudang')->first();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin Gudang');
        $response->assertSee('Nilai Aset Stok');
    }

    public function test_kasir_can_login_and_access_dashboard_and_pos()
    {
        $kasir = User::where('role', 'kasir')->first();

        $response = $this->actingAs($kasir)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Buka Kasir (POS)');

        $posResponse = $this->actingAs($kasir)->get('/pos');
        $posResponse->assertStatus(200);
        $posResponse->assertSee('Keranjang Kasir');
    }

    public function test_kasir_is_blocked_from_admin_gudang_pages()
    {
        $kasir = User::where('role', 'kasir')->first();

        $response = $this->actingAs($kasir)->get('/stock-in');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $userManageResponse = $this->actingAs($kasir)->get('/users');
        $userManageResponse->assertRedirect('/dashboard');
        $userManageResponse->assertSessionHas('error');
    }

    public function test_product_creation_with_initial_stock_creates_audit_stock_in_transaction()
    {
        $admin = User::where('role', 'admin_gudang')->first();
        $code = 'TEST-PRD-' . uniqid();

        $response = $this->actingAs($admin)->post('/products', [
            'kode_produk' => $code,
            'nama_produk' => 'Produk Uji Saldo Awal',
            'satuan' => 'Pcs',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'stok' => 15,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect('/products');
        $product = Product::where('kode_produk', $code)->first();
        $this->assertNotNull($product);
        $this->assertEquals(15, $product->stok);

        // Pastikan transaksi stok masuk otomatis tercatat
        $initialIn = StockTransaction::where('tipe', 'masuk')
            ->whereHas('details', fn($q) => $q->where('product_id', $product->id))
            ->first();

        $this->assertNotNull($initialIn);
        $this->assertEquals(15, $initialIn->total_item);
    }

    public function test_stock_in_increases_product_stock()
    {
        $admin = User::where('role', 'admin_gudang')->first();
        $product = Product::first();
        $initialStock = $product->stok;

        $response = $this->actingAs($admin)->post('/stock-in', [
            'no_transaksi' => 'TEST-IN-' . uniqid(),
            'tanggal' => date('Y-m-d'),
            'supplier_id' => null,
            'keterangan' => 'Pengujian Stok Masuk Otomatis',
            'items' => [
                [
                    'product_id' => $product->id,
                    'jumlah' => 10,
                    'harga_satuan' => $product->harga_beli,
                    'catatan' => 'Item test',
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals($initialStock + 10, $product->fresh()->stok);
    }

    public function test_stock_out_decreases_product_stock()
    {
        $admin = User::where('role', 'admin_gudang')->first();
        $product = Product::where('stok', '>=', 5)->first();
        $initialStock = $product->stok;

        $response = $this->actingAs($admin)->post('/stock-out', [
            'no_transaksi' => 'TEST-OUT-' . uniqid(),
            'tanggal' => date('Y-m-d'),
            'alasan' => 'Barang Rusak',
            'keterangan' => 'Uji barang pecah',
            'items' => [
                [
                    'product_id' => $product->id,
                    'jumlah' => 2,
                    'catatan' => 'Pecah saat uji coba',
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals($initialStock - 2, $product->fresh()->stok);
    }

    public function test_duplicate_items_exceeding_stock_rejected_in_stock_out()
    {
        $admin = User::where('role', 'admin_gudang')->first();
        $product = Product::create([
            'kode_produk' => 'STK-LIMIT-' . uniqid(),
            'nama_produk' => 'Produk Limit Tes',
            'satuan' => 'Pcs',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'stok' => 5,
            'stok_minimum' => 2,
        ]);

        // Kirim 2 baris produk yang sama, masing-masing 3 (total 6 > stok 5)
        $response = $this->actingAs($admin)->post('/stock-out', [
            'no_transaksi' => 'TEST-LIMIT-' . uniqid(),
            'tanggal' => date('Y-m-d'),
            'alasan' => 'Barang Rusak',
            'keterangan' => 'Uji duplikasi item',
            'items' => [
                ['product_id' => $product->id, 'jumlah' => 3],
                ['product_id' => $product->id, 'jumlah' => 3],
            ]
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(5, $product->fresh()->stok);
    }

    public function test_pos_checkout_creates_sale_and_decreases_stock()
    {
        $kasir = User::where('role', 'kasir')->first();
        $product = Product::where('stok', '>=', 3)->first();
        $initialStock = $product->stok;

        $totalBelanja = $product->harga_jual * 2;

        $response = $this->actingAs($kasir)->postJson('/pos', [
            'no_invoice' => 'TEST-INV-' . uniqid(),
            'nama_pelanggan' => 'Pembeli Uji Coba',
            'metode_pembayaran' => 'Tunai',
            'diskon' => 0,
            'bayar' => $totalBelanja + 10000,
            'catatan' => 'Uji POS checkout',
            'items' => [
                [
                    'product_id' => $product->id,
                    'jumlah' => 2,
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals($initialStock - 2, $product->fresh()->stok);
    }

    public function test_pos_checkout_rejects_duplicate_items_exceeding_stock()
    {
        $kasir = User::where('role', 'kasir')->first();
        $product = Product::create([
            'kode_produk' => 'POS-LIMIT-' . uniqid(),
            'nama_produk' => 'Produk POS Limit',
            'satuan' => 'Pcs',
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'stok' => 5,
            'stok_minimum' => 2,
        ]);

        // Kirim 2 baris produk yang sama masing-masing 4 (total 8 > 5)
        $response = $this->actingAs($kasir)->postJson('/pos', [
            'no_invoice' => 'TEST-INV-LIMIT-' . uniqid(),
            'nama_pelanggan' => 'Pembeli Tes',
            'metode_pembayaran' => 'Tunai',
            'diskon' => 0,
            'bayar' => 100000,
            'items' => [
                ['product_id' => $product->id, 'jumlah' => 4],
                ['product_id' => $product->id, 'jumlah' => 4],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertEquals(5, $product->fresh()->stok);
    }

    public function test_reports_are_accessible()
    {
        $admin = User::where('role', 'admin_gudang')->first();

        $this->actingAs($admin)->get('/reports')->assertStatus(200);
        $this->actingAs($admin)->get('/reports/stock')->assertStatus(200);
        $this->actingAs($admin)->get('/reports/stock-in')->assertStatus(200);
        $this->actingAs($admin)->get('/reports/stock-out')->assertStatus(200);
        $this->actingAs($admin)->get('/reports/sales')->assertStatus(200);
    }
}
