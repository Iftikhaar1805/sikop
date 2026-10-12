@extends('layouts.app')

@section('title', 'Anggota & simpanan')

@section('content')
<div class="mb-4 grid gap-4 sm:grid-cols-5">
    <section data-reveal class="card p-6 sm:col-span-2 sm:p-7">
        <p class="text-sm text-muted">Jumlah anggota</p>
        <p class="font-display mt-6 text-6xl leading-none">{{ number_format($totalAnggota, 0, ',', '.') }}</p>
    </section>
    <section data-reveal class="card card-accent card-photo p-6 sm:col-span-3 sm:p-7" style="--photo: url('{{ asset('images/tile-macro.jpg') }}')">
        <p class="text-sm font-medium opacity-70">Total simpanan anggota</p>
        <p class="font-display mt-6 whitespace-nowrap text-[clamp(2rem,4vw,3.75rem)] leading-none"><span class="mr-1 align-top text-[0.4em] opacity-60">Rp</span>{{ number_format((float) $totalSimpanan, 0, ',', '.') }}</p>
    </section>
</div>

@include('partials.flash', ['errorTitle' => 'Periksa kembali data anggota:'])

<section data-reveal class="card p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-lg font-semibold tracking-tight">Daftar anggota</h2>
        <form action="{{ route('members.index') }}" method="GET" class="flex w-full gap-2 sm:w-auto">
            <div class="relative flex-1 sm:w-80">
                @include('partials.icon', ['name' => 'search', 'class' => 'pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted'])
                <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama, nomor, atau telepon" aria-label="Cari anggota" class="input-field pl-9">
            </div>
            <button type="submit" class="btn btn-ghost">Cari</button>
        </form>
    </div>

    <details class="group mt-5 rounded-2xl bg-ink/[.035] ring-1 ring-ink/10" {{ $errors->any() ? 'open' : '' }}>
        <summary class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl px-4 py-3.5 text-sm font-semibold transition-colors duration-300 hover:bg-ink/5">
            <span class="flex items-center gap-2">@include('partials.icon', ['name' => 'plus', 'class' => 'size-4']) Tambah anggota baru</span>
            <span class="text-muted group-open:hidden">Buka formulir</span>
            <span class="hidden text-muted group-open:inline">Tutup</span>
        </summary>
        <form action="{{ route('members.store') }}" method="POST" class="grid grid-cols-1 gap-4 border-t border-ink/10 px-4 pb-5 pt-5 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            <div>
                <label class="field-label" for="m-nomor">Nomor anggota</label>
                <input id="m-nomor" name="nomor_anggota" value="{{ old('nomor_anggota') }}" class="input-field" maxlength="50" required>
                @error('nomor_anggota')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="field-label" for="m-nama">Nama lengkap</label>
                <input id="m-nama" name="nama" value="{{ old('nama') }}" class="input-field" maxlength="255" required>
                @error('nama')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="field-label" for="m-gabung">Tanggal bergabung</label>
                <input id="m-gabung" type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', now()->toDateString()) }}" class="input-field" required>
                @error('tanggal_bergabung')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="field-label" for="m-telp">Nomor telepon <span class="field-hint">(opsional)</span></label>
                <input id="m-telp" type="tel" name="telepon" value="{{ old('telepon') }}" class="input-field" maxlength="30">
                @error('telepon')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="field-label" for="m-alamat">Alamat <span class="field-hint">(opsional)</span></label>
                <textarea id="m-alamat" name="alamat" class="input-field" rows="2" maxlength="1000">{{ old('alamat') }}</textarea>
                @error('alamat')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="md:col-span-2 xl:col-span-3"><button type="submit" class="btn btn-primary">Simpan anggota <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button></div>
        </form>
    </details>

    <div class="-mx-5 mt-5 overflow-x-auto sm:-mx-6">
        <table class="table">
            <thead>
                <tr>
                    <th class="pl-5 sm:pl-6">Nomor</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Bergabung</th>
                    <th class="num">Saldo simpanan</th>
                    <th class="center pr-5 sm:pr-6">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    @php
                        $saldo = (float) ($member->total_setoran ?? 0) - (float) ($member->total_penarikan ?? 0);
                    @endphp
                    <tr>
                        <td class="pl-5 sm:pl-6"><span class="code-chip">{{ $member->nomor_anggota }}</span></td>
                        <td class="font-medium">{{ $member->nama }}</td>
                        <td class="text-muted">{{ $member->telepon ?: '—' }}</td>
                        <td class="whitespace-nowrap text-muted">{{ $member->tanggal_bergabung->format('d/m/Y') }}</td>
                        <td class="num font-semibold">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                        <td class="center pr-5 sm:pr-6"><a href="{{ route('members.show', $member) }}" class="link">Buka</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="!p-0">@include('partials.empty', ['object' => 'basket', 'title' => 'Belum ada anggota', 'hint' => 'Tambahkan anggota baru, atau ubah kata kunci pencarian.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $members->links() }}
</section>
@endsection
