<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
        ]);
        $bulan = (int) ($filters['bulan'] ?? now()->month);
        $tahun = (int) ($filters['tahun'] ?? now()->year);

        $transactions = Transaction::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get(['tanggal', 'jenis', 'total_harga']);

        $totalPemasukan = $transactions->where('jenis', 'PENJUALAN')->sum('total_harga');
        $totalPengeluaran = $transactions->where('jenis', 'PEMBELIAN')->sum('total_harga');
        $selisihKas = $totalPemasukan - $totalPengeluaran;
        $totalTransaksi = $transactions->count();

        // Calculate weekly breakdown
        $mingguan = [];
        for ($i = 1; $i <= 4; $i++) {
            $mingguan[$i] = [
                'pemasukan' => 0,
                'pengeluaran' => 0,
                'selisih_kas' => 0,
                'transaksi' => 0,
            ];
        }

        foreach ($transactions as $t) {
            $day = $t->tanggal->day;
            $week = 4;
            if ($day <= 7) {
                $week = 1;
            } elseif ($day <= 14) {
                $week = 2;
            } elseif ($day <= 21) {
                $week = 3;
            }

            if ($t->jenis == 'PENJUALAN') {
                $mingguan[$week]['pemasukan'] += $t->total_harga;
            } else {
                $mingguan[$week]['pengeluaran'] += $t->total_harga;
            }
            $mingguan[$week]['selisih_kas'] = $mingguan[$week]['pemasukan'] - $mingguan[$week]['pengeluaran'];
            $mingguan[$week]['transaksi']++;
        }

        return view('reports.index', compact(
            'bulan',
            'tahun',
            'totalPemasukan',
            'totalPengeluaran',
            'selisihKas',
            'totalTransaksi',
            'mingguan'
        ));
    }
}
