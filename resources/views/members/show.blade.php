@extends('layouts.app')

@section('title', 'Simpanan anggota')

@section('content')
<a href="{{ route('members.index') }}" class="link mb-5 inline-flex items-center gap-2 text-sm no-underline">
    @include('partials.icon', ['name' => 'arrow-left', 'class' => 'size-4'])
    Kembali ke daftar anggota
</a>

@include('partials.flash', ['errorTitle' => 'Perubahan belum tersimpan:'])

<div class="grid items-start gap-6 xl:grid-cols-[24rem_minmax(0,1fr)]">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-1">
        <section data-reveal class="card p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <span class="code-chip">{{ $member->nomor_anggota }}</span>
                    <h2 class="mt-2 text-2xl font-bold leading-tight tracking-tight">{{ $member->nama }}</h2>
                </div>
                <button type="button" class="btn btn-ghost btn-sm shrink-0" data-dialog-open="edit-member">
                    @include('partials.icon', ['name' => 'edit', 'class' => 'size-4'])
                    Ubah
                </button>
            </div>

            <dl class="mt-6 space-y-4 text-sm">
                <div><dt class="text-muted">Nomor telepon</dt><dd class="mt-0.5 font-medium">{{ $member->telepon ?: 'Belum diisi' }}</dd></div>
                <div><dt class="text-muted">Alamat</dt><dd class="mt-0.5 font-medium">{{ $member->alamat ?: 'Belum diisi' }}</dd></div>
                <div><dt class="text-muted">Bergabung sejak</dt><dd class="mt-0.5 font-medium">{{ $member->tanggal_bergabung->format('d/m/Y') }}</dd></div>
            </dl>

            <dialog id="edit-member" class="modal" aria-labelledby="edit-member-title">
                <form action="{{ route('members.update', $member) }}" method="POST" class="p-6 sm:p-7">
                    @csrf
                    @method('PUT')
                    <div class="mb-5 flex items-start justify-between gap-4">
                        <h3 id="edit-member-title" class="text-xl font-semibold tracking-tight">Ubah data anggota</h3>
                        <button type="button" class="btn btn-ghost btn-sm size-8 px-0" data-dialog-close aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'class' => 'size-4'])</button>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="em-nomor">Nomor anggota</label>
                            <input id="em-nomor" name="nomor_anggota" value="{{ $member->nomor_anggota }}" class="input-field" maxlength="50" required>
                        </div>
                        <div>
                            <label class="field-label" for="em-nama">Nama</label>
                            <input id="em-nama" name="nama" value="{{ $member->nama }}" class="input-field" maxlength="255" required>
                        </div>
                        <div>
                            <label class="field-label" for="em-telp">Telepon</label>
                            <input id="em-telp" type="tel" name="telepon" value="{{ $member->telepon }}" class="input-field" maxlength="30">
                        </div>
                        <div>
                            <label class="field-label" for="em-gabung">Tanggal bergabung</label>
                            <input id="em-gabung" type="date" name="tanggal_bergabung" value="{{ $member->tanggal_bergabung->format('Y-m-d') }}" class="input-field" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="em-alamat">Alamat</label>
                            <textarea id="em-alamat" name="alamat" class="input-field" rows="2" maxlength="1000">{{ $member->alamat }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="btn btn-ghost" data-dialog-close>Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan perubahan <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button>
                    </div>
                </form>
            </dialog>
        </section>

        <section data-reveal class="card card-accent p-6">
            <p class="text-sm font-medium opacity-70">Saldo simpanan saat ini</p>
            <p class="font-display mt-4 whitespace-nowrap text-4xl leading-none sm:text-5xl"><span class="mr-1 align-top text-[0.4em] opacity-60">Rp</span>{{ number_format($saldo, 0, ',', '.') }}</p>
            <p class="mt-4 text-[0.8125rem] opacity-70">Total setoran dikurangi total penarikan.</p>
        </section>

        <section data-reveal class="card p-6 md:col-span-2 xl:col-span-1">
            <h2 class="text-lg font-semibold tracking-tight">Catat mutasi simpanan</h2>
            <form action="{{ route('members.savings.store', $member) }}" method="POST" class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                @csrf
                <div>
                    <label class="field-label" for="s-jenis">Jenis mutasi</label>
                    <select id="s-jenis" name="jenis" class="input-field" required>
                        <option value="SETORAN" {{ old('jenis') === 'SETORAN' ? 'selected' : '' }}>Setoran</option>
                        <option value="PENARIKAN" {{ old('jenis') === 'PENARIKAN' ? 'selected' : '' }}>Penarikan</option>
                    </select>
                </div>
                <div>
                    <label class="field-label" for="s-jumlah">Jumlah (Rp)</label>
                    <input id="s-jumlah" type="number" name="jumlah" data-rupiah inputmode="numeric" value="{{ old('jumlah') }}" class="input-field" min="1" max="9999999999999" step="1" required>
                </div>
                <div>
                    <label class="field-label" for="s-petugas">Nama petugas</label>
                    <input id="s-petugas" name="petugas" value="{{ old('petugas') }}" class="input-field" maxlength="255" required>
                </div>
                <div class="sm:col-span-2 xl:col-span-1">
                    <label class="field-label" for="s-ket">Keterangan <span class="field-hint">(opsional)</span></label>
                    <textarea id="s-ket" name="keterangan" class="input-field" rows="2" maxlength="1000">{{ old('keterangan') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary h-11 w-full sm:col-span-2 xl:col-span-1">Simpan mutasi <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button>
            </form>
        </section>
    </div>

    <section data-reveal class="card min-w-0 p-5 sm:p-6">
        <h2 class="text-lg font-semibold tracking-tight">Riwayat simpanan</h2>
        <p class="mt-1 text-sm text-muted">Setiap setoran dan penarikan tercatat sebagai mutasi permanen.</p>

        <div class="-mx-5 mt-4 overflow-x-auto sm:-mx-6">
            <table class="table">
                <thead>
                    <tr>
                        <th class="pl-5 sm:pl-6">Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Petugas</th>
                        <th class="num pr-5 sm:pr-6">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($member->savingsTransactions as $mutation)
                        @php $setoran = $mutation->jenis === 'SETORAN'; @endphp
                        <tr>
                            <td class="whitespace-nowrap pl-5 sm:pl-6">{{ $mutation->tanggal->format('d/m/Y H:i') }}</td>
                            <td><span class="badge {{ $setoran ? 'badge-pos' : 'badge-neg' }}">{{ $setoran ? 'Setoran' : 'Penarikan' }}</span></td>
                            <td class="text-muted">{{ $mutation->keterangan ?: '—' }}</td>
                            <td>{{ $mutation->petugas }}</td>
                            <td class="num pr-5 font-semibold sm:pr-6 {{ $setoran ? 'text-pos' : 'text-neg' }}">{{ $setoran ? '+' : '−' }} Rp {{ number_format((float) $mutation->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="!p-0">@include('partials.empty', ['object' => 'coins', 'title' => 'Belum ada mutasi', 'hint' => 'Setoran pertama akan muncul di riwayat ini.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
