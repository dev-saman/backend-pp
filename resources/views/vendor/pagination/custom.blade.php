@if ($paginator->hasPages())
<style>
.pg-nav {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.pg-nav li a,
.pg-nav li span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    color: #374151;
    background: #fff;
    transition: all .15s;
    cursor: pointer;
    white-space: nowrap;
}
.pg-nav li a:hover {
    background: #C8102E;
    color: #fff;
    border-color: #C8102E;
}
.pg-nav li.active span {
    background: #C8102E;
    color: #fff;
    border-color: #C8102E;
}
.pg-nav li.disabled span,
.pg-nav li.disabled a {
    color: #d1d5db;
    cursor: not-allowed;
    background: #f9fafb;
    pointer-events: none;
}
</style>
<ul class="pg-nav">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <li class="disabled"><span>&#8249;</span></li>
    @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&#8249;</a></li>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="disabled"><span>{{ $element }}</span></li>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&#8250;</a></li>
    @else
        <li class="disabled"><span>&#8250;</span></li>
    @endif
</ul>
@endif
