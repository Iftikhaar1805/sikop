<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#241c17">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Struk #{{ $transaction->id }} | SIKOP</title>
    @vite(['resources/css/app.css'])
    <style>
        .paper { background: #fff; color: #121212; box-shadow: 0 0 0 1px rgba(13,27,20,.08), 0 40px 80px -40px rgba(13,74,51,.45); }
        .paper .muted { color: #6b6b66; }
        .paper .rule { border-color: #c9c8c0; }
        @media print {
            html, body { background: #fff !important; }
            body::after { display: none; }
            .no-print { display: none !important; }
            .paper { box-shadow: none !important; margin: 0 !important; max-width: none !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body class="min-h-dvh px-4 py-10">
    <div class="no-print rise mx-auto mb-6 flex max-w-sm justify-center gap-2">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            Cetak struk
            <span class="btn-icon">@include('partials.icon', ['name' => 'printer', 'class' => 'size-3.5'])</span>
        </button>
        <a href="{{ route('transactions.index') }}" class="btn btn-ghost">
            @include('partials.icon', ['name' => 'arrow-left', 'class' => 'size-4'])
            Kembali
        </a>
    </div>

    <article class="paper rise mx-auto max-w-sm rounded-3xl px-7 py-9" style="--d: 120ms">
        <header class="text-center">
            <img src="{{ asset('images/logo-dark.png') }}" alt="Wibu Studio Corps" width="507" height="323" class="mx-auto h-14 w-auto">
            <h1 class="mt-3 text-lg font-bold leading-none tracking-tight">SIKOP · Koperasi SMK PGRI 3 Tangerang</h1>
            <p class="muted mt-2 text-sm">{{ $transaction->jenis === 'PENJUALAN' ? 'Struk penjualan' : 'Bukti pembelian / restok' }}</p>
        </header>

        <dl class="rule mt-7 space-y-1.5 border-y border-dashed py-4 font-mono text-[0.8125rem]">
            <div class="flex justify-between gap-4"><dt class="muted">No. transaksi</dt><dd>#{{ $transaction->id }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="muted">Tanggal</dt><dd>{{ $transaction->tanggal->locale('id')->translatedFormat('d F Y, H:i') }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="muted">Kasir</dt><dd>{{ $transaction->kasir }}</dd></div>
        </dl>

        <table class="mt-2 w-full text-sm">
            <thead>
                <tr class="muted text-left text-xs">
                    <th class="py-2 font-medium">Barang</th>
                    <th class="py-2 text-right font-medium">Jml</th>
                    <th class="py-2 text-right font-medium">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr class="rule border-t border-dashed align-top">
                        <td class="py-3 pr-3">
                            <span class="font-medium">{{ $item->product->nama_barang }}</span>
                            <span class="muted block font-mono text-xs">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} / {{ $item->product->satuan }}</span>
                        </td>
                        <td class="py-3 text-right font-mono">{{ $item->jumlah }}</td>
                        <td class="whitespace-nowrap py-3 text-right font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="rule mt-2 flex items-baseline justify-between gap-4 border-t border-dashed pt-5">
            <span class="font-medium">Total</span>
            <span class="font-display text-3xl leading-none">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
        </div>

        <p class="muted mt-8 text-center text-sm">Terima kasih sudah berbelanja.</p>
    </article>
</body>
</html>
