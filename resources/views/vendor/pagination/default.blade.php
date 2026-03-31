<nav class="pagination-nav">
    <div class="pagination-info" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--gray-600, #4b5563);">
            <span>Show</span>
            <select onchange="changePerPage(this.value)" style="padding: 4px 8px; border: 1px solid var(--gray-200, #e5e7eb); border-radius: 6px; font-size: 13px; background: white; color: var(--gray-700, #374151); cursor: pointer; min-width: 60px;">
                @foreach([15, 25, 50, 100, 'all'] as $option)
                    <option value="{{ $option }}" {{ request('per_page', $paginator->perPage()) == $option ? 'selected' : '' }}>
                        {{ $option === 'all' ? 'All' : $option }}
                    </option>
                @endforeach
            </select>
            <span>entries</span>
        </div>
        @if($paginator->total())
            <p>
                Showing <span class="font-medium">{{ $paginator->firstItem() }}</span>
                to <span class="font-medium">{{ $paginator->lastItem() }}</span>
                of <span class="font-medium">{{ $paginator->total() }}</span> results
            </p>
        @endif
    </div>

    @if ($paginator->hasPages())
        <div class="pagination-controls">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn pagination-btn-disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev" aria-label="@lang('pagination.previous')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next" aria-label="@lang('pagination.next')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @else
                <span class="pagination-btn pagination-btn-disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
            @endif
        </div>

        <div class="pagination-numbers">
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="pagination-dots" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-btn pagination-btn-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
    @endif
</nav>

<script>
function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
