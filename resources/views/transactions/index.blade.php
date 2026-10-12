@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
@include('partials.flash')

@php
    $opsiBarang = function ($terpilih = null) use ($products) {
        $html = '<option value="">Pilih barang</option>';
        foreach ($products as $p) {
            $html .= sprintf(
                '<option value="%d" data-jual="%s" data-beli="%s" data-stok="%d" data-satuan="%s"%s>%s (sisa %d)</option>',
                $p->id,
                (float) $p->harga_jual,
                (float) $p->harga_beli,
                $p->stok_tersisa,
                e($p->satuan),
                (string) $terpilih === (string) $p->id ? ' selected' : '',
                e($p->nama_barang),
                $p->stok_tersisa
            );
        }

        return $html;
    };
@endphp

<div class="grid items-start gap-6 xl:grid-cols-[23rem_minmax(0,1fr)]">
    <section data-reveal class="card p-6 xl:sticky xl:top-28">
        <h2 class="text-lg font-semibold tracking-tight">Transaksi baru</h2>
        <p class="mt-1 text-sm text-muted">Pilih barang dan jumlahnya. Total dihitung otomatis, stok ikut diperbarui setelah disimpan.</p>

        <form id="trx-form" action="{{ route('transactions.store') }}" method="POST" class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-1">
            @csrf
            <div>
                <label class="field-label" for="jenis">Jenis transaksi</label>
                <select id="jenis" name="jenis" class="input-field" required>
                    <option value="PENJUALAN" {{ old('jenis', $jenis) === 'PENJUALAN' ? 'selected' : '' }}>Penjualan (uang masuk)</option>
                    <option value="PEMBELIAN" {{ old('jenis', $jenis) === 'PEMBELIAN' ? 'selected' : '' }}>Pembelian (uang keluar / restok)</option>
                </select>
            </div>

            <div>
                <label class="field-label" for="kasir">Kasir / operator</label>
                @if($isCashier)
                    <input id="kasir" type="text" value="{{ auth()->user()->name }}" class="input-field" disabled>
                @else
                    <input id="kasir" type="text" name="kasir" value="{{ old('kasir', auth()->user()->name) }}" class="input-field" required maxlength="255" placeholder="Nama kasir">
                @endif
            </div>

            <div class="sm:col-span-2 xl:col-span-1">
                <div class="mb-1.5 flex items-center justify-between">
                    <span class="field-label !mb-0">Barang <span class="field-hint">dan jumlah</span></span>
                </div>

                <div id="items-container">
                    <div class="item-row mb-3">
                        <div class="flex gap-2">
                            <select name="items[0][product_id]" aria-label="Barang" class="input-field min-w-0 flex-1" data-item-product required>{!! $opsiBarang(old('items.0.product_id')) !!}</select>
                            <input type="number" name="items[0][jumlah]" aria-label="Jumlah" class="input-field w-20 shrink-0 px-3" min="1" step="1" value="{{ old('items.0.jumlah', 1) }}" data-item-qty required>
                        </div>
                        <p class="mt-1 min-h-4 text-xs text-muted" data-item-info aria-live="polite"></p>
                    </div>
                </div>
                @error('items')<p class="field-error">{{ $message }}</p>@enderror

                <button type="button" id="add-item" class="btn btn-ghost btn-sm mt-1">
                    @include('partials.icon', ['name' => 'plus', 'class' => 'size-3.5'])
                    Tambah barang lain
                </button>
            </div>

            <div class="rounded-2xl bg-sunken p-4 ring-1 ring-line sm:col-span-2 xl:col-span-1">
                <div class="flex items-baseline justify-between gap-4">
                    <span class="text-sm font-medium text-muted">Total</span>
                    <span id="trx-total" class="font-display text-3xl leading-none" aria-live="polite">Rp 0</span>
                </div>

                <div id="trx-bayar-box" class="mt-4 border-t border-line pt-4">
                    <label class="field-label" for="trx-bayar">Uang diterima <span class="field-hint">(opsional, untuk hitung kembalian)</span></label>
                    <input id="trx-bayar" type="number" class="input-field" min="0" step="500" inputmode="numeric" placeholder="Contoh: 50000" data-rupiah>
                    <div class="mt-3 flex items-baseline justify-between gap-4">
                        <span id="trx-kembali-label" class="text-sm font-medium text-muted">Kembalian</span>
                        <span id="trx-kembali" class="text-xl font-semibold tracking-tight" aria-live="polite">Rp 0</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary h-11 w-full sm:col-span-2 xl:col-span-1">Simpan transaksi <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button>
        </form>
    </section>

    <section data-reveal class="card min-w-0 p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold tracking-tight">Riwayat transaksi</h2>
            <form action="{{ route('transactions.index') }}" method="GET" class="flex flex-wrap gap-2">
                <select name="jenis" aria-label="Filter jenis transaksi" class="input-field h-9 w-auto text-sm" onchange="this.form.submit()">
                    <option value="SEMUA">Semua jenis</option>
                    <option value="PENJUALAN" {{ $jenis == 'PENJUALAN' ? 'selected' : '' }}>Penjualan</option>
                    <option value="PEMBELIAN" {{ $jenis == 'PEMBELIAN' ? 'selected' : '' }}>Pembelian</option>
                </select>
                @if(! $isCashier)
                    <select name="minggu" aria-label="Filter minggu" class="input-field h-9 w-auto text-sm" onchange="this.form.submit()">
                        <option value="">Semua minggu</option>
                        @for($week = 1; $week <= 4; $week++)
                            <option value="{{ $week }}" {{ (string) $minggu === (string) $week ? 'selected' : '' }}>Minggu {{ $week }}</option>
                        @endfor
                    </select>
                @endif
                <noscript><button type="submit" class="btn btn-ghost btn-sm">Terapkan</button></noscript>
            </form>
        </div>

        <div class="-mx-5 mt-4 overflow-x-auto sm:-mx-6">
            <table class="table table-tight">
                <thead>
                    <tr>
                        <th class="pl-5 sm:pl-6">Tanggal</th>
                        <th>Jenis</th>
                        <th>Kasir</th>
                        <th>Barang</th>
                        <th class="num">Total</th>
                        <th class="center relative pr-5 sm:pr-6"><span class="sr-only">Struk</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        @php $penjualan = $trx->jenis === 'PENJUALAN'; @endphp
                        <tr>
                            <td class="whitespace-nowrap pl-5 sm:pl-6">{{ $trx->tanggal->format('d/m/Y H:i') }}</td>
                            <td><span class="badge {{ $penjualan ? 'badge-pos' : 'badge-neg' }}">{{ $penjualan ? 'Penjualan' : 'Pembelian' }}</span></td>
                            <td>{{ $trx->kasir }}</td>
                            <td class="text-[0.8125rem] text-muted">
                                @foreach($trx->items as $item)
                                    <span class="block">{{ $item->product->nama_barang }} <span class="text-ink">&times;{{ $item->jumlah }}</span></span>
                                @endforeach
                            </td>
                            <td class="num font-semibold {{ $penjualan ? 'text-pos' : 'text-neg' }}">{{ $penjualan ? '+' : '−' }} Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="center whitespace-nowrap pr-5 sm:pr-6">
                                <a href="{{ route('transactions.receipt', $trx) }}" class="btn btn-ghost btn-sm" title="Cetak struk transaksi ini">
                                    @include('partials.icon', ['name' => 'printer', 'class' => 'size-4'])
                                    Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="!p-0">@include('partials.empty', ['object' => 'notebook', 'title' => 'Belum ada transaksi', 'hint' => 'Tidak ada transaksi yang cocok dengan filter ini.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $transactions->links() }}
    </section>
</div>

<template id="item-row-template">
    <div class="item-row mb-3">
        <div class="flex gap-2">
            <select aria-label="Barang" class="input-field min-w-0 flex-1" data-item-product required>{!! $opsiBarang() !!}</select>
            <input type="number" aria-label="Jumlah" class="input-field w-20 shrink-0 px-3" min="1" step="1" value="1" data-item-qty required>
            <button type="button" class="btn btn-ghost size-[2.75rem] shrink-0 px-0 text-neg" data-item-remove aria-label="Hapus barang ini" title="Hapus barang ini">
                @include('partials.icon', ['name' => 'x', 'class' => 'size-4'])
            </button>
        </div>
        <p class="mt-1 min-h-4 text-xs text-muted" data-item-info aria-live="polite"></p>
    </div>
</template>

@push('scripts')
<script>
    (function () {
        var form = document.getElementById('trx-form');
        var container = document.getElementById('items-container');
        var template = document.getElementById('item-row-template');
        var jenis = document.getElementById('jenis');
        var totalEl = document.getElementById('trx-total');
        var bayarBox = document.getElementById('trx-bayar-box');
        var bayar = document.getElementById('trx-bayar');
        var kembali = document.getElementById('trx-kembali');
        var kembaliLabel = document.getElementById('trx-kembali-label');
        var nextIndex = 1;

        function rp(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

        function hitung() {
            var penjualan = jenis.value === 'PENJUALAN';
            var total = 0;

            container.querySelectorAll('.item-row').forEach(function (row) {
                var opt = row.querySelector('[data-item-product]').selectedOptions[0];
                var qtyInput = row.querySelector('[data-item-qty]');
                var info = row.querySelector('[data-item-info]');
                var qty = parseInt(qtyInput.value, 10) || 0;
                info.classList.remove('text-neg', 'font-semibold');

                if (!opt || !opt.value) { info.textContent = ''; return; }

                var harga = parseFloat(penjualan ? opt.dataset.jual : opt.dataset.beli) || 0;
                var stok = parseInt(opt.dataset.stok, 10) || 0;
                var sub = harga * qty;
                total += sub;

                if (penjualan && qty > stok) {
                    info.textContent = 'Stok tinggal ' + stok + ' ' + opt.dataset.satuan + '. Kurangi jumlahnya.';
                    info.classList.add('text-neg', 'font-semibold');
                } else {
                    info.textContent = rp(harga) + ' × ' + qty + ' = ' + rp(sub);
                }
            });

            totalEl.textContent = rp(total);
            bayarBox.hidden = !penjualan;

            var dibayar = parseFloat(bayar.value) || 0;
            var selisih = dibayar - total;
            kembali.classList.remove('text-neg');
            if (!dibayar) {
                kembaliLabel.textContent = 'Kembalian';
                kembali.textContent = 'Rp 0';
            } else if (selisih < 0) {
                kembaliLabel.textContent = 'Uang kurang';
                kembali.textContent = rp(-selisih);
                kembali.classList.add('text-neg');
            } else {
                kembaliLabel.textContent = 'Kembalian';
                kembali.textContent = rp(selisih);
            }
        }

        document.getElementById('add-item').addEventListener('click', function () {
            var row = template.content.firstElementChild.cloneNode(true);
            row.querySelector('[data-item-product]').name = 'items[' + nextIndex + '][product_id]';
            row.querySelector('[data-item-qty]').name = 'items[' + nextIndex + '][jumlah]';
            nextIndex++;
            container.appendChild(row);
            row.querySelector('[data-item-product]').focus();
            hitung();
        });

        container.addEventListener('click', function (e) {
            var remove = e.target.closest('[data-item-remove]');
            if (!remove) return;
            remove.closest('.item-row').remove();
            hitung();
        });

        form.addEventListener('input', hitung);
        form.addEventListener('change', hitung);
        hitung();
    })();
</script>
@endpush
@endsection
