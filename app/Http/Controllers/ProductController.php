<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $filterStock = $request->query('stock_status'); // 'all', 'low', 'out'

        $query = Product::with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($filterStock === 'low') {
            $query->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        } elseif ($filterStock === 'out') {
            $query->where('stok', '<=', 0);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'filterStock'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        
        // Auto-generate kode produk default (e.g. PRD-0001)
        $lastProduct = Product::latest('id')->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        $suggestedCode = 'PRD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('products.create', compact('categories', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products,kode_produk'],
            'nama_produk' => ['required', 'string', 'max:200'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kode_produk.required' => 'Kode produk wajib diisi.',
            'kode_produk.unique' => 'Kode produk sudah digunakan oleh produk lain.',
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'satuan.required' => 'Satuan produk wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'stok.required' => 'Stok awal wajib diisi.',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/products');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $file->move($destinationPath, $filename);
            $validated['gambar'] = 'uploads/products/' . $filename;
        }

        $product = Product::create($validated);

        // Catat mutasi stok awal jika stok diisi lebih dari 0
        if ($product->stok > 0) {
            $todayStr = date('Ymd');
            $countToday = \App\Models\StockTransaction::where('tipe', 'masuk')->whereDate('created_at', date('Y-m-d'))->count();
            $transCode = 'IN-' . $todayStr . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

            $trans = \App\Models\StockTransaction::create([
                'no_transaksi' => $transCode,
                'tipe' => 'masuk',
                'tanggal' => date('Y-m-d'),
                'supplier_id' => null,
                'user_id' => auth()->id() ?? 1,
                'total_item' => $product->stok,
                'total_nominal' => $product->stok * $product->harga_beli,
                'keterangan' => 'Stok awal saat pendaftaran produk baru (' . $product->nama_produk . ')',
            ]);

            \App\Models\StockTransactionDetail::create([
                'stock_transaction_id' => $trans->id,
                'product_id' => $product->id,
                'jumlah' => $product->stok,
                'harga_satuan' => $product->harga_beli,
                'subtotal' => $product->stok * $product->harga_beli,
                'catatan' => 'Saldo Stok Awal',
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stockTransactionDetails.transaction.supplier', 'saleItems.sale.user']);

        // Riwayat mutasi stok produk (gabungan stok masuk, keluar gudang, dan penjualan)
        $stockIns = $product->stockTransactionDetails()
            ->whereHas('transaction', fn($q) => $q->where('tipe', 'masuk'))
            ->with('transaction.supplier')
            ->latest()
            ->take(10)
            ->get();

        $stockOutsGudang = $product->stockTransactionDetails()
            ->whereHas('transaction', fn($q) => $q->where('tipe', 'keluar'))
            ->with('transaction')
            ->latest()
            ->take(10)
            ->get();

        $salesHistory = $product->saleItems()
            ->with('sale.user')
            ->latest()
            ->take(10)
            ->get();

        return view('products.show', compact('product', 'stockIns', 'stockOutsGudang', 'salesHistory'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products,kode_produk,' . $product->id],
            'nama_produk' => ['required', 'string', 'max:200'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($product->gambar && File::exists(public_path($product->gambar))) {
                File::delete(public_path($product->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/products');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $file->move($destinationPath, $filename);
            $validated['gambar'] = 'uploads/products/' . $filename;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Cek apakah ada riwayat transaksi
        if ($product->stockTransactionDetails()->count() > 0 || $product->saleItems()->count() > 0) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi stok atau penjualan.');
        }

        if ($product->gambar && File::exists(public_path($product->gambar))) {
            File::delete(public_path($product->gambar));
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}

