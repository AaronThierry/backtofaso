@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-3">
        <span class="hidden text-sm text-ink-600 sm:block">
            {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} sur <b class="text-navy-950">{{ $paginator->total() }}</b>
        </span>

        <div class="flex flex-1 items-center justify-between gap-1.5 sm:flex-none sm:justify-end">
            @if ($paginator->onFirstPage())
                <span class="icon-btn cursor-not-allowed opacity-40" aria-disabled="true" aria-label="Page précédente"><x-icon name="back" :size="18" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="icon-btn" aria-label="Page précédente"><x-icon name="back" :size="18" /></a>
            @endif

            <span class="flex items-center gap-1 sm:hidden text-sm font-semibold text-ink-700">Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</span>

            <span class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 text-ink-400">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="flex size-11 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="flex size-11 items-center justify-center rounded-full text-sm font-semibold text-ink-700 transition hover:bg-white hover:shadow-card" aria-label="Page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="icon-btn" aria-label="Page suivante"><x-icon name="chevron" :size="18" /></a>
            @else
                <span class="icon-btn cursor-not-allowed opacity-40" aria-disabled="true" aria-label="Page suivante"><x-icon name="chevron" :size="18" /></span>
            @endif
        </div>
    </nav>
@endif
