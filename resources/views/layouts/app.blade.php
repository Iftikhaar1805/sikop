<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#241c17">
    <title>@yield('title', 'SIKOP') | SIKOP</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script>document.documentElement.classList.add('js');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-dvh flex-col">
@php
    $user = auth()->user();
    $nav = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'admin' => false],
        ['route' => 'transactions.index', 'match' => 'transactions.*', 'label' => 'Transaksi', 'icon' => 'transactions', 'admin' => false],
        ['route' => 'products.index', 'match' => 'products.*', 'label' => 'Stok', 'icon' => 'stock', 'admin' => false],
        ['route' => 'members.index', 'match' => 'members.*', 'label' => 'Anggota', 'icon' => 'members', 'admin' => true],
        ['route' => 'reports.index', 'match' => 'reports.*', 'label' => 'Laporan', 'icon' => 'report', 'admin' => true],
        ['route' => 'users.index', 'match' => 'users.*', 'label' => 'Akun kasir', 'icon' => 'cashier', 'admin' => true],
    ];
    $nav = array_values(array_filter($nav, fn ($item) => ! $item['admin'] || $user->isSuperAdmin()));
@endphp

<a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-accent focus:px-4 focus:py-2 focus:text-accent-ink">Lewati ke konten</a>

{{-- Floating glass pill --}}
<header class="fixed inset-x-0 top-4 z-40 flex justify-center px-3">
    <div class="nav-pill flex w-full max-w-max items-center gap-1 rounded-full p-1.5 pr-2">
        <a href="{{ route('dashboard') }}" class="flex h-10 shrink-0 items-center rounded-full px-3 transition-colors duration-300 hover:bg-white/5" aria-label="SIKOP, ke dashboard">
            <img src="{{ asset('images/logo.png') }}" alt="Wibu Studio Corps" width="507" height="323" class="h-8 w-auto">
        </a>

        <span class="mx-1 hidden h-5 w-px bg-white/10 lg:block"></span>

        <nav class="hidden items-center gap-0.5 lg:flex" aria-label="Menu utama">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}" class="nav-link" @if(request()->routeIs($item['match'])) aria-current="page" @endif>
                    @include('partials.icon', ['name' => $item['icon'], 'class' => 'size-[1.125rem]'])
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <span class="mx-1 hidden h-5 w-px bg-white/10 lg:block"></span>

        <div class="ml-2 hidden items-center gap-2.5 lg:flex">
            <span class="grid size-8 place-items-center rounded-full bg-white/10 text-xs font-semibold">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            <span class="hidden max-w-32 truncate text-sm leading-tight xl:block">
                {{ $user->name }}
                <span class="block text-xs text-muted">{{ $user->isSuperAdmin() ? 'Admin' : 'Kasir' }}</span>
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
            @csrf
            <button type="submit" class="nav-link size-10 justify-center px-0" aria-label="Keluar" title="Keluar">
                @include('partials.icon', ['name' => 'logout', 'class' => 'size-[1.125rem]'])
            </button>
        </form>

        <button type="button" class="burger relative ml-auto size-10 rounded-full bg-white/5 lg:hidden" aria-label="Buka menu" aria-expanded="false" aria-controls="menu-overlay" data-menu-toggle>
            <i></i><i></i>
        </button>
    </div>
</header>

{{-- Mobile menu --}}
<div id="menu-overlay" class="fixed inset-0 z-30 flex flex-col justify-center bg-brand-deep/95 px-8 backdrop-blur-2xl lg:hidden" aria-hidden="true">
    <nav class="flex flex-col gap-1" aria-label="Menu seluler">
        @foreach($nav as $i => $item)
            <a href="{{ route($item['route']) }}" style="--i: {{ $i }}" class="menu-item font-display flex items-center justify-between border-b border-white/10 py-4 text-3xl {{ request()->routeIs($item['match']) ? 'text-gold' : 'text-white' }}" tabindex="-1">
                {{ $item['label'] }}
                @include('partials.icon', ['name' => $item['icon'], 'class' => 'size-6 text-white/50'])
            </a>
        @endforeach
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="menu-item mt-8" style="--i: {{ count($nav) }}">
        @csrf
        <p class="mb-3 text-sm text-white/60">Masuk sebagai {{ $user->name }} · {{ $user->isSuperAdmin() ? 'Admin' : 'Kasir' }}</p>
        <button type="submit" class="btn btn-ghost" tabindex="-1">
            @include('partials.icon', ['name' => 'logout', 'class' => 'size-4'])
            Keluar
        </button>
    </form>
