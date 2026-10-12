<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_and_can_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'kasir@example.test',
            'password' => Hash::make('secret-password'),
        ]);

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->post(route('login.store'), [
            'email' => 'kasir@example.test',
            'password' => 'secret-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_cashier_is_limited_to_today_and_cannot_access_admin_pages(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        Transaction::create([
            'tanggal' => today()->setTime(9, 0),
            'jenis' => 'PENJUALAN',
            'total_harga' => 5000,
            'kasir' => 'Kasir Hari Ini',
        ]);
        Transaction::create([
            'tanggal' => today()->subDay()->setTime(9, 0),
            'jenis' => 'PENJUALAN',
            'total_harga' => 7000,
            'kasir' => 'Kasir Kemarin',
        ]);

        $this->actingAs($cashier)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Kasir Hari Ini')
            ->assertDontSee('Kasir Kemarin');

        $this->get(route('reports.index'))->assertForbidden();
        $this->get(route('members.index'))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
        $this->post(route('products.store'), [])->assertForbidden();
        $this->get(route('products.index'))->assertOk()->assertDontSee('Nilai Stok (Jual)');
        $this->get(route('dashboard'))->assertOk()
            ->assertSee('Transaksi Hari Ini')
            ->assertDontSee('Total Pemasukan');
        $this->get(route('transactions.receipt', 2))->assertForbidden();
        $this->get(route('transactions.receipt', 1))->assertOk();
    }

    public function test_cashier_transaction_uses_authenticated_name_and_can_restock(): void
    {
        $cashier = User::factory()->create(['name' => 'Kasir Aktif', 'role' => 'cashier']);
        $product = Product::create([
            'kode_barang' => 'RESTOCK-1',
            'nama_barang' => 'Buku',
            'kategori' => 'Alat Tulis',
            'stok_tersedia' => 3,
            'stok_tersisa' => 3,
            'harga_beli' => 2000,
            'harga_jual' => 3000,
            'satuan' => 'Pcs',
        ]);

        $this->actingAs($cashier)->post(route('transactions.store'), [
            'jenis' => 'PEMBELIAN',
            'kasir' => 'Nama Palsu',
            'items' => [['product_id' => $product->id, 'jumlah' => 2]],
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'jenis' => 'PEMBELIAN',
            'kasir' => 'Kasir Aktif',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok_tersedia' => 5,
            'stok_tersisa' => 5,
        ]);
    }

    public function test_super_admin_can_create_cashier_but_cannot_assign_privileged_role_through_form(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Kasir Baru',
            'email' => 'kasir-baru@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'super_admin',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'kasir-baru@example.test',
            'role' => 'cashier',
        ]);
        $this->get(route('reports.index'))->assertOk();
    }

    public function test_admin_seeder_creates_admin_with_configured_name_email_and_password(): void
    {
        config([
            'koperasi.admin.name' => 'Admin Pengujian',
            'koperasi.admin.email' => 'admin-pengujian@example.test',
            'koperasi.admin.password' => 'kata-sandi-uji',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'admin-pengujian@example.test')->firstOrFail();

        $this->assertSame('Admin Pengujian', $admin->name);
        $this->assertSame('super_admin', $admin->role);
        $this->assertTrue(Hash::check('kata-sandi-uji', $admin->password));

        $this->post(route('login.store'), [
            'email' => 'admin-pengujian@example.test',
            'password' => 'kata-sandi-uji',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }
}
