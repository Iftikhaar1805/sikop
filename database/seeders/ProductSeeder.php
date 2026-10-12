<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['kode_barang' => 'BRG-001', 'nama_barang' => 'Buku Tulis Sidu 38 Lembar', 'kategori' => 'Alat Tulis', 'stok_tersedia' => 100, 'stok_tersisa' => 72, 'harga_beli' => 3000, 'harga_jual' => 4500, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-002', 'nama_barang' => 'Pulpen Pilot G2 Hitam', 'kategori' => 'Alat Tulis', 'stok_tersedia' => 80, 'stok_tersisa' => 55, 'harga_beli' => 12000, 'harga_jual' => 15000, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-003', 'nama_barang' => 'Pensil 2B Faber Castell', 'kategori' => 'Alat Tulis', 'stok_tersedia' => 120, 'stok_tersisa' => 95, 'harga_beli' => 3500, 'harga_jual' => 5000, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-004', 'nama_barang' => 'Penggaris Plastik 30cm', 'kategori' => 'Alat Tulis', 'stok_tersedia' => 50, 'stok_tersisa' => 40, 'harga_beli' => 2000, 'harga_jual' => 3500, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-005', 'nama_barang' => 'Dasi SMA Putra/Putri', 'kategori' => 'Seragam & Atribut', 'stok_tersedia' => 40, 'stok_tersisa' => 28, 'harga_beli' => 10000, 'harga_jual' => 15000, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-006', 'nama_barang' => 'Sabuk Logo Sekolah', 'kategori' => 'Seragam & Atribut', 'stok_tersedia' => 35, 'stok_tersisa' => 22, 'harga_beli' => 18000, 'harga_jual' => 25000, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-007', 'nama_barang' => 'Kaos Kaki Putih Polos', 'kategori' => 'Seragam & Atribut', 'stok_tersedia' => 60, 'stok_tersisa' => 45, 'harga_beli' => 8000, 'harga_jual' => 12000, 'satuan' => 'Pasang'],
            ['kode_barang' => 'BRG-008', 'nama_barang' => 'Air Mineral 600ml', 'kategori' => 'Makanan & Minuman', 'stok_tersedia' => 150, 'stok_tersisa' => 110, 'harga_beli' => 2200, 'harga_jual' => 3500, 'satuan' => 'Botol'],
            ['kode_barang' => 'BRG-009', 'nama_barang' => 'Roti Bakery Cokelat', 'kategori' => 'Makanan & Minuman', 'stok_tersedia' => 50, 'stok_tersisa' => 18, 'harga_beli' => 4000, 'harga_jual' => 6000, 'satuan' => 'Pcs'],
            ['kode_barang' => 'BRG-010', 'nama_barang' => 'Buku Gambar A4', 'kategori' => 'Alat Tulis', 'stok_tersedia' => 45, 'stok_tersisa' => 30, 'harga_beli' => 4500, 'harga_jual' => 7000, 'satuan' => 'Pcs'],
        ];

        DB::table('products')->insert($products);
    }
}
