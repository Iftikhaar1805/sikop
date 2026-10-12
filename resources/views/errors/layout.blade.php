<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#241c17">
    <title>@yield('title') | SIKOP</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-dvh items-center justify-center px-5 py-12">
    <main class="rise card w-full max-w-lg p-8 text-center sm:p-10">
        <img src="{{ asset('images/obj-' . trim($__env->yieldContent('object', 'box')) . '.png') }}" alt="" width="373" height="373" class="float mx-auto h-auto w-28 select-none">
        <p class="mt-6 text-sm font-semibold text-muted">Kode @yield('code')</p>
        <h1 class="font-display mt-2 text-3xl leading-tight sm:text-4xl">@yield('title')</h1>
        <p class="mx-auto mt-4 max-w-sm text-muted">@yield('message')</p>
        <div class="mt-8 flex flex-wrap justify-center gap-2">
            @yield('actions')
        </div>
    </main>
</body>
</html>
