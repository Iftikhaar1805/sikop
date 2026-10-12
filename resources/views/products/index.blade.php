@extends('layouts.app')

@section('title', 'Stok barang')

@section('content')
<div class="mb-4 grid grid-cols-2 gap-4 {{ $isSuperAdmin ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
    <section data-reveal class="card p-5 sm:p-6">
        <p class="text-sm text-muted">Jenis barang</p>
        <p class="font-display mt-4 text-4xl leading-none sm:text-5xl">{{ number_format($totalJenis, 0, ',', '.') }}</p>
    </section>
    @if($isSuperAdmin)
        <section data-reveal class="card card-accent card-photo order-first col-span-2 p-5 sm:p-6 lg:order-none lg:col-span-1" style="--photo: url('{{ asset('images/tile-macro.jpg') }}')">
            <p class="text-sm font-medium opacity-70">Nilai stok jual</p>
            <p class="font-display mt-4 whitespace-nowrap text-2xl leading-none sm:text-3xl xl:text-4xl"><span class="mr-0.5 align-top text-[0.45em] opacity-60">Rp</span>{{ number_format((float) $totalNilaiStok, 0, ',', '.') }}</p>
        </section>
    @endif
    <section data-reveal class="card p-5 sm:p-6">
        <p class="text-sm text-muted">Stok menipis</p>
        <p class="font-display mt-4 text-4xl leading-none sm:text-5xl {{ $stokMenipis > 0 ? 'text-warn' : '' }}">{{ number_format($stokMenipis, 0, ',', '.') }}</p>
    </section>
    <section data-reveal class="card p-5 sm:p-6 {{ $isSuperAdmin ? '' : 'col-span-2 lg:col-span-1' }}">
        <p class="text-sm text-muted">Stok habis</p>
        <p class="font-display mt-4 text-4xl leading-none sm:text-5xl {{ $stokHabis > 0 ? 'text-neg' : '' }}">{{ number_format($stokHabis, 0, ',', '.') }}</p>
    </section>
</div>

@include('partials.flash', ['errorTitle' => 'Periksa kembali isian formulir:'])

