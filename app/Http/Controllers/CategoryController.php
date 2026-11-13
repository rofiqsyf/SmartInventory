<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $categories = Category::withCount('products')
            ->when($search, function ($query, $search) {
                return $query->where('nama_kategori', 'like', "%{$search}%")
                             ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:categories,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori ini sudah terdaftar.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:categories,nama_kategori,' . $category->id],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori ini sudah digunakan oleh kategori lain.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

