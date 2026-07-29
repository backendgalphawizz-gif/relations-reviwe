@if ($paginator->hasPages())
    <div class="admin-dash-pagination">
        <div class="admin-dash-pagination__info">
            Showing
            <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
            to
            <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            results
        </div>

        <ul class="admin-dash-pagination__list">
            @if ($paginator->onFirstPage())
                <li class="is-disabled"><span>&lsaquo;</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&lsaquo;</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="is-disabled"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="is-active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&rsaquo;</a></li>
            @else
                <li class="is-disabled"><span>&rsaquo;</span></li>
            @endif
        </ul>
    </div>
@endif
