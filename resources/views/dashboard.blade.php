@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<nav class="quick-actions mb-5 flex flex-wrap gap-2" aria-label="Aksi cepat" data-reveal>
    <a href="{{ route('transactions.index') }}" class="btn btn-primary">
        @include('partials.icon', ['name' => 'plus', 'class' => 'size-4'])
        Catat penjualan
    </a>
    <a href="{{ route('transactions.index', ['jenis' => 'PEMBELIAN']) }}" class="btn btn-ghost">
        @include('partials.icon', ['name' => 'stock', 'class' => 'size-4'])
        Catat restok
    </a>
    <a href="{{ route('products.index') }}" class="btn btn-ghost">
        @include('partials.icon', ['name' => 'search', 'class' => 'size-4'])
        Cek stok barang
    </a>
    @if($isSuperAdmin)
        <a href="{{ route('reports.index') }}" class="btn btn-ghost">
            @include('partials.icon', ['name' => 'report', 'class' => 'size-4'])
            Lihat laporan
        </a>
    @endif
</nav>

@if($isSuperAdmin)
    @php
        $masuk = (float) $totalPemasukan;
        $keluar = (float) $totalPengeluaran;
        $selisih = (float) $selisihKas;
        $arus = $masuk + $keluar;
        $persenMasuk = $arus > 0 ? (int) round($masuk / $arus * 100) : 0;
        $persenKeluar = $arus > 0 ? 100 - $persenMasuk : 0;
    @endphp

    <div class="grid gap-5 lg:grid-flow-dense lg:grid-cols-12">
        <section data-reveal class="card card-glow overflow-hidden p-7 sm:p-9 lg:col-span-7 lg:row-span-2">
            <div class="hero-slices !w-[26%] max-lg:hidden" aria-hidden="true"></div>
            <div class="relative flex h-full min-h-[22rem] flex-col justify-between gap-12">
                <p class="text-sm font-medium text-muted">Selisih kas, seluruh transaksi</p>

                <p class="font-display whitespace-nowrap text-[clamp(2.75rem,5.6vw,5.5rem)] leading-[0.95] {{ $selisih >= 0 ? 'text-brand' : 'text-neg' }}">
                    <span class="mr-1 align-top text-[0.32em] font-bold text-muted">{{ $selisih < 0 ? '−Rp' : 'Rp' }}</span>{{ number_format(abs($selisih), 0, ',', '.') }}
                </p>

                <div class="lg:max-w-[calc(100%-24%)]">
                    <div class="flex h-3 overflow-hidden rounded-full bg-sand-soft" role="img" aria-label="Pemasukan {{ $persenMasuk }} persen, pengeluaran {{ $persenKeluar }} persen dari total arus kas">
                        <div class="bg-pos" style="width: {{ $persenMasuk }}%"></div>
                        <div class="bg-neg" style="width: {{ $persenKeluar }}%"></div>
                    </div>
                    <dl class="mt-5 grid grid-cols-2 gap-6">
                        <div>
                            <dt class="flex items-center gap-2 text-sm text-muted"><span class="size-2 rounded-full bg-pos"></span>Pemasukan · {{ $persenMasuk }}%</dt>
                            <dd class="mt-1 text-xl font-semibold tracking-tight">Rp {{ number_format($masuk, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="flex items-center gap-2 text-sm text-muted"><span class="size-2 rounded-full bg-neg"></span>Pengeluaran · {{ $persenKeluar }}%</dt>
                            <dd class="mt-1 text-xl font-semibold tracking-tight">Rp {{ number_format($keluar, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <section data-reveal class="card p-0 lg:col-span-5">
            <div class="grid h-full grid-cols-2 divide-x divide-line">
                <div class="flex flex-col justify-between gap-8 p-6 sm:p-7">
                    <p class="text-sm font-medium text-muted">Jenis barang</p>
                    <div>
                        <p class="font-display text-6xl leading-none">{{ number_format($totalItem, 0, ',', '.') }}</p>
                        @if($stokMenipis > 0)
                            <a href="{{ route('products.index') }}" class="badge badge-warn mt-4">{{ $stokMenipis }} hampir habis</a>
                        @else
                            <a href="{{ route('products.index') }}" class="link mt-4 inline-block text-sm">Lihat stok</a>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-8 p-6 sm:p-7">
                    <p class="text-sm font-medium text-muted">Anggota</p>
                    <div>
                        <p class="font-display text-6xl leading-none">{{ number_format($totalAnggota, 0, ',', '.') }}</p>
                        <a href="{{ route('members.index') }}" class="link mt-4 inline-block text-sm">Lihat anggota</a>
                    </div>
                </div>
            </div>
        </section>

        <section data-reveal class="card card-accent card-photo p-6 sm:p-7 lg:col-span-5" style="--photo: url('{{ asset('images/tile-macro.jpg') }}')">
            <div class="flex h-full flex-col justify-between gap-8">
                <p class="text-sm font-medium text-white/70">Total simpanan anggota</p>
                <p class="font-display whitespace-nowrap text-[clamp(2rem,3.4vw,3.25rem)] leading-none">
                    <span class="mr-1 align-top text-[0.4em] font-bold text-gold">Rp</span>{{ number_format((float) $totalSimpanan, 0, ',', '.') }}
                </p>
            </div>
        </section>
    </div>
@else
    <div class="grid gap-5 md:grid-flow-dense md:grid-cols-5">
        <section data-reveal class="card card-accent overflow-hidden p-7 sm:p-9 md:col-span-3 md:row-span-2">
            <div class="hero-slices !w-[30%] max-md:hidden" aria-hidden="true"></div>
            <div class="relative flex h-full min-h-[18rem] flex-col justify-between gap-10">
                <p class="text-sm font-medium text-white/70">Transaksi hari ini</p>
                <p class="font-display text-[clamp(5rem,11vw,8.5rem)] leading-[0.85]">{{ $transactions->count() }}</p>
                <div>
                    <a href="{{ route('transactions.index') }}" class="btn btn-dark">
                        Catat transaksi
                        <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span>
                    </a>
                </div>
            </div>
        </section>
        <section data-reveal class="card p-6 sm:p-7 md:col-span-2">
            <div class="flex h-full flex-col justify-between gap-8">
                <p class="text-sm font-medium text-muted">Stok menipis</p>
                <div>
                    <p class="font-display text-6xl leading-none {{ $stokMenipis > 0 ? 'text-warn' : '' }}">{{ $stokMenipis }}</p>
                    <p class="mt-3 text-sm text-muted">Barang dengan sisa 10 atau kurang.</p>
                </div>
            </div>
        </section>
        <section data-reveal class="card p-6 sm:p-7 md:col-span-2">
            <div class="flex h-full flex-col justify-between gap-8">
                <p class="text-sm font-medium text-muted">Jenis barang</p>
                <div>
                    <p class="font-display text-6xl leading-none">{{ $totalItem }}</p>
                    <a href="{{ route('products.index') }}" class="link mt-3 inline-block text-sm">Lihat stok barang</a>
                </div>
            </div>
        </section>
    </div>
@endif

<section data-reveal class="card mt-5 p-3 sm:p-4">
    <div class="flex items-center justify-between gap-4 px-3 pb-2 pt-3 sm:px-4">
        <h2 class="text-lg font-semibold tracking-tight">{{ $isSuperAdmin ? 'Transaksi terkini' : 'Transaksi hari ini' }}</h2>
        <a href="{{ route('transactions.index') }}" class="link text-sm">Semua transaksi</a>
    </div>

    @if($transactions->isEmpty())
        @include('partials.empty', [
            'object' => 'notebook',
            'title' => 'Belum ada transaksi',
            'hint' => $isSuperAdmin ? 'Transaksi yang dicatat kasir akan muncul di sini.' : 'Catat penjualan pertama hari ini dari menu Transaksi.',
        ])
    @else
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Kasir</th>
                        <th class="num">Total</th>
                        @if(! $isSuperAdmin)<th class="center">Struk</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                        @php $penjualan = $trx->jenis === 'PENJUALAN'; @endphp
                        <tr>
                            <td class="whitespace-nowrap text-muted">{{ $trx->tanggal->format('d/m/Y H:i') }}</td>
                            <td><span class="badge {{ $penjualan ? 'badge-pos' : 'badge-neg' }}">{{ $penjualan ? 'Penjualan' : 'Pembelian' }}</span></td>
                            <td>{{ $trx->kasir }}</td>
                            <td class="num font-semibold {{ $penjualan ? 'text-pos' : 'text-neg' }}">{{ $penjualan ? '+' : '−' }} Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            @if(! $isSuperAdmin)
                                <td class="center"><a href="{{ route('transactions.receipt', $trx) }}" class="link">Cetak</a></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
