# Sistem Koperasi Sekolah

Aplikasi pengelolaan barang, transaksi, dan ringkasan keuangan koperasi. Dibangun dengan Laravel 12, PHP 8.2+, Tailwind CSS 4, dan Vite.

## Fitur

- Dashboard ringkasan pemasukan, pengeluaran, selisih kas, dan stok menipis.
- Data barang: pencarian, filter kategori, tambah, dan ubah barang.
- Transaksi penjualan dan pembelian dengan perhitungan harga dan pembaruan stok otomatis.
- Data anggota dan simpanan pribadi dengan riwayat setoran/penarikan serta validasi saldo.
- Laporan keuangan per bulan dengan rincian mingguan.
- Login berbasis peran: kasir hanya mengelola transaksi hari ini, mencetak struk, dan mencatat restok; super admin dapat mengelola seluruh fitur.
- Super admin dapat membuat akun kasir dari menu **Pengguna Kasir**.
- Nilai uang disimpan sebagai angka desimal dua digit agar presisi penyimpanan tetap terjaga.

## Menjalankan aplikasi

1. Pasang dependensi:

   ```powershell
   composer install
   npm install
   ```

2. Jika belum memiliki `.env`, salin template; jangan menimpa konfigurasi lokal yang sudah ada. Lalu buat application key:

   ```powershell
   if (-not (Test-Path .env)) { Copy-Item .env.example .env }
   php artisan key:generate
   ```

3. Atur kredensial admin awal di `.env` dengan `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD`. Gunakan kata sandi kuat dan jangan membagikan atau mengunggah file `.env`. Konfigurasikan database; SQLite digunakan secara default, sedangkan untuk MySQL pada XAMPP ubah `DB_CONNECTION`, `DB_DATABASE`, dan kredensialnya sesuai database lokal.

4. Jalankan migrasi dan data awal (seeder membuat akun super admin dari variabel di atas), lalu bangun aset UI:

   ```powershell
   php artisan migrate --seed
   npm run build
   ```

   Setelah masuk sebagai super admin, buat akun kasir melalui menu **Pengguna Kasir**. Registrasi publik tidak tersedia.

5. Jalankan server:

   ```powershell
   php artisan serve
   ```

## Pengujian

```powershell
php artisan test
```
