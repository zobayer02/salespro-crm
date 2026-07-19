@if ($paginator->hasPages())
    <nav class="salespro-pagination" role="navigation" aria-label="Pagination Navigation">
        <p class="salespro-pagination__summary">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            to
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            results
        </p>

        <div class="salespro-pagination__controls">
            @if ($paginator->onFirstPage())
                <span class="salespro-pagination__link is-disabled" aria-disabled="true" aria-label="Previous page">&lsaquo;</span>
            @else
                <a class="salespro-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&lsaquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="salespro-pagination__ellipsis" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="salespro-pagination__link is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="salespro-pagination__link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="salespro-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&rsaquo;</a>
            @else
                <span class="salespro-pagination__link is-disabled" aria-disabled="true" aria-label="Next page">&rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
