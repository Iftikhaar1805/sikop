@php
    // Phosphor Light glyphs, copied from @phosphor-icons/core into resources/icons.
    $files = [
        'dashboard' => 'squares-four',
        'transactions' => 'arrows-left-right',
        'stock' => 'package',
        'members' => 'users-three',
        'report' => 'chart-line-up',
        'cashier' => 'user-plus',
        'logout' => 'sign-out',
        'menu' => 'list',
        'search' => 'magnifying-glass',
        'alert' => 'warning',
        'check' => 'check-circle',
        'edit' => 'pencil-simple',
        'inbox' => 'tray',
    ];
    $file = resource_path('icons/' . ($files[$name] ?? $name) . '.svg');
    $svg = is_file($file) ? file_get_contents($file) : '';
    $svg = preg_replace('/^<svg /', '<svg class="' . e($class ?? 'size-5') . ' shrink-0" aria-hidden="true" ', $svg, 1);
@endphp
{!! $svg !!}