<section data-reveal class="card p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('products.index') }}" method="GET" class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
            <div class="relative min-w-52 flex-1 sm:max-w-sm">
                @include('partials.icon', ['name' => 'search', 'class' => 'pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted'])
                <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama atau kode barang" aria-label="Cari barang" class="input-field pl-9">
            </div>
            <select name="kategori" aria-label="Filter kategori" class="input-field w-auto">
                <option value="Semua">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ $kategori === $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Terapkan</button>
            @if($search || ($kategori && $kategori !== 'Semua'))
                <a href="{{ route('products.index') }}" class="link text-sm">Atur ulang</a>
            @endif
        </form>

        @unless($isSuperAdmin)
            <a href="{{ route('transactions.index', ['jenis' => 'PEMBELIAN']) }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'plus', 'class' => 'size-4'])
                Catat restok
            </a>
        @endunless
    </div>

    @if($isSuperAdmin)
        <details class="group mt-5 rounded-2xl bg-ink/[.035] ring-1 ring-ink/10" {{ $errors->any() ? 'open' : '' }}>
            <summary class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl px-4 py-3.5 text-sm font-semibold transition-colors duration-300 hover:bg-ink/5">
                <span class="flex items-center gap-2">@include('partials.icon', ['name' => 'plus', 'class' => 'size-4']) Tambah barang baru</span>
                <span class="text-muted group-open:hidden">Buka formulir</span>
                <span class="hidden text-muted group-open:inline">Tutup</span>
            </summary>
            <form action="{{ route('products.store') }}" method="POST" class="grid grid-cols-1 gap-4 border-t border-ink/10 px-4 pb-5 pt-5 sm:grid-cols-2 xl:grid-cols-4">
                @csrf
                <div>
                    <label class="field-label" for="np-kode">Kode barang</label>
                    <input id="np-kode" name="kode_barang" value="{{ old('kode_barang') }}" class="input-field" required maxlength="255">
                    @error('kode_barang')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-nama">Nama barang</label>
                    <input id="np-nama" name="nama_barang" value="{{ old('nama_barang') }}" class="input-field" required maxlength="255">
                    @error('nama_barang')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-kategori">Kategori</label>
                    <input id="np-kategori" name="kategori" value="{{ old('kategori') }}" class="input-field" required maxlength="255" list="kategori-list">
                    <datalist id="kategori-list">@foreach($categories as $category)<option value="{{ $category }}">@endforeach</datalist>
                    @error('kategori')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-satuan">Satuan</label>
                    <input id="np-satuan" name="satuan" value="{{ old('satuan', 'Pcs') }}" class="input-field" required maxlength="50">
                    @error('satuan')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-tersedia">Stok tersedia</label>
                    <input id="np-tersedia" type="number" name="stok_tersedia" value="{{ old('stok_tersedia', 0) }}" class="input-field" required min="0" step="1">
                    @error('stok_tersedia')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-tersisa">Stok tersisa</label>
                    <input id="np-tersisa" type="number" name="stok_tersisa" value="{{ old('stok_tersisa', 0) }}" class="input-field" required min="0" step="1">
                    @error('stok_tersisa')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-beli">Harga beli (Rp)</label>
                    <input id="np-beli" type="number" name="harga_beli" value="{{ old('harga_beli', 0) }}" class="input-field" required min="0" step="0.01">
                    @error('harga_beli')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="field-label" for="np-jual">Harga jual (Rp)</label>
                    <input id="np-jual" type="number" name="harga_jual" value="{{ old('harga_jual', 0) }}" class="input-field" required min="0" step="0.01">
                    @error('harga_jual')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="sm:col-span-2 xl:col-span-4"><button type="submit" class="btn btn-primary">Simpan barang <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button></div>
            </form>
        </details>
    @endif

    <div class="-mx-5 mt-5 overflow-x-auto sm:-mx-6">
        <table class="table table-tight">
            <thead>
                <tr>
                    <th class="pl-5 sm:pl-6">Kode</th>
                    <th>Nama barang</th>
                    <th>Kategori</th>
                    <th class="num hidden xl:table-cell">Tersedia</th>
                    <th class="num">Tersisa</th>
                    <th class="num">Harga jual</th>
                    <th class="center pr-5 sm:pr-6">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $sisa = (int) $product->stok_tersisa;
                        $statusClass = $sisa === 0 ? 'badge-neg' : ($sisa <= 10 ? 'badge-warn' : 'badge-pos');
                    @endphp
                    <tr>
                        <td class="pl-5 sm:pl-6"><span class="code-chip">{{ $product->kode_barang }}</span></td>
                        <td class="min-w-36 font-medium">{{ $product->nama_barang }}</td>
                        <td><span class="badge badge-muted">{{ $product->kategori }}</span></td>
                        <td class="num hidden text-muted xl:table-cell">{{ number_format($product->stok_tersedia, 0, ',', '.') }}</td>
                        <td class="num"><span class="badge {{ $statusClass }}">{{ number_format($sisa, 0, ',', '.') }} {{ $product->satuan }}</span></td>
                        <td class="num font-semibold">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                        <td class="center pr-5 sm:pr-6">
                            @if($isSuperAdmin)
                                <button type="button" class="btn btn-ghost btn-sm max-xl:size-8 max-xl:px-0" data-dialog-open="edit-{{ $product->id }}" aria-label="Ubah {{ $product->nama_barang }}" title="Ubah barang">
                                    @include('partials.icon', ['name' => 'edit', 'class' => 'size-4'])
                                    <span class="hidden xl:inline">Ubah</span>
                                </button>

                                <dialog id="edit-{{ $product->id }}" class="modal text-left" aria-labelledby="edit-title-{{ $product->id }}">
                                    <form action="{{ route('products.update', $product) }}" method="POST" class="p-6 sm:p-7">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-5 flex items-start justify-between gap-4">
                                            <div>
                                                <h3 id="edit-title-{{ $product->id }}" class="text-xl font-semibold tracking-tight">Ubah barang</h3>
                                                <p class="mt-0.5 text-sm text-muted">{{ $product->nama_barang }}</p>
                                            </div>
                                            <button type="button" class="btn btn-ghost btn-sm size-8 px-0" data-dialog-close aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'class' => 'size-4'])</button>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-kode">Kode</label>
                                                <input id="e{{ $product->id }}-kode" name="kode_barang" value="{{ $product->kode_barang }}" class="input-field" required maxlength="255">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-nama">Nama barang</label>
                                                <input id="e{{ $product->id }}-nama" name="nama_barang" value="{{ $product->nama_barang }}" class="input-field" required maxlength="255">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-kategori">Kategori</label>
                                                <input id="e{{ $product->id }}-kategori" name="kategori" value="{{ $product->kategori }}" class="input-field" required maxlength="255">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-satuan">Satuan</label>
                                                <input id="e{{ $product->id }}-satuan" name="satuan" value="{{ $product->satuan }}" class="input-field" required maxlength="50">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-tersedia">Stok tersedia</label>
                                                <input id="e{{ $product->id }}-tersedia" type="number" name="stok_tersedia" value="{{ $product->stok_tersedia }}" class="input-field" required min="0" step="1">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-tersisa">Stok tersisa</label>
                                                <input id="e{{ $product->id }}-tersisa" type="number" name="stok_tersisa" value="{{ $product->stok_tersisa }}" class="input-field" required min="0" step="1">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-beli">Harga beli (Rp)</label>
                                                <input id="e{{ $product->id }}-beli" type="number" name="harga_beli" value="{{ $product->harga_beli }}" class="input-field" required min="0" step="0.01">
                                            </div>
                                            <div>
                                                <label class="field-label" for="e{{ $product->id }}-jual">Harga jual (Rp)</label>
                                                <input id="e{{ $product->id }}-jual" type="number" name="harga_jual" value="{{ $product->harga_jual }}" class="input-field" required min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end gap-2">
                                            <button type="button" class="btn btn-ghost" data-dialog-close>Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan perubahan <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button>
                                        </div>
                                    </form>
                                </dialog>
                            @else
                                <span class="text-xs text-muted">Hanya lihat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="!p-0">@include('partials.empty', ['object' => 'box', 'title' => 'Tidak ada barang yang cocok', 'hint' => 'Ubah kata kunci atau atur ulang filter kategori.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</section>
@endsection
