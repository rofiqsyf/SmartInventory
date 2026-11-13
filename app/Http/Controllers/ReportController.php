<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockTransaction;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stok');
        $totalStockValue = Product::selectRaw('SUM(stok * harga_beli) as total')->value('total') ?? 0;
        
        $todaySales = Sale::whereDate('tanggal', Carbon::today())->sum('total_akhir');
        $monthlySales = Sale::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('total_akhir');

        return view('reports.index', compact(
            'totalProducts',
            'totalStock',
            'totalStockValue',
            'todaySales',
            'monthlySales'
        ));
    }

    public function stock(Request $request)
    {
        $categoryId = $request->query('category_id');
        $status = $request->query('status'); // all, normal, low, out
        $isPrint = $request->boolean('print');

        $query = Product::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status === 'low') {
            $query->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        } elseif ($status === 'out') {
            $query->where('stok', '<=', 0);
        } elseif ($status === 'normal') {
            $query->whereColumn('stok', '>', 'stok_minimum');
        }

        if ($isPrint) {
            $products = $query->orderBy('nama_produk')->get();
        } else {
            $products = $query->orderBy('nama_produk')->paginate(20)->withQueryString();
        }

        // Summary calculations
        $summaryQuery = Product::query();
        if ($categoryId) $summaryQuery->where('category_id', $categoryId);
        if ($status === 'low') $summaryQuery->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        if ($status === 'out') $summaryQuery->where('stok', '<=', 0);
        if ($status === 'normal') $summaryQuery->whereColumn('stok', '>', 'stok_minimum');

        $totalItems = $summaryQuery->count();
        $totalStock = (clone $summaryQuery)->sum('stok');
        $totalAssetValue = (clone $summaryQuery)->selectRaw('SUM(stok * harga_beli) as total')->value('total') ?? 0;
        $totalPotentialValue = (clone $summaryQuery)->selectRaw('SUM(stok * harga_jual) as total')->value('total') ?? 0;

        $categories = Category::orderBy('nama_kategori')->get();

        $viewName = $isPrint ? 'reports.stock_print' : 'reports.stock';
        return view($viewName, compact(
            'products',
            'categories',
            'categoryId',
            'status',
            'totalItems',
            'totalStock',
            'totalAssetValue',
            'totalPotentialValue'
        ));
    }

    public function stockIn(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $supplierId = $request->query('supplier_id');
        $isPrint = $request->boolean('print');

        $query = StockTransaction::where('tipe', 'masuk')
            ->with(['supplier', 'user', 'details.product'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($isPrint) {
            $transactions = $query->latest('tanggal')->latest('id')->get();
        } else {
            $transactions = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();
        }

        // Summary
        $summaryQuery = StockTransaction::where('tipe', 'masuk')
            ->whereBetween('tanggal', [$startDate, $endDate]);
        if ($supplierId) $summaryQuery->where('supplier_id', $supplierId);

        $totalTransactions = $summaryQuery->count();
        $totalItemIn = (clone $summaryQuery)->sum('total_item');
        $totalNominalIn = (clone $summaryQuery)->sum('total_nominal');

        $suppliers = Supplier::orderBy('nama_supplier')->get();

        $viewName = $isPrint ? 'reports.stock_in_print' : 'reports.stock_in';
        return view($viewName, compact(
            'transactions',
            'suppliers',
            'startDate',
            'endDate',
            'supplierId',
            'totalTransactions',
            'totalItemIn',
            'totalNominalIn'
        ));
    }

    public function stockOut(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $isPrint = $request->boolean('print');

        $query = StockTransaction::where('tipe', 'keluar')
            ->with(['user', 'details.product'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($isPrint) {
            $transactions = $query->latest('tanggal')->latest('id')->get();
        } else {
            $transactions = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();
        }

        $summaryQuery = StockTransaction::where('tipe', 'keluar')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $totalTransactions = $summaryQuery->count();
        $totalItemOut = (clone $summaryQuery)->sum('total_item');
        $totalNominalOut = (clone $summaryQuery)->sum('total_nominal');

        $viewName = $isPrint ? 'reports.stock_out_print' : 'reports.stock_out';
        return view($viewName, compact(
            'transactions',
            'startDate',
            'endDate',
            'totalTransactions',
            'totalItemOut',
            'totalNominalOut'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $userId = $request->query('user_id');
        $paymentMethod = $request->query('metode_pembayaran');
        $isPrint = $request->boolean('print');

        $query = Sale::with(['user', 'items.product'])
            ->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        // Kasir hanya bisa melihat penjualannya sendiri
        if (Auth::user()->isKasir()) {
            $query->where('user_id', Auth::id());
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }

        if ($paymentMethod) {
            $query->where('metode_pembayaran', $paymentMethod);
        }

        if ($isPrint) {
            $sales = $query->latest('tanggal')->latest('id')->get();
        } else {
            $sales = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();
        }

        // Summary calculations
        $summaryQuery = Sale::whereBetween('tanggal', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
        if (Auth::user()->isKasir()) {
            $summaryQuery->where('user_id', Auth::id());
        } elseif ($userId) {
            $summaryQuery->where('user_id', $userId);
        }
        if ($paymentMethod) {
            $summaryQuery->where('metode_pembayaran', $paymentMethod);
        }

        $totalSalesCount = $summaryQuery->count();
        $totalItemsSold = (clone $summaryQuery)->sum('total_item');
        $totalRevenue = (clone $summaryQuery)->sum('total_akhir');
        $totalDiscounts = (clone $summaryQuery)->sum('diskon');

        // Total COGS / Biaya Modal Penjualan untuk menghitung profit
        $saleIds = (clone $summaryQuery)->pluck('id');
        $totalHpp = \App\Models\SaleItem::whereIn('sale_id', $saleIds)
            ->selectRaw('SUM(harga_beli * jumlah) as total_hpp')
            ->value('total_hpp') ?? 0;
        
        $totalProfit = $totalRevenue - $totalHpp;

        $cashiers = User::where('role', 'kasir')->get();

        $viewName = $isPrint ? 'reports.sales_print' : 'reports.sales';
        return view($viewName, compact(
            'sales',
            'startDate',
            'endDate',
            'userId',
            'paymentMethod',
            'totalSalesCount',
            'totalItemsSold',
            'totalRevenue',
            'totalDiscounts',
            'totalHpp',
            'totalProfit',
            'cashiers'
        ));
    }
}

