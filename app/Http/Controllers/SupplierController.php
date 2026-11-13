<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $suppliers = Supplier::withCount('stockTransactions')
            ->when($search, function ($query, $search) {
                return $query->where('nama_supplier', 'like', "%{$search}%")
                             ->orWhere('telepon', 'like', "%{$search}%")
                             ->orWhere('alamat', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['nullable', 'string'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['nullable', 'string'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->stockTransactions()->count() > 0) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat transaksi pasokan stok.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}

