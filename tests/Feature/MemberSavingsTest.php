<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberSavingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'super_admin']));
    }

    public function test_member_can_be_registered_and_searched(): void
    {
        $this->post(route('members.store'), [
            'nomor_anggota' => 'AG-001',
            'nama' => 'Siti Aminah',
            'tanggal_bergabung' => '2026-09-01',
            'telepon' => '08123456789',
            'alamat' => 'Bandung',
        ])->assertRedirect();

        $this->assertDatabaseHas('members', [
            'nomor_anggota' => 'AG-001',
            'nama' => 'Siti Aminah',
        ]);

        $this->get(route('members.index', ['search' => 'AG-001']))
            ->assertOk()
            ->assertSee('Siti Aminah');
    }

    public function test_deposits_and_withdrawals_update_balance_and_history(): void
    {
        $member = $this->createMember();

        $this->post(route('members.savings.store', $member), [
            'jenis' => 'SETORAN',
            'jumlah' => '100000',
            'petugas' => 'Petugas Koperasi',
            'keterangan' => 'Simpanan awal',
        ])->assertRedirect(route('members.show', $member));

        $this->post(route('members.savings.store', $member), [
            'jenis' => 'PENARIKAN',
            'jumlah' => '25000',
            'petugas' => 'Petugas Koperasi',
        ])->assertRedirect(route('members.show', $member));

        $this->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Rp 75.000')
            ->assertSee('Simpanan awal')
            ->assertSee('SETORAN')
            ->assertSee('PENARIKAN');

        $this->assertDatabaseCount('savings_transactions', 2);
    }

    public function test_withdrawal_cannot_exceed_member_savings_balance(): void
    {
        $member = $this->createMember();
        $member->savingsTransactions()->create([
            'jenis' => 'SETORAN',
            'jumlah' => 10000,
            'petugas' => 'Petugas Koperasi',
            'tanggal' => now(),
        ]);

        $this->from(route('members.show', $member))
            ->post(route('members.savings.store', $member), [
                'jenis' => 'PENARIKAN',
                'jumlah' => 10001,
                'petugas' => 'Petugas Koperasi',
            ])
            ->assertRedirect(route('members.show', $member))
            ->assertSessionHasErrors('jumlah');

        $this->assertDatabaseCount('savings_transactions', 1);
        $this->assertSame('10000.00', $member->fresh()->saldo_simpanan);
    }

    public function test_member_detail_shows_zero_balance_before_any_transactions(): void
    {
        $member = $this->createMember();

        $this->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Saldo Simpanan Saat Ini')
            ->assertSee('Rp 0');
    }

    private function createMember(): Member
    {
        return Member::create([
            'nomor_anggota' => 'AG-001',
            'nama' => 'Anggota Uji',
            'tanggal_bergabung' => '2026-09-01',
        ]);
    }
}
