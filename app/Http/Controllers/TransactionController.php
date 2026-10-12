<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'jenis' => ['nullable', 'in:SEMUA,PENJUALAN,PEMBELIAN'],
            'minggu' => ['nullable', 'integer', 'between:1,4'],
        ]);
        $jenis = $filters['jenis'] ?? null;
        $minggu = $filters['minggu'] ?? null;

        $isCashier = $request->user()->isCashier();
        $query = Transaction::with('items.product')->latest();

        if ($isCashier) {
            $query->whereDate('tanggal', today());
        } elseif ($minggu) {
            $firstDay = (($minggu - 1) * 7) + 1;
            $lastDay = $minggu === 4 ? 31 : $minggu * 7;
            $query->whereDay('tanggal', '>=', $firstDay)
                ->whereDay('tanggal', '<=', $lastDay);
        }

        if ($jenis && $jenis !== 'SEMUA') {
            $query->where('jenis', $jenis);
        }

        $transactions = $query->paginate(15)->withQueryString();
        $products = Product::query()->orderBy('nama_barang')->get();

        return view('transactions.index', compact('transactions', 'products', 'jenis', 'minggu', 'isCashier'));
    }

    public function store(Request $request)
    {
        $kasirRules = $request->user()->isCashier()
            ? ['nullable', 'string', 'max:255']
            : ['required', 'string', 'max:255'];
        $validated = $request->validate([
            'jenis' => ['required', 'in:PENJUALAN,PEMBELIAN'],
            'kasir' => $kasirRules,
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);
        $validated['kasir'] = $request->user()->isCashier()
            ? $request->user()->name
            : $validated['kasir'];

        DB::transaction(function () use ($validated) {
            $totalHarga = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $hargaSatuan = $validated['jenis'] === 'PENJUALAN' ? $product->harga_jual : $product->harga_beli;
                $subtotal = round((float) $hargaSatuan * $item['jumlah'], 2);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];

                $totalHarga += $subtotal;

                if ($validated['jenis'] === 'PENJUALAN') {
                    if ($product->stok_tersisa < $item['jumlah']) {
                        throw ValidationException::withMessages([
                            'items' => "Stok tidak mencukupi untuk {$product->nama_barang}.",
                        ]);
                    }
                    $product->stok_tersisa -= $item['jumlah'];
                } else {
                    $product->stok_tersisa += $item['jumlah'];
                    $product->stok_tersedia += $item['jumlah'];
                }
                $product->save();
            }

            $transaction = Transaction::create([
                'tanggal' => now(),
                'jenis' => $validated['jenis'],
                'total_harga' => $totalHarga,
                'kasir' => $validated['kasir'],
            ]);

            foreach ($itemsData as $data) {
                $data['transaction_id'] = $transaction->id;
                TransactionItem::create($data);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan');
    }

    public function receipt(Request $request, Transaction $transaction)
    {
        abort_if(
            $request->user()->isCashier() && ! $transaction->tanggal->isToday(),
            403
        );

        $transaction->load('items.product');

        return view('transactions.receipt', compact('transaction'));
    }
}
