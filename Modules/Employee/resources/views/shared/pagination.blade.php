@if($employees->hasPages())
<div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
    <div class="text-sm text-gray-500">
        Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} results
    </div>
    <div class="flex items-center gap-2">
        @if($employees->onFirstPage())
            <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
        @else
            <button class="pagination-btn px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition" data-page="{{ $employees->currentPage() - 1 }}">Previous</button>
        @endif

        <div class="flex items-center gap-1">
            @foreach($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                @if($page == $employees->currentPage())
                    <span class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg">{{ $page }}</span>
                @else
                    <button class="pagination-btn px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition" data-page="{{ $page }}">{{ $page }}</button>
                @endif
            @endforeach
        </div>

        @if($employees->hasMorePages())
            <button class="pagination-btn px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition" data-page="{{ $employees->currentPage() + 1 }}">Next</button>
        @else
            <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
        @endif
    </div>
</div>
@endif