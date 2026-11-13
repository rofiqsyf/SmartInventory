<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin Gudang & Kasir
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventory.com'],
            [
                'name' => 'Budi Santoso (Admin Gudang)',
                'password' => Hash::make('password'),
                'role' => 'admin_gudang',
                'phone' => '081234567890',
                'status' => true,
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@inventory.com'],
            [
                'name' => 'Siti Rahma (Kasir Toko)',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'phone' => '089876543210',
                'status' => true,
            ]
        );

        // 2. Kategori Produk
        $categoriesData = [
            ['nama_kategori' => 'Makanan & Minuman', 'deskripsi' => 'Produk konsumsi, makanan ringan, dan minuman kemasan.'],
            ['nama_kategori' => 'Elektronik & Aksesoris', 'deskripsi' => 'Perangkat komputer, gadget, aksesoris kabel, dan perlengkapan.'],
            ['nama_kategori' => 'Alat Tulis Kantor', 'deskripsi' => 'Perlengkapan ATK, kertas, pulpen, dan kebutuhan kantor.'],
            ['nama_kategori' => 'Kebutuhan Rumah Tangga', 'deskripsi' => 'Pembersih, sabun cuci, dan kebutuhan harian rumah tangga.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['nama_kategori']] = Category::firstOrCreate(['nama_kategori' => $c['nama_kategori']], $c);
        }

        // 3. Data Supplier
        $suppliersData = [
            [
                'nama_supplier' => 'PT Sumber Berkah Pangan',
                'telepon' => '081211112222',
                'email' => 'sales@berkahpangan.co.id',
                'alamat' => 'Kawasan Industri Pulogadung No. 12, Jakarta Timur',
            ],
            [
                'nama_supplier' => 'CV Digital Abadi Elektronik',
                'telepon' => '081322223333',
                'email' => 'distribusi@digitalabadi.com',
                'alamat' => 'Jl. Embong Malang No. 45, Surabaya',
            ],
            [
                'nama_supplier' => 'PT Logistik Maju Bersama',
                'telepon' => '081533334444',
                'email' => 'kontak@majubersama.id',
                'alamat' => 'Jl. Soekarno Hatta No. 88, Bandung',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[] = Supplier::firstOrCreate(['nama_supplier' => $s['nama_supplier']], $s);
        }

        // 4. Data Produk
        $productsData = [
            [
                'kode_produk' => 'PRD-0001',
                'nama_produk' => 'Kopi Arabika Toraja 250gr',
                'category_id' => $categories['Makanan & Minuman']->id,
                'satuan' => 'Bungkus',
                'harga_beli' => 28000,
                'harga_jual' => 42000,
                'stok' => 25,
                'stok_minimum' => 5,
                'deskripsi' => 'Kopi bubuk premium pilihan khas Toraja Sulawesi Selatan.',
            ],
            [
                'kode_produk' => 'PRD-0002',
                'nama_produk' => 'Susu UHT Full Cream 1 Liter',
                'category_id' => $categories['Makanan & Minuman']->id,
                'satuan' => 'Kotak',
                'harga_beli' => 15000,
                'harga_jual' => 19500,
                'stok' => 40,
                'stok_minimum' => 10,
                'deskripsi' => 'Susu sapi segar steril dalam kemasan tetra pack.',
            ],
            [
                'kode_produk' => 'PRD-0003',
                'nama_produk' => 'Biskuit Cokelat Kaleng 300g',
                'category_id' => $categories['Makanan & Minuman']->id,
                'satuan' => 'Kaleng',
                'harga_beli' => 22000,
                'harga_jual' => 30000,
                'stok' => 4, // Stok Menipis!
                'stok_minimum' => 6,
                'deskripsi' => 'Biskuit renyah bersalut cokelat murni.',
            ],
            [
                'kode_produk' => 'PRD-0004',
                'nama_produk' => 'Teh Celup Melati Box (25 Kantong)',
                'category_id' => $categories['Makanan & Minuman']->id,
                'satuan' => 'Box',
                'harga_beli' => 6500,
                'harga_jual' => 9500,
                'stok' => 50,
                'stok_minimum' => 10,
                'deskripsi' => 'Teh wangi melati alami kualitas terbaik.',
            ],
            [
                'kode_produk' => 'PRD-0005',
                'nama_produk' => 'Mouse Wireless Ergonomis Silent',
                'category_id' => $categories['Elektronik & Aksesoris']->id,
                'satuan' => 'Pcs',
                'harga_beli' => 65000,
                'harga_jual' => 99000,
                'stok' => 15,
                'stok_minimum' => 5,
                'deskripsi' => 'Mouse nirkabel 2.4GHz dengan tombol senyap.',
            ],
            [
                'kode_produk' => 'PRD-0006',
                'nama_produk' => 'Kabel Data Fast Charging Type-C 1M',
                'category_id' => $categories['Elektronik & Aksesoris']->id,
                'satuan' => 'Pcs',
                'harga_beli' => 18000,
                'harga_jual' => 35000,
                'stok' => 30,
                'stok_minimum' => 8,
                'deskripsi' => 'Kabel braided kuat support fast charging hingga 65W.',
            ],
            [
                'kode_produk' => 'PRD-0007',
                'nama_produk' => 'Flashdisk 32GB USB 3.0 Metal',
                'category_id' => $categories['Elektronik & Aksesoris']->id,
                'satuan' => 'Pcs',
                'harga_beli' => 45000,
                'harga_jual' => 68000,
                'stok' => 3, // Stok Menipis!
                'stok_minimum' => 5,
                'deskripsi' => 'Penyimpanan USB transfer data kecepatan tinggi.',
            ],
            [
                'kode_produk' => 'PRD-0008',
                'nama_produk' => 'Kertas HVS A4 80 GSM 1 Rim',
                'category_id' => $categories['Alat Tulis Kantor']->id,
                'satuan' => 'Rim',
                'harga_beli' => 42000,
                'harga_jual' => 55000,
                'stok' => 20,
                'stok_minimum' => 5,
                'deskripsi' => 'Kertas putih bersih cocok untuk fotokopi dan cetak printer.',
            ],
            [
                'kode_produk' => 'PRD-0009',
                'nama_produk' => 'Pulpen Gel Hitam 0.5mm (Box isi 12)',
                'category_id' => $categories['Alat Tulis Kantor']->id,
                'satuan' => 'Box',
                'harga_beli' => 25000,
                'harga_jual' => 36000,
                'stok' => 18,
                'stok_minimum' => 5,
                'deskripsi' => 'Tinta lancar tidak macet, pegangan nyaman.',
            ],
            [
                'kode_produk' => 'PRD-0010',
                'nama_produk' => 'Sabun Cuci Piring Jeruk Nipis 750ml',
                'category_id' => $categories['Kebutuhan Rumah Tangga']->id,
                'satuan' => 'Pouch',
                'harga_beli' => 11000,
                'harga_jual' => 15500,
                'stok' => 35,
                'stok_minimum' => 8,
                'deskripsi' => 'Membersihkan lemak membandel dengan keharuman jeruk nipis.',
            ],
            [
                'kode_produk' => 'PRD-0011',
                'nama_produk' => 'Deterjen Bubuk Konsentrat 1kg',
                'category_id' => $categories['Kebutuhan Rumah Tangga']->id,
                'satuan' => 'Pack',
                'harga_beli' => 17500,
                'harga_jual' => 24000,
                'stok' => 22,
                'stok_minimum' => 6,
                'deskripsi' => 'Busa melimpah, wangi tahan lama hingga 7 hari.',
            ],
            [
                'kode_produk' => 'PRD-0012',
                'nama_produk' => 'Lampu Bohlam LED 12 Watt Putih',
                'category_id' => $categories['Elektronik & Aksesoris']->id,
                'satuan' => 'Pcs',
                'harga_beli' => 21000,
                'harga_jual' => 32000,
                'stok' => 2, // Stok Menipis!
                'stok_minimum' => 5,
                'deskripsi' => 'Hemat energi hingga 85%, terang merata.',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $p) {
            $createdProducts[] = Product::firstOrCreate(['kode_produk' => $p['kode_produk']], $p);
        }

        // 5. Transaksi Stok Masuk Contoh
        if (StockTransaction::where('tipe', 'masuk')->count() === 0) {
            $transMasuk = StockTransaction::create([
                'no_transaksi' => 'IN-' . date('Ymd') . '-0001',
                'tipe' => 'masuk',
                'tanggal' => Carbon::now()->subDays(3)->toDateString(),
                'supplier_id' => $suppliers[0]->id,
                'user_id' => $admin->id,
                'total_item' => 35,
                'total_nominal' => 700000,
                'keterangan' => 'Penerimaan barang pasokan awal bulan dari PT Sumber Berkah Pangan.',
            ]);

            StockTransactionDetail::create([
                'stock_transaction_id' => $transMasuk->id,
                'product_id' => $createdProducts[0]->id,
                'jumlah' => 15,
                'harga_satuan' => 28000,
                'subtotal' => 420000,
                'catatan' => 'Kemasan utuh dan baik',
            ]);

            StockTransactionDetail::create([
                'stock_transaction_id' => $transMasuk->id,
                'product_id' => $createdProducts[1]->id,
                'jumlah' => 20,
                'harga_satuan' => 14000,
                'subtotal' => 280000,
                'catatan' => 'Expired masih panjang (2028)',
            ]);
        }

        // 6. Transaksi Stok Keluar Gudang Contoh
        if (StockTransaction::where('tipe', 'keluar')->count() === 0) {
            $transKeluar = StockTransaction::create([
                'no_transaksi' => 'OUT-' . date('Ymd') . '-0001',
                'tipe' => 'keluar',
                'tanggal' => Carbon::now()->subDays(2)->toDateString(),
                'user_id' => $admin->id,
                'total_item' => 2,
                'total_nominal' => 44000,
                'keterangan' => '[Alasan: Barang Rusak] Kaleng penyok saat pemindahan palet di gudang.',
            ]);

            StockTransactionDetail::create([
                'stock_transaction_id' => $transKeluar->id,
                'product_id' => $createdProducts[2]->id,
                'jumlah' => 2,
                'harga_satuan' => 22000,
                'subtotal' => 44000,
                'catatan' => 'Penyok fisik parah',
            ]);
        }

        // 7. Transaksi Kasir Contoh
        if (Sale::count() === 0) {
            $sale1 = Sale::create([
                'no_invoice' => 'INV-' . date('Ymd') . '-0001',
                'tanggal' => Carbon::now()->subHours(4),
                'user_id' => $kasir->id,
                'nama_pelanggan' => 'Andi Wijaya',
                'total_item' => 3,
                'subtotal' => 116500,
                'diskon' => 0,
                'total_akhir' => 116500,
                'bayar' => 120000,
                'kembalian' => 3500,
                'metode_pembayaran' => 'Tunai',
                'catatan' => 'Pembeli langganan',
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $createdProducts[0]->id,
                'harga_beli' => 28000,
                'harga_jual' => 42000,
                'jumlah' => 1,
                'subtotal' => 42000,
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $createdProducts[1]->id,
                'harga_beli' => 15000,
                'harga_jual' => 19500,
                'jumlah' => 2,
                'subtotal' => 39000,
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $createdProducts[5]->id,
                'harga_beli' => 18000,
                'harga_jual' => 35000,
                'jumlah' => 1,
                'subtotal' => 35000,
            ]);
        }
    }
}
