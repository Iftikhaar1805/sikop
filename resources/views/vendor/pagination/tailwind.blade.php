@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3 pt-5 text-sm">
        <p class="text-muted">
            Menampilkan <span class="font-semibold text-ink">{{ $paginator->firstItem() }}</span>&ndash;<span class="font-semibold text-ink">{{ $paginator->lastItem() }}</span>
            dari <span class="font-semibold text-ink">{{ $paginator->total() }}</span> data
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn btn-ghost btn-sm pointer-events-none opacity-40" aria-disabled="true">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-ghost btn-sm">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="btn btn-primary btn-sm min-w-8 px-2">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn btn-ghost btn-sm hidden min-w-8 px-2 sm:inline-flex" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-ghost btn-sm">Berikutnya</a>
            @else
                <span class="btn btn-ghost btn-sm pointer-events-none opacity-40" aria-disabled="true">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
