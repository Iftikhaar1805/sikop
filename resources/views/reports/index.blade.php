@extends('layouts.app')

@section('title', 'Laporan keuangan')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $selisih = (float) $selisihKas;
@endphp

<section data-reveal class="card mb-6 p-5 sm:p-6">
    <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold tracking-tight">Periode laporan</h2>
            <p class="mt-0.5 text-sm text-muted">{{ $namaBulan[$bulan] }} {{ $tahun }}</p>
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="field-label" for="r-bulan">Bulan</label>
                <select id="r-bulan" name="bulan" class="input-field w-44" required>
                    @foreach($namaBulan as $nomor => $nama)
                        <option value="{{ $nomor }}" {{ $bulan === $nomor ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label" for="r-tahun">Tahun</label>
                <input id="r-tahun" type="number" name="tahun" value="{{ $tahun }}" min="2000" max="2100" class="input-field w-28" required>
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button>
        </div>
    </form>
</section>

<div class="grid gap-6 lg:grid-cols-12">
    <section data-reveal class="card card-glow p-6 sm:p-8 lg:col-span-5">
        <p class="text-sm text-muted">Selisih kas {{ $namaBulan[$bulan] }}</p>
        <p class="font-display mt-6 whitespace-nowrap text-[clamp(2.25rem,4vw,3.75rem)] leading-none {{ $selisih >= 0 ? 'text-pos' : 'text-neg' }}">
            {{ $selisih < 0 ? '−' : '' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
        </p>
        <p class="mt-4 text-sm text-muted">Dari {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi pada periode ini.</p>
    </section>

    <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
        <section data-reveal class="card p-6">
            <p class="flex items-center gap-2 text-sm text-muted"><span class="size-2 rounded-full bg-pos"></span>Pemasukan penjualan</p>
            <p class="mt-8 text-2xl font-semibold tracking-tight">Rp {{ number_format((float) $totalPemasukan, 0, ',', '.') }}</p>
        </section>
        <section data-reveal class="card p-6">
            <p class="flex items-center gap-2 text-sm text-muted"><span class="size-2 rounded-full bg-neg"></span>Pengeluaran pembelian</p>
            <p class="mt-8 text-2xl font-semibold tracking-tight">Rp {{ number_format((float) $totalPengeluaran, 0, ',', '.') }}</p>
        </section>
    </div>
</div>

@php
    $nilaiMaks = max(1, (float) collect($mingguan)->flatMap(fn ($w) => [(float) $w['pemasukan'], (float) $w['pengeluaran']])->max());
    $magnitude = 10 ** floor(log10($nilaiMaks));
    $norm = $nilaiMaks / $magnitude;
    $langit = ($norm <= 1 ? 1 : ($norm <= 2 ? 2 : ($norm <= 5 ? 5 : 10))) * $magnitude;
    $ringkas = function ($v) {
        if ($v >= 1000000) { return rtrim(rtrim(number_format($v / 1000000, 2, ',', '.'), '0'), ',') . ' jt'; }
        if ($v >= 1000) { return number_format($v / 1000, 0, ',', '.') . ' rb'; }
        return number_format($v, 0, ',', '.');
    };
    $tinggi = fn ($v) => $v <= 0 ? 0 : max(2.5, round($v / $langit * 100, 1));
@endphp

<section data-reveal class="card mt-6 p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold tracking-tight">Arus kas per minggu</h2>
        <div class="flex items-center gap-5 text-sm text-muted">
            <span class="flex items-center gap-2"><span class="size-2.5 rounded-full bg-pos"></span>Pemasukan</span>
            <span class="flex items-center gap-2"><span class="size-2.5 rounded-full bg-neg"></span>Pengeluaran</span>
        </div>
    </div>

    <div class="mt-6" role="img" aria-label="Grafik batang pemasukan dan pengeluaran per minggu pada {{ $namaBulan[$bulan] }} {{ $tahun }}. Angka lengkap ada pada tabel di bawahnya.">
        <div class="relative pl-12 sm:pl-14">
            <div class="pointer-events-none absolute inset-y-0 left-0 right-0 flex h-56 flex-col justify-between sm:h-64" aria-hidden="true">
                @foreach([4, 3, 2, 1, 0] as $garis)
                    <div class="flex items-center gap-3">
                        <span class="w-9 shrink-0 text-right text-[0.6875rem] text-muted sm:w-11">{{ $ringkas($langit * $garis / 4) }}</span>
                        <span class="h-px flex-1 {{ $garis === 0 ? 'bg-ink/25' : 'bg-line' }}"></span>
                    </div>
                @endforeach
            </div>

            <div class="relative grid grid-cols-4 gap-3 sm:gap-8">
                @foreach($mingguan as $nomor => $w)
                    <div class="flex flex-col items-center">
                        <div class="flex h-56 w-full items-end justify-center gap-1.5 sm:h-64 sm:gap-2.5">
                            <div class="chart-bar w-1/2 max-w-12 rounded-t-xl bg-pos" style="height: {{ $tinggi((float) $w['pemasukan']) }}%; --d: {{ $nomor * 90 }}ms" title="Pemasukan Rp {{ number_format((float) $w['pemasukan'], 0, ',', '.') }}"></div>
                            <div class="chart-bar w-1/2 max-w-12 rounded-t-xl bg-neg" style="height: {{ $tinggi((float) $w['pengeluaran']) }}%; --d: {{ $nomor * 90 + 60 }}ms" title="Pengeluaran Rp {{ number_format((float) $w['pengeluaran'], 0, ',', '.') }}"></div>
                        </div>
                        <p class="mt-3 text-sm font-medium">Minggu {{ $nomor }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section data-reveal class="card mt-6 p-5 sm:p-6">
    <h2 class="text-lg font-semibold tracking-tight">Rincian mingguan</h2>
    <div class="-mx-5 mt-4 overflow-x-auto sm:-mx-6">
        <table class="table">
            <thead>
                <tr>
                    <th class="pl-5 sm:pl-6">Periode</th>
                    <th class="num">Pemasukan</th>
                    <th class="num">Pengeluaran</th>
                    <th class="num">Selisih kas</th>
                    <th class="num pr-5 sm:pr-6">Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mingguan as $nomor => $data)
                    @php
                        $awal = (($nomor - 1) * 7) + 1;
                        $akhir = $nomor === 4 ? 'akhir bulan' : $nomor * 7;
                        $minggu = (float) $data['selisih_kas'];
                    @endphp
                    <tr>
                        <td class="pl-5 sm:pl-6"><span class="font-medium">Minggu {{ $nomor }}</span> <span class="text-muted">· tanggal {{ $awal }}–{{ $akhir }}</span></td>
                        <td class="num text-pos">Rp {{ number_format($data['pemasukan'], 0, ',', '.') }}</td>
                        <td class="num text-neg">Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</td>
                        <td class="num font-semibold {{ $minggu < 0 ? 'text-neg' : '' }}">Rp {{ number_format($data['selisih_kas'], 0, ',', '.') }}</td>
                        <td class="num pr-5 sm:pr-6">{{ $data['transaksi'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="pl-5 text-left sm:pl-6">Total {{ $namaBulan[$bulan] }}</th>
                    <td class="num">Rp {{ number_format((float) $totalPemasukan, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format((float) $totalPengeluaran, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($selisih, 0, ',', '.') }}</td>
                    <td class="num pr-5 sm:pr-6">{{ $totalTransaksi }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
@endsection