</div>

<main id="konten" class="mx-auto w-full max-w-[88rem] flex-1 px-4 pb-16 pt-28 sm:px-8 sm:pt-32">
    <div class="mb-8 flex flex-wrap items-end justify-between gap-x-6 gap-y-2 sm:mb-10" data-reveal>
        <h1 class="font-display flex items-center gap-[0.35em] text-[clamp(1.9rem,4vw,3.4rem)] leading-[1.05]"><span class="slices" aria-hidden="true"><i></i><i></i><i></i></span>@yield('title', 'SIKOP')</h1>
        <p class="pb-1 text-sm text-muted">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</p>
    </div>

    @yield('content')
</main>

<footer class="mx-auto w-full max-w-[88rem] px-4 pb-10 sm:px-8">
    <div class="flex flex-wrap items-center justify-between gap-x-8 gap-y-2 border-t border-line pt-6 text-[0.8125rem] text-muted">
        <p><span class="font-semibold text-ink">SIKOP</span> · Sistem Informasi Penjualan Koperasi Sekolah, SMK PGRI 3 Tangerang</p>
        <p>Dikembangkan oleh Wibu Studio Corps</p>
    </div>
</footer>

<script>
    (function () {
        var overlay = document.getElementById('menu-overlay');
        var burger = document.querySelector('[data-menu-toggle]');

        function setMenu(open) {
            overlay.classList.toggle('is-open', open);
            overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
            burger.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
            overlay.querySelectorAll('a, button').forEach(function (el) { el.tabIndex = open ? 0 : -1; });
            document.body.style.overflow = open ? 'hidden' : '';
        }

        burger.addEventListener('click', function () { setMenu(!overlay.classList.contains('is-open')); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setMenu(false);
        });

        // Dialogs
        document.addEventListener('click', function (e) {
            var opener = e.target.closest('[data-dialog-open]');
            if (opener) {
                var dlg = document.getElementById(opener.getAttribute('data-dialog-open'));
                if (dlg && dlg.showModal) dlg.showModal();
                return;
            }
            if (e.target.closest('[data-dialog-close]')) {
                e.target.closest('dialog').close();
                return;
            }
            if (e.target.tagName === 'DIALOG' && e.target.classList.contains('modal')) {
                var r = e.target.getBoundingClientRect();
                if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) e.target.close();
            }
        });

        // Scroll reveal: staggered by order of appearance
        var items = document.querySelectorAll('[data-reveal]');
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!('IntersectionObserver' in window) || reduce) {
            items.forEach(function (el) { el.classList.add('is-in'); });
        } else {
            var batch = 0, timer = null;
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    entry.target.style.setProperty('--d', Math.min(batch++, 6) * 90 + 'ms');
                    entry.target.classList.add('is-in');
                    io.unobserve(entry.target);
                });
                clearTimeout(timer);
                timer = setTimeout(function () { batch = 0; }, 120);
            }, { threshold: 0.06, rootMargin: '0px 0px -4% 0px' });
            items.forEach(function (el) { io.observe(el); });
        }

        // Cursor spotlight on cards
        if (window.matchMedia('(hover: hover)').matches) {
            document.addEventListener('pointermove', function (e) {
                var card = e.target.closest && e.target.closest('.card');
                if (!card) return;
                var r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top) + 'px');
            }, { passive: true });
        }
    })();
</script>

@stack('scripts')
</body>
</html>
