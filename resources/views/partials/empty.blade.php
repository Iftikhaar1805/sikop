@php $object = $object ?? 'box'; @endphp
<div class="flex flex-col items-center gap-3 px-6 py-12 text-center">
    <img src="{{ asset('images/obj-' . $object . '.png') }}" alt="" width="373" height="373" loading="lazy" decoding="async" class="float h-auto w-28 select-none sm:w-32">
    <p class="text-xl font-semibold tracking-tight">{{ $title }}</p>
    @isset($hint)<p class="max-w-sm text-sm text-muted">{{ $hint }}</p>@endisset
</div>
