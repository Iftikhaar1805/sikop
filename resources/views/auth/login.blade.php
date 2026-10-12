<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#241c17">
    <title>Masuk | SIKOP</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-fresh-form class="min-h-dvh lg:grid lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]">
    <aside class="brand-panel relative flex flex-col justify-between gap-20 overflow-hidden px-8 py-10 sm:px-14 lg:min-h-dvh lg:py-14" style="--hero: url('{{ asset('images/login-hero.jpg') }}')">
        <div class="brand-slices" aria-hidden="true"></div>
        <div class="brand-photo" aria-hidden="true"></div>
        <div class="dust" aria-hidden="true"></div>
        <img src="{{ asset('images/logo.png') }}" alt="Wibu Studio Corps" width="507" height="323" class="rise h-28 w-auto self-start sm:h-32">

        <div class="max-w-2xl">
            <h2 class="rise font-display text-[clamp(2.5rem,5.4vw,5rem)] leading-[0.95] text-white" style="--d: 120ms">Stok, kas, dan simpanan dalam satu tempat.</h2>
            <p class="rise mt-6 max-w-[19rem] text-lg text-white/70" style="--d: 240ms">Catat penjualan harian, pantau stok yang menipis, dan lihat selisih kas bulan ini.</p>
        </div>

        <p class="rise hidden text-sm text-white/55 lg:block" style="--d: 360ms">SIKOP · Koperasi SMK PGRI 3 Tangerang</p>
    </aside>

    <main class="flex items-center justify-center px-5 py-12 sm:px-12">
        <div class="rise card w-full max-w-md p-8 sm:p-10" style="--d: 200ms">
            <h1 class="font-display flex items-center gap-[0.35em] text-4xl leading-none"><span class="slices" aria-hidden="true"><i></i><i></i><i></i></span>Masuk</h1>
            <p class="mt-3 text-muted">Masukkan email dan kata sandi dari Admin koperasi.</p>

            @if($errors->any())
                <div class="alert alert-error mt-6" role="alert">
                    @include('partials.icon', ['name' => 'info', 'class' => 'size-5 mt-px'])
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="input-field" autocomplete="username" required autofocus>
                </div>
                <div>
                    <label for="password" class="field-label">Kata sandi</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" class="input-field pr-12" autocomplete="current-password" required>
                        <button type="button" class="pw-toggle" data-password-toggle="password" aria-pressed="false" aria-label="Tampilkan kata sandi">
                            <span class="pw-show">@include('partials.icon', ['name' => 'eye', 'class' => 'size-5'])</span>
                            <span class="pw-hide">@include('partials.icon', ['name' => 'eye-slash', 'class' => 'size-5'])</span>
                        </button>
                    </div>
                </div>
                <label class="flex items-center gap-2.5 text-sm text-muted">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-[#372c25]">
                    Ingat saya di perangkat ini
                </label>
                <button type="submit" class="btn btn-primary h-12 w-full justify-between pl-6 pr-2">
                    Masuk
                    <span class="btn-icon !mr-0 size-8">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-4'])</span>
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-muted">Lupa kata sandi? Hubungi Admin koperasi untuk dibuatkan akun baru.</p>
        </div>
    </main>
</body>
</html>
