<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdminGudang()) {
            return $this->adminGudangDashboard();
        } else {
            return $this->kasirDashboard();
        }
    }

    private function adminGudangDashboard()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stok');
        
        // Nilai aset inventori (stok * harga_beli)
        $totalAssetValue = Product::selectRaw('SUM(stok * harga_beli) as total')->value('total') ?? 0;
        $totalPotentialRevenue = Product::selectRaw('SUM(stok * harga_jual) as total')->value('total') ?? 0;

        // Produk dengan stok menipis (<= stok_minimum)
        $lowStockCount = Product::whereColumn('stok', '<=', 'stok_minimum')->count();
        $lowStockProducts = Product::with('category')
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();

        // Statistik bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stockInThisMonth = StockTransaction::where('tipe', 'masuk')
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('total_item');

        $stockOutThisMonth = StockTransaction::where('tipe', 'keluar')
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('total_item');

        $salesThisMonth = Sale::whereBetween('tanggal', [$startOfMonth->toDateTimeString(), $endOfMonth->toDateTimeString()])
            ->sum('total_akhir');

        // Transaksi stok terbaru
        $recentStockIns = StockTransaction::with(['supplier', 'user'])
            ->where('tipe', 'masuk')
            ->latest('tanggal')
            ->latest('id')
            ->take(5)
            ->get();

        $recentStockOuts = StockTransaction::with(['user'])
            ->where('tipe', 'keluar')
            ->latest('tanggal')
            ->latest('id')
            ->take(5)
            ->get();

        // Grafik 7 Hari Terakhir
        $chartDates = [];
        $chartStockIn = [];
        $chartStockOut = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::parse($date)->translatedFormat('d M');

            $in = StockTransaction::where('tipe', 'masuk')
                ->whereDate('tanggal', $date)
                ->sum('total_item');
            $outGudang = StockTransaction::where('tipe', 'keluar')
                ->whereDate('tanggal', $date)
                ->sum('total_item');
            $outKasir = Sale::whereDate('tanggal', $date)
                ->sum('total_item');

            $chartStockIn[] = (int) $in;
            $chartStockOut[] = (int) ($outGudang + $outKasir);
        }

        return view('dashboard.admin', compact(
            'totalProducts',
            'totalStock',
            'totalAssetValue',
            'totalPotentialRevenue',
            'lowStockCount',
            'lowStockProducts',
            'stockInThisMonth',
            'stockOutThisMonth',
            'salesThisMonth',
            'recentStockIns',
            'recentStockOuts',
            'chartDates',
            'chartStockIn',
            'chartStockOut'
        ));
    }

    private function kasirDashboard()
    {
        $today = Carbon::today();
        $user = Auth::user();

        // Penjualan hari ini oleh kasir ini
        $todaySalesCount = Sale::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->count();

        $todayRevenue = Sale::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->sum('total_akhir');

        $todayItemsSold = Sale::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->sum('total_item');

        $recentSales = Sale::with('items.product')
            ->where('user_id', $user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $availableProductsCount = Product::where('stok', '>', 0)->where('status', true)->count();

        return view('dashboard.kasir', compact(
            'todaySalesCount',
            'todayRevenue',
            'todayItemsSold',
            'recentSales',
            'availableProductsCount'
        ));
    }
}

