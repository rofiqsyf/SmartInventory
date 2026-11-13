<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Product;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = StockTransaction::where('tipe', 'keluar')
            ->with(['user', 'details.product']);

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

        $stockOuts = $query->latest('tanggal')->latest('id')->paginate(10)->withQueryString();

        return view('stock_out.index', compact('stockOuts', 'search', 'startDate', 'endDate'));
    }

    public function create()
    {
        // Hanya produk dengan stok > 0 yang bisa dikeluarkan
        $products = Product::where('status', true)->where('stok', '>', 0)->orderBy('nama_produk')->get();

        $todayStr = date('Ymd');
        $countToday = StockTransaction::where('tipe', 'keluar')
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        $suggestedCode = 'OUT-' . $todayStr . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('stock_out.create', compact('products', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_transaksi' => ['required', 'string', 'unique:stock_transactions,no_transaksi'],
            'tanggal' => ['required', 'date'],
            'alasan' => ['required', 'string'], // Rusak, Kadaluarsa, Retur Supplier, Pemakaian Internal, Lainnya
            'keterangan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'alasan.required' => 'Pilih alasan pengeluaran stok.',
            'items.required' => 'Minimal pilih 1 produk untuk dikeluarkan.',
        ]);

        // Validasi total stok cukup untuk setiap produk (mencegah duplikasi baris produk melebihi stok)
        $productTotals = [];
        foreach ($validated['items'] as $item) {
            $pid = $item['product_id'];
            $productTotals[$pid] = ($productTotals[$pid] ?? 0) + $item['jumlah'];
        }

        foreach ($productTotals as $pid => $totalQty) {
            $prod = Product::find($pid);
            if ($prod && $totalQty > $prod->stok) {
                return back()->withInput()->with('error', "Stok untuk produk '{$prod->nama_produk}' tidak mencukupi (Tersedia: {$prod->stok} {$prod->satuan}, Diminta: {$totalQty} {$prod->satuan}).");
            }
        }

        DB::beginTransaction();
        try {
            $totalItem = 0;
            $totalNominal = 0;

            foreach ($validated['items'] as $item) {
                $prod = Product::find($item['product_id']);
                $subtotal = $item['jumlah'] * $prod->harga_beli;
                $totalItem += $item['jumlah'];
                $totalNominal += $subtotal;
            }

            $catatanLengkap = "[Alasan: {$validated['alasan']}] " . ($validated['keterangan'] ?? '');

            $transaction = StockTransaction::create([
                'no_transaksi' => $validated['no_transaksi'],
                'tipe' => 'keluar',
                'tanggal' => $validated['tanggal'],
                'user_id' => Auth::id(),
                'total_item' => $totalItem,
                'total_nominal' => $totalNominal,
                'keterangan' => $catatanLengkap,
            ]);

            foreach ($validated['items'] as $item) {
                $prod = Product::findOrFail($item['product_id']);
                $subtotal = $item['jumlah'] * $prod->harga_beli;

                StockTransactionDetail::create([
                    'stock_transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $prod->harga_beli,
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // Kurangi stok produk
                $prod->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            return redirect()->route('stock-out.show', $transaction->id)
                ->with('success', 'Transaksi Stok Keluar Gudang berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(StockTransaction $stockOut)
    {
        if ($stockOut->tipe !== 'keluar') {
            abort(404);
        }

        $stockOut->load(['user', 'details.product']);
        return view('stock_out.show', compact('stockOut'));
    }

    public function print(StockTransaction $stockOut)
    {
        if ($stockOut->tipe !== 'keluar') {
            abort(404);
        }

        $stockOut->load(['user', 'details.product']);
        return view('stock_out.print', compact('stockOut'));
    }
}

