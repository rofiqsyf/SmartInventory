<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Product;
use App\Models\Supplier;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $supplierId = $request->query('supplier_id');

        $query = StockTransaction::where('tipe', 'masuk')
            ->with(['supplier', 'user', 'details.product']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $stockIns = $query->latest('tanggal')->latest('id')->paginate(10)->withQueryString();
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('stock_in.index', compact('stockIns', 'suppliers', 'search', 'startDate', 'endDate', 'supplierId'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $products = Product::where('status', true)->orderBy('nama_produk')->get();

        // Generate Transaction Code (e.g. IN-20261113-0001)
        $todayStr = date('Ymd');
        $countToday = StockTransaction::where('tipe', 'masuk')
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        $suggestedCode = 'IN-' . $todayStr . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('stock_in.create', compact('suppliers', 'products', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_transaksi' => ['required', 'string', 'unique:stock_transactions,no_transaksi'],
            'tanggal' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Minimal pilih 1 produk untuk stok masuk.',
            'items.*.jumlah.min' => 'Jumlah barang masuk minimal 1.',
        ]);

        DB::beginTransaction();
        try {
            $totalItem = 0;
            $totalNominal = 0;

            foreach ($validated['items'] as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $totalItem += $item['jumlah'];
                $totalNominal += $subtotal;
            }

            $transaction = StockTransaction::create([
                'no_transaksi' => $validated['no_transaksi'],
                'tipe' => 'masuk',
                'tanggal' => $validated['tanggal'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'total_item' => $totalItem,
                'total_nominal' => $totalNominal,
                'keterangan' => $validated['keterangan'],
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];

                StockTransactionDetail::create([
                    'stock_transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // Update stok produk & perbarui harga beli terbaru jika diisi
                $product = Product::findOrFail($item['product_id']);
                $product->increment('stok', $item['jumlah']);
                if ($item['harga_satuan'] > 0) {
                    $product->update(['harga_beli' => $item['harga_satuan']]);
                }
            }

            DB::commit();

            return redirect()->route('stock-in.show', $transaction->id)
                ->with('success', 'Transaksi Stok Masuk berhasil dicatat dan stok produk telah bertambah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function show(StockTransaction $stockIn)
    {
        if ($stockIn->tipe !== 'masuk') {
            abort(404);
        }

        $stockIn->load(['supplier', 'user', 'details.product']);
        return view('stock_in.show', compact('stockIn'));
    }

    public function print(StockTransaction $stockIn)
    {
        if ($stockIn->tipe !== 'masuk') {
            abort(404);
        }

        $stockIn->load(['supplier', 'user', 'details.product']);
        return view('stock_in.print', compact('stockIn'));
    }
}

