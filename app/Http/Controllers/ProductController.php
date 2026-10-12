<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = $request->user()->isSuperAdmin();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:100'],
        ]);
        $search = $filters['search'] ?? null;
        $kategori = $filters['kategori'] ?? null;

        $query = Product::query();

        if ($search !== null && $search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($kategori && $kategori !== 'Semua') {
            $query->where('kategori', $kategori);
        }

        $products = $query->orderBy('nama_barang')->paginate(10)->withQueryString();
        $totalNilaiStok = $isSuperAdmin
            ? Product::query()->get(['stok_tersisa', 'harga_jual'])
                ->sum(fn (Product $product) => $product->stok_tersisa * $product->harga_jual)
            : null;
        $categories = Product::query()->distinct()->orderBy('kategori')->pluck('kategori');

        $stokMenipis = Product::where('stok_tersisa', '<=', 10)->where('stok_tersisa', '>', 0)->count();
        $stokHabis = Product::where('stok_tersisa', 0)->count();
        $totalJenis = Product::count();

        return view('products.index', compact(
            'products',
            'isSuperAdmin',
            'search',
            'kategori',
            'categories',
            'totalNilaiStok',
            'stokMenipis',
            'stokHabis',
            'totalJenis'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->productRules());

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->productRules($product));

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil diperbarui');
    }

    private function productRules(?Product $product = null): array
    {
        $uniqueCode = 'unique:products,kode_barang';
        if ($product) {
            $uniqueCode .= ','.$product->id;
        }

        return [
            'kode_barang' => ['required', 'string', 'max:255', $uniqueCode],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'stok_tersedia' => ['required', 'integer', 'min:0'],
            'stok_tersisa' => ['required', 'integer', 'min:0', 'lte:stok_tersedia'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
        ];
    }
}
