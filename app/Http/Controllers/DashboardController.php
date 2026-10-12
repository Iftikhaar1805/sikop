<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\SavingsTransaction;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $transactionsQuery = Transaction::with('items')->latest();
        if (! $isSuperAdmin) {
            $transactionsQuery->whereDate('tanggal', today());
        }
        $transactions = $transactionsQuery->take(5)->get();
        $stokMenipis = Product::where('stok_tersisa', '<=', 10)->where('stok_tersisa', '>', 0)->count();
        $totalItem = Product::count();

        $totalPemasukan = null;
        $totalPengeluaran = null;
        $selisihKas = null;
        $totalAnggota = null;
        $totalSimpanan = null;
        if ($isSuperAdmin) {
            $selisihKas = Transaction::where('jenis', 'PENJUALAN')->sum('total_harga')
                - Transaction::where('jenis', 'PEMBELIAN')->sum('total_harga');
            $totalPemasukan = Transaction::where('jenis', 'PENJUALAN')->sum('total_harga');
            $totalPengeluaran = Transaction::where('jenis', 'PEMBELIAN')->sum('total_harga');
            $totalAnggota = Member::count();
            $totalSimpanan = (float) SavingsTransaction::where('jenis', 'SETORAN')->sum('jumlah')
                - (float) SavingsTransaction::where('jenis', 'PENARIKAN')->sum('jumlah');
        }

        return view('dashboard', compact(
            'isSuperAdmin',
            'totalPemasukan',
            'totalPengeluaran',
            'selisihKas',
            'stokMenipis',
            'totalItem',
            'totalAnggota',
            'totalSimpanan',
            'transactions'
        ));
    }
}
