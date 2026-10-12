@extends('layouts.app')

@section('title', 'Pengguna kasir')

@section('content')
@include('partials.flash')

<div class="grid items-start gap-6 xl:grid-cols-[22rem_minmax(0,1fr)]">
    <section data-reveal class="card p-6">
        <h2 class="text-lg font-semibold tracking-tight">Buat akun kasir</h2>
        <p class="mt-1 text-sm text-muted">Kasir hanya bisa mengelola transaksi hari ini dan mencetak struk.</p>

        <form action="{{ route('users.store') }}" method="POST" class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
            @csrf
            <div>
                <label class="field-label" for="u-nama">Nama</label>
                <input id="u-nama" name="name" value="{{ old('name') }}" class="input-field" required maxlength="255">
            </div>
            <div>
                <label class="field-label" for="u-email">Email</label>
                <input id="u-email" type="email" name="email" value="{{ old('email') }}" class="input-field" required maxlength="255">
            </div>
            <div>
                <label class="field-label" for="u-pass">Kata sandi <span class="field-hint">(minimal 8 karakter)</span></label>
                <input id="u-pass" type="password" name="password" class="input-field" required minlength="8" autocomplete="new-password">
            </div>
            <div>
                <label class="field-label" for="u-pass2">Ulangi kata sandi</label>
                <input id="u-pass2" type="password" name="password_confirmation" class="input-field" required minlength="8" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary h-11 w-full sm:col-span-2 xl:col-span-1">Buat akun <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></button>
        </form>
    </section>

    <section data-reveal class="card min-w-0 p-5 sm:p-6">
        <h2 class="text-lg font-semibold tracking-tight">Daftar akun kasir</h2>
        <div class="-mx-5 mt-4 overflow-x-auto sm:-mx-6">
            <table class="table">
                <thead>
                    <tr><th class="pl-5 sm:pl-6">Nama</th><th>Email</th><th class="pr-5 sm:pr-6">Peran</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="pl-5 font-medium sm:pl-6">{{ $user->name }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="pr-5 sm:pr-6"><span class="badge badge-muted">Kasir</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="!p-0">@include('partials.empty', ['object' => 'basket', 'title' => 'Belum ada akun kasir', 'hint' => 'Buat akun pertama dari formulir di samping.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </section>
</div>
@endsection
