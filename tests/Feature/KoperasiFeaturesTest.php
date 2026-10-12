<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoperasiFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'super_admin']));
    }

    public function test_products_can_be_created_and_search_filters_stay_combined(): void
    {
        $this->post(route('products.store'), [
            'kode_barang' => 'BRG-TEST',
            'nama_barang' => 'Buku Uji',
            'kategori' => 'Alat Tulis',
            'stok_tersedia' => 20,
            'stok_tersisa' => 15,
            'harga_beli' => 2000,
            'harga_jual' => 3000,
            'satuan' => 'Pcs',
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'kode_barang' => 'BRG-TEST',
            'stok_tersisa' => 15,
        ]);

        $this->get(route('products.index', [
            'search' => 'BRG-TEST',
            'kategori' => 'Makanan & Minuman',
        ]))->assertOk()
            ->assertSee('Belum ada barang yang cocok')
            ->assertDontSee('Buku Uji');
    }

    public function test_sale_reduces_stock_and_records_transaction_items(): void
    {
        $product = $this->createProduct();

        $this->post(route('transactions.store'), [
            'jenis' => 'PENJUALAN',
            'kasir' => 'Petugas Uji',
            'items' => [
                ['product_id' => $product->id, 'jumlah' => 3],
            ],
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok_tersedia' => 10,
            'stok_tersisa' => 7,
        ]);
        $this->assertDatabaseHas('transactions', [
            'jenis' => 'PENJUALAN',
            'total_harga' => 15000,
        ]);
        $this->assertDatabaseHas('transaction_items', [
            'product_id' => $product->id,
            'jumlah' => 3,
            'subtotal' => 15000,
        ]);
    }

    public function test_sale_with_insufficient_stock_does_not_create_transaction_or_change_stock(): void
    {
        $product = $this->createProduct();

        $this->from(route('transactions.index'))
            ->post(route('transactions.store'), [
                'jenis' => 'PENJUALAN',
                'kasir' => 'Petugas Uji',
                'items' => [
                    ['product_id' => $product->id, 'jumlah' => 11],
                ],
            ])
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok_tersedia' => 10,
            'stok_tersisa' => 10,
        ]);
    }

    public function test_purchase_increases_available_and_remaining_stock(): void
    {
        $product = $this->createProduct();

        $this->post(route('transactions.store'), [
            'jenis' => 'PEMBELIAN',
            'kasir' => 'Petugas Uji',
            'items' => [
                ['product_id' => $product->id, 'jumlah' => 4],
            ],
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok_tersedia' => 14,
            'stok_tersisa' => 14,
        ]);
        $this->assertDatabaseHas('transactions', [
            'jenis' => 'PEMBELIAN',
            'total_harga' => 16000,
        ]);
    }

    public function test_transaction_week_filter_limits_results_to_the_selected_days(): void
    {
        Transaction::create([
            'tanggal' => '2026-09-05 10:00:00',
            'jenis' => 'PENJUALAN',
            'total_harga' => 5000,
            'kasir' => 'Kasir Minggu Satu',
        ]);
        Transaction::create([
            'tanggal' => '2026-09-12 10:00:00',
            'jenis' => 'PENJUALAN',
            'total_harga' => 5000,
            'kasir' => 'Kasir Minggu Dua',
        ]);

        $this->get(route('transactions.index', ['minggu' => 1]))
            ->assertOk()
            ->assertSee('Kasir Minggu Satu')
            ->assertDontSee('Kasir Minggu Dua');
    }

    public function test_monthly_report_summarizes_financial_transactions(): void
    {
        Transaction::create([
            'tanggal' => '2026-09-10 10:00:00',
            'jenis' => 'PENJUALAN',
            'total_harga' => 25000,
            'kasir' => 'Petugas Uji',
        ]);
        Transaction::create([
            'tanggal' => '2026-09-18 10:00:00',
            'jenis' => 'PEMBELIAN',
            'total_harga' => 12000,
            'kasir' => 'Petugas Uji',
        ]);
        Transaction::create([
            'tanggal' => '2026-08-18 10:00:00',
            'jenis' => 'PENJUALAN',
            'total_harga' => 99000,
            'kasir' => 'Petugas Uji',
        ]);

        $this->get(route('reports.index', ['bulan' => 9, 'tahun' => 2026]))
            ->assertOk()
            ->assertSee('25.000')
            ->assertSee('12.000')
            ->assertSee('13.000')
            ->assertSee('September 2026');
    }

    private function createProduct(): Product
    {
        return Product::create([
            'kode_barang' => 'BRG-TEST',
            'nama_barang' => 'Barang Uji',
            'kategori' => 'Alat Tulis',
            'stok_tersedia' => 10,
            'stok_tersisa' => 10,
            'harga_beli' => 4000,
            'harga_jual' => 5000,
            'satuan' => 'Pcs',
        ]);
    }
}
