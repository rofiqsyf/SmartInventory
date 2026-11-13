<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        $products = Product::where('status', true)
            ->where('stok', '>', 0)
            ->with('category')
            ->orderBy('nama_produk')
            ->get();

        // Invoice code suggestion
        $todayStr = date('Ymd');
        $countToday = Sale::whereDate('created_at', date('Y-m-d'))->count();
        $suggestedInvoice = 'INV-' . $todayStr . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('pos.index', compact('categories', 'products', 'suggestedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_invoice' => ['required', 'string', 'unique:sales,no_invoice'],
            'nama_pelanggan' => ['nullable', 'string', 'max:150'],
            'metode_pembayaran' => ['required', 'string'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'bayar' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Keranjang belanja masih kosong! Silakan pilih produk terlebih dahulu.',
            'bayar.required' => 'Nominal pembayaran wajib diisi.',
        ]);

        // Verifikasi ketersediaan stok teragregasi
        $productTotals = [];
        foreach ($validated['items'] as $item) {
            $pid = $item['product_id'];
            $productTotals[$pid] = ($productTotals[$pid] ?? 0) + $item['jumlah'];
        }

        foreach ($productTotals as $pid => $totalQty) {
            $product = Product::find($pid);
            if (!$product || $totalQty > $product->stok) {
                $stockAvailable = $product ? $product->stok : 0;
                $productName = $product ? $product->nama_produk : 'Produk';
                return response()->json([
                    'success' => false,
                    'message' => "Stok produk '{$productName}' tidak mencukupi (Tersedia: {$stockAvailable}, Diminta: {$totalQty})."
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $totalItem = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $product->harga_jual * $item['jumlah'];
                $subtotal += $itemSubtotal;
                $totalItem += $item['jumlah'];

                $itemsData[] = [
                    'product_id' => $product->id,
                    'harga_beli' => $product->harga_beli,
                    'harga_jual' => $product->harga_jual,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            $diskon = floatval($validated['diskon'] ?? 0);
            $totalAkhir = max(0, $subtotal - $diskon);
            $bayar = floatval($validated['bayar']);

            if ($bayar < $totalAkhir) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah uang bayar kurang dari total belanja (Total: Rp ' . number_format($totalAkhir, 0, ',', '.') . ', Bayar: Rp ' . number_format($bayar, 0, ',', '.') . ').'
                ], 422);
            }

            $kembalian = $bayar - $totalAkhir;

            $sale = Sale::create([
                'no_invoice' => $validated['no_invoice'],
                'tanggal' => Carbon::now(),
                'user_id' => Auth::id(),
                'nama_pelanggan' => $validated['nama_pelanggan'] ?: 'Pelanggan Umum',
                'total_item' => $totalItem,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total_akhir' => $totalAkhir,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($itemsData as $itemData) {
                $itemData['sale_id'] = $sale->id;
                SaleItem::create($itemData);

                // Kurangi stok produk
                $prod = Product::find($itemData['product_id']);
                $prod->decrement('stok', $itemData['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'sale_id' => $sale->id,
                'no_invoice' => $sale->no_invoice,
                'receipt_url' => route('pos.receipt', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('pos.receipt', compact('sale'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $query = Sale::with(['user', 'items.product']);

        // Kasir hanya melihat transaksi miliknya, Admin Gudang bisa melihat semua
        if ($user->isKasir()) {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_invoice', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->where('tanggal', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('tanggal', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $sales = $query->latest('tanggal')->latest('id')->paginate(10)->withQueryString();

        return view('pos.history', compact('sales', 'startDate', 'endDate', 'search'));
    }
}

