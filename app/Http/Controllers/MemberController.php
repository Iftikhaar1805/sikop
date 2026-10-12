<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SavingsTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = $filters['search'] ?? null;

        $query = Member::query()->withSum([
            'savingsTransactions as total_setoran' => fn ($query) => $query->where('jenis', 'SETORAN'),
        ], 'jumlah')->withSum([
            'savingsTransactions as total_penarikan' => fn ($query) => $query->where('jenis', 'PENARIKAN'),
        ], 'jumlah');

        if ($search !== null && $search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nomor_anggota', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('nama')->paginate(10)->withQueryString();
        $totalAnggota = Member::count();
        $totalSimpanan = (float) SavingsTransaction::where('jenis', 'SETORAN')->sum('jumlah')
            - (float) SavingsTransaction::where('jenis', 'PENARIKAN')->sum('jumlah');

        return view('members.index', compact('members', 'search', 'totalAnggota', 'totalSimpanan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->memberRules());
        $validated['tanggal_bergabung'] ??= now()->toDateString();

        $member = Member::create($validated);

        return redirect()->route('members.show', $member)->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Member $member)
    {
        $member->load([
            'savingsTransactions' => fn ($query) => $query->latest('tanggal')->latest('id'),
        ]);
        $saldo = $this->balanceCentsFor($member) / 100;

        return view('members.show', compact('member', 'saldo'));
    }

    public function update(Request $request, Member $member)
    {
        $member->update($request->validate($this->memberRules($member)));

        return redirect()->route('members.show', $member)->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function storeSavings(Request $request, Member $member)
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:SETORAN,PENARIKAN'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:9999999999999'],
            'petugas' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $member) {
            $lockedMember = Member::query()->lockForUpdate()->findOrFail($member->id);
            $saldoInCents = $this->balanceCentsFor($lockedMember);
            $withdrawalInCents = (int) round((float) $validated['jumlah'] * 100);

            if ($validated['jenis'] === 'PENARIKAN' && $withdrawalInCents > $saldoInCents) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah penarikan melebihi saldo simpanan anggota.',
                ]);
            }

            $lockedMember->savingsTransactions()->create([
                ...$validated,
                'tanggal' => now(),
            ]);
        });

        return redirect()->route('members.show', $member)->with('success', 'Mutasi simpanan berhasil dicatat.');
    }

    private function memberRules(?Member $member = null): array
    {
        $uniqueNumber = 'unique:members,nomor_anggota';
        if ($member) {
            $uniqueNumber .= ','.$member->id;
        }

        return [
            'nomor_anggota' => ['required', 'string', 'max:50', $uniqueNumber],
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'tanggal_bergabung' => ['required', 'date'],
        ];
    }

    private function balanceCentsFor(Member $member): int
    {
        $setoran = $member->savingsTransactions()->where('jenis', 'SETORAN')->sum('jumlah');
        $penarikan = $member->savingsTransactions()->where('jenis', 'PENARIKAN')->sum('jumlah');

        return (int) round(((float) $setoran - (float) $penarikan) * 100);
    }
}
